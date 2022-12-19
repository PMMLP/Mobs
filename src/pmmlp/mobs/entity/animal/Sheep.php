<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\animal;

use pmmlp\mobs\entity\ai\goal\BreedGoal;
use pmmlp\mobs\entity\ai\goal\EatBlockGoal;
use pmmlp\mobs\entity\ai\goal\FloatGoal;
use pmmlp\mobs\entity\ai\goal\FollowParentGoal;
use pmmlp\mobs\entity\ai\goal\LookAtPlayerGoal;
use pmmlp\mobs\entity\ai\goal\PanicGoal;
use pmmlp\mobs\entity\ai\goal\RandomLookAroundGoal;
use pmmlp\mobs\entity\ai\goal\TemptGoal;
use pmmlp\mobs\entity\ai\goal\WaterAvoidingRandomStrollGoal;
use pmmlp\mobs\util\DyeColorCombiner;
use pmmlp\mobs\util\MobsConfig;
use pmmlp\mobs\world\sound\PlaySound;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Durable;
use pocketmine\item\Dye;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class Sheep extends Animal {
    public const BABY_SCALE = 0.4;

    protected DyeColor $color;

    protected bool $sheared = false;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(8);

        $this->color = DyeColorIdMap::getInstance()->fromId($nbt->getByte("Color", DyeColorIdMap::getInstance()->toId($this->getRandomColor())));
        $this->sheared = (bool)$nbt->getByte("Sheared", 0);
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setByte("Sheared", (int)$this->sheared);
        $nbt->setByte("Color", DyeColorIdMap::getInstance()->toId($this->color));
        return $nbt;
    }

    public function getRandomColor(): DyeColor {
        $random = random_int(0, 100);
        if($random < 5) {
            return DyeColor::BLACK();
        }
        if($random < 10) {
            return DyeColor::GRAY();
        }
        if($random < 15) {
            return DyeColor::LIGHT_GRAY();
        }
        if($random < 18) {
            return DyeColor::BROWN();
        }
        return random_int(0, 500) === 0 ? DyeColor::PINK() : DyeColor::WHITE();
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.3, 0.9);
    }

    public static function getNetworkTypeId(): string{
        return EntityIds::SHEEP;
    }

    public function getName(): string{
        return "Sheep";
    }

    public function getColor(): DyeColor{
        return $this->color;
    }

    public function setColor(DyeColor $color): void{
        $this->color = $color;
        $this->networkPropertiesDirty = true;
    }

    public function isSheared(): bool{
        return $this->sheared;
    }

    public function setSheared(bool $sheared): void{
        $this->sheared = $sheared;
        $this->networkPropertiesDirty = true;
    }

    public function getDrops(): array{
        if($this->isBaby()){
            return [];
        }
        return [
            VanillaBlocks::WOOL()->setColor($this->getColor())->asItem(),
            ($this->isOnFire() ? VanillaItems::COOKED_MUTTON() : VanillaItems::RAW_MUTTON())->setCount(random_int(1, 2))
        ];
    }

    public function getBreedXpAmount(): int{
        return random_int(1, 7);
    }

    public function getXpDropAmount(): int{
        return random_int(1, 3);
    }

    public function getMovementSpeed(): float{
        return 0.2;
    }

    public function getDespawnDistance(): int{
        return MobsConfig::$sheepDespawnDistance;
    }

    public function getNoDespawnDistance(): int{
        return MobsConfig::$sheepNoDespawnDistance;
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $inventory = $player->getInventory();
        $item = $inventory->getItemInHand();
        if($item->equals(VanillaItems::SHEARS(), false, false)) {
            if($this->isSheared() || $this->isBaby()) {
                return false;
            }

            $this->setSheared(true);

            $this->getWorld()->dropItem($this->location, VanillaBlocks::WOOL()->setColor($this->getColor())->asItem()->setCount(random_int(1, 3)));
            $this->getWorld()->addSound($this->location, new PlaySound("mob.sheep.shear"));

            if(!$player->isCreative(true)) {
                /** @var Durable $item */
                $item->applyDamage(1);
                $player->getInventory()->setItemInHand($item);
            }
            return true;
        }
        if($item instanceof Dye) {
            if($this->getColor()->equals($item->getColor())) {
                return false;
            }
            $this->setColor($item->getColor());

            if(!$player->isCreative(true)) {
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setByte(EntityMetadataProperties::COLOR, DyeColorIdMap::getInstance()->toId($this->color));
        $properties->setGenericFlag(EntityMetadataFlags::SHEARED, $this->isSheared());
    }

    public function getBreedOffspring(Animal $animal): Animal{
        $sheep = (new Sheep($animal->getLocation()))->setBaby(true);
        $color = DyeColorCombiner::combine($animal->getColor(), $this->getColor());
        $sheep->setColor($color ?? (random_int(0, 1) === 0 ? $animal->getColor() : $this->getColor()));
        return $sheep;
    }

    protected function registerGoals(): void{
        $this->goalSelector->addGoal(new FloatGoal(0));
        $this->goalSelector->addGoal(new PanicGoal(1, 0.5));
        $this->goalSelector->addGoal(new BreedGoal(2, 0.25));
        $this->goalSelector->addGoal(new TemptGoal(3, 0.3, false));
        $this->goalSelector->addGoal(new FollowParentGoal(4, 0.4));
        $this->goalSelector->addGoal(new EatBlockGoal(5));
        $this->goalSelector->addGoal(new WaterAvoidingRandomStrollGoal(6));
        $this->goalSelector->addGoal(new LookAtPlayerGoal(7, Player::class, 6));
        $this->goalSelector->addGoal(new RandomLookAroundGoal(8));
    }

    public function getPickResult(): Item{
        return ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityLegacyIds::SHEEP);
    }

    public function ate(): void {
        $this->setSheared(false);
        if($this->isBaby()) {
            $this->age += 60;
        }
    }

    public function onUpdate(int $currentTick): bool{
        if($this->getNameTag() === "_jeb") {
            //TODO
        }
        return parent::onUpdate($currentTick);
    }
}