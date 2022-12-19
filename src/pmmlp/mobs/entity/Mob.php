<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity;

use pmmlp\mobs\entity\ai\control\LookControl;
use pmmlp\mobs\entity\ai\goal\GoalSelector;
use pmmlp\mobs\entity\ai\navigation\PathNavigation;
use pmmlp\mobs\entity\ai\pathfinder\evaluator\NodeEvaluator;
use pmmlp\mobs\entity\ai\pathfinder\PathFinder;
use pmmlp\mobs\entity\animal\Animal;
use pmmlp\mobs\util\MobsConfig;
use pocketmine\block\Liquid;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;

abstract class Mob extends Living {
    protected bool $persistenceRequired = false;

    protected bool $leashed = false;
    protected ?int $leashedToEntityId = null;

    protected bool $isHeadInsideOfLiquid = false;
    protected bool $isBodyInsideOfLiquid = false;

    protected int $noActionTime = 0;

    protected GoalSelector $goalSelector;
    protected GoalSelector $targetSelector;

    protected PathFinder $pathFinder;
    protected PathNavigation $navigation;
    protected NodeEvaluator $nodeEvaluator;

    protected LookControl $lookControl;

    protected ?int $lastDamagerId = null;
    protected int $lastDamageTick = 0;

    protected float $headYaw = 0.0;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);

        $this->lookControl = new LookControl($this);

        $this->goalSelector = new GoalSelector($this);
        $this->targetSelector = new GoalSelector($this);

        $this->nodeEvaluator = $this->initNodeEvaluator();
        $this->pathFinder = new PathFinder($this);
        $this->navigation = $this->initPathNavigation();

        $this->registerGoals();
    }

    abstract protected function initNodeEvaluator(): NodeEvaluator;
    abstract protected function initPathNavigation(): PathNavigation;

    /**
     * @internal
     */
    public function getPathFinder(): PathFinder{
        return $this->pathFinder;
    }

    public function getNodeEvaluator(): NodeEvaluator{
        return $this->nodeEvaluator;
    }

    public function getNavigation(): PathNavigation{
        return $this->navigation;
    }

    abstract protected function registerGoals(): void;

    abstract public function getPickResult(): Item;

    public function getDespawnDistance(): int {
        return 128;
    }

    public function getNoDespawnDistance(): int {
        return 32;
    }

    public function shouldDespawnInPeaceful(): bool {
        return false;
    }

    public function isPersistenceRequired(): bool{
        return $this->persistenceRequired;
    }

    public function requiresCustomPersistence(): bool {
        return false;
    }

    public function setPersistenceRequired(bool $persistenceRequired = true): void{
        $this->persistenceRequired = $persistenceRequired;
    }

    public function removeWhenFarAway(): bool {
        return true;
    }

    public function checkDespawn(): void {
        $world = $this->getWorld();
        if($world->getDifficulty() === World::DIFFICULTY_PEACEFUL && $this->shouldDespawnInPeaceful()) {
            $this->flagForDespawn();
        } elseif(!$this->isPersistenceRequired() && !$this->requiresCustomPersistence()) {
            $player = $world->getNearestEntity($this->location, $this->getDespawnDistance(), Player::class);
            if($player === null && $this->removeWhenFarAway()) {
                $this->flagForDespawn();
            }
        } else {
            $this->noActionTime = 0;
        }
    }

    public function getLookControl(): LookControl{
        return $this->lookControl;
    }

    public function getMaxFallDistance(): int {
        if($this->getTargetEntityId() === null) {
            return 3;
        }
        $fallDistance = (int)($this->getHealth() - $this->getMaxHealth() * 0.33);
        $fallDistance -= (3 - $this->getWorld()->getDifficulty()) * 4;
        return max(0, $fallDistance) + 3;
    }

    public function getMaxJumpHeight(): float {
        return 1.125;
    }

    public function getJumpVelocity(): float{
        return parent::getJumpVelocity() + $this->drag + $this->gravity;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        $this->noActionTime += $tickDiff;
        return parent::entityBaseTick($tickDiff);
    }

    public function onUpdate(int $currentTick): bool{
        $this->navigation->internalTick();
        $this->goalSelector->tick();
        $this->targetSelector->tick();
        $this->lookControl->tick();

        if($currentTick % 20 === 0) {
            $this->checkDespawn();
        }
        if($this->isBodyInsideOfLiquid()) {
            $this->motion->x /= 1.2;
            $this->motion->z /= 1.2;
        }

        if($this->canBePushed()) {
            foreach($this->getWorld()->getCollidingEntities($this->getBoundingBox()->expandedCopy(0.2, 0, 0.2), $this) as $entity){
                if($entity instanceof Mob && $entity->canBePushed()) {
                    $this->applyEntityCollision($entity);
                }
            }
        }

        if(MobsConfig::DEBUG) {
            $nametag = ["§6Goals§7:"];
            foreach($this->goalSelector->getAvailableGoals() as $goal) {
                if($goal->isRunning()) {
                    $nametag[] = (new \ReflectionClass($goal))->getShortName();
                }
            }
            $nametag[] = "§6Collision§7: ".(int)$this->isCollidedHorizontally;
            $nametag[] = "§6Liquid§7: ".(int)$this->isBodyInsideOfLiquid();

            $this->setNameTag(implode("\n§r§7", $nametag));
            $this->setNameTagVisible();
            $this->setNameTagAlwaysVisible();
        }

        $update = parent::onUpdate($currentTick);
        $this->updateLiquidState();
        return $update;
    }

    public function attack(EntityDamageEvent $source): void{
        parent::attack($source);
        if(!$source->isCancelled()) {
            if($source instanceof EntityDamageByEntityEvent) {
                $this->lastDamagerId = $source->getDamager()?->getId();
                $this->lastDamageTick = Server::getInstance()->getTick();
            }

            if(!$this->navigation->isDone()) {
                $this->navigation->recalculatePath();
            }
        }
    }

    public function getLastDamager(): ?Entity {
        if($this->lastDamagerId === null || $this->lastDamageTick + 100 < Server::getInstance()->getTick()) {
            $this->lastDamagerId = null;
            return null;
        }
        return Server::getInstance()->getWorldManager()->findEntity($this->lastDamagerId);
    }

    public function canStandOnLiquid(Liquid $liquid): bool {
        return false;
    }

    public function canSaveWithChunk(): bool{
        $this->checkDespawn();
        return $this->isFlaggedForDespawn();
    }

    protected function updateLiquidState(): void {
        $world = $this->getWorld();
        $bb = $this->getBoundingBox();

        $minX = (int) floor($bb->minX - 1);
        $minY = (int) floor($bb->minY - 1);
        $minZ = (int) floor($bb->minZ - 1);
        $maxX = (int) floor($bb->maxX + 1);
        $maxY = (int) floor($bb->maxY + 1);
        $maxZ = (int) floor($bb->maxZ + 1);

        $this->isBodyInsideOfLiquid = false;
        for($z = $minZ; $z <= $maxZ; ++$z){
            for($x = $minX; $x <= $maxX; ++$x){
                for($y = $minY; $y <= $maxY; ++$y){
                    if($world->getBlockAt($x, $y, $z) instanceof Liquid) {
                        $this->isBodyInsideOfLiquid = true;
                        break 3;
                    }
                }
            }
        }
        $this->isHeadInsideOfLiquid = ($world->getBlockAt((int)floor($this->location->x), (int)floor($this->location->y + $this->getEyeHeight()), (int)floor($this->location->z)) instanceof Liquid);
    }

    public function isHeadInsideOfLiquid(): bool {
        return $this->isHeadInsideOfLiquid;
    }

    public function isBodyInsideOfLiquid(): bool {
        return $this->isBodyInsideOfLiquid;
    }

    public function getYSize(): float{
        return $this->ySize;
    }

    public function getMovementRotation(Vector3 $target): float {
        $xDist = $target->x - $this->location->x;
        $zDist = $target->z - $this->location->z;
        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if($yaw < 0){
            $yaw += 360.0;
        }
        return $yaw;
    }

    public function getMovementDirection(Vector3|float $targetOrYaw): Vector3 {
        $yaw = is_numeric($targetOrYaw) ? $targetOrYaw : $this->getMovementRotation($targetOrYaw);
        $x = -1 * sin(deg2rad($yaw));
        $z = cos(deg2rad($yaw));
        return (new Vector3($x, 0, $z))->normalize();
    }

    public function setHeadYaw(float $headYaw): void{
        $this->headYaw = $headYaw;
    }

    public function getHeadYaw(): float{
        return $this->headYaw;
    }

    protected function broadcastMovement(bool $teleport = false) : void{
        if($teleport){
            foreach($this->hasSpawned as $player){
                $this->despawnFrom($player);
                $this->spawnTo($player);
            }
        }else{
            $this->server->broadcastPackets($this->hasSpawned, [MoveActorAbsolutePacket::create(
                $this->id,
                $this->getOffsetPosition($this->location),
                $this->location->pitch,
                $this->location->yaw,
                $this->headYaw,
                (($this->onGround ? MoveActorAbsolutePacket::FLAG_GROUND : 0))
            )]);
        }
    }

    public function canBePushed(): bool {
        return true;
    }

    public function applyEntityCollision(Entity $entity) : void{
        if(!($entity instanceof Player && $entity->isSpectator())){
            $d0 = $entity->getLocation()->x - $this->location->x;
            $d1 =  $entity->getLocation()->z - $this->location->z;
            $d2 = abs(max($d0, $d1));

            if($d2 > 0){
                $d2 = sqrt($d2);
                $d0 /= $d2;
                $d1 /= $d2;
                $d3 = min(1, 1 / $d2);

                $entity->setMotion($entity->getMotion()->add($d0 * $d3 * 0.05, 0, $d1 * $d3 * 0.05));
            }
        }
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $item = $player->getInventory()->getItemInHand();
        if($item->equals($this->getPickResult())) {
            $entity = new $this($this->location);
            if($entity instanceof Animal) {
                $entity->setBaby(true);
            }
            $entity->spawnToAll();
            if(!$player->isCreative()) {
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }

    public function ate(): void {
    }

    //TODO: We first need to implement leashes
    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, $this->leashedToEntityId ?? -1);
    }

    public function canBeLeashed(): bool {
        return false;
    }

    public function setLeashedToEntity(Entity $entity): void{
        $this->leashed = true;
        $this->leashedToEntityId = $entity->getId();
    }

    public function getLeashedToEntity() : ?Entity{
        if($this->leashedToEntityId !== null){
            return Server::getInstance()->getWorldManager()->findEntity($this->leashedToEntityId);
        }
        return null;
    }

    public function isLeashed(): bool{
        return $this->leashed;
    }
}