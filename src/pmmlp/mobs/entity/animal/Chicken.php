<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\animal;

use pmmlp\mobs\entity\ai\goal\BreedGoal;
use pmmlp\mobs\entity\ai\goal\FloatGoal;
use pmmlp\mobs\entity\ai\goal\FollowParentGoal;
use pmmlp\mobs\entity\ai\goal\LookAtPlayerGoal;
use pmmlp\mobs\entity\ai\goal\PanicGoal;
use pmmlp\mobs\entity\ai\goal\RandomLookAroundGoal;
use pmmlp\mobs\entity\ai\goal\TemptGoal;
use pmmlp\mobs\entity\ai\goal\WaterAvoidingRandomStrollGoal;
use pmmlp\mobs\util\MobsConfig;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\sound\PopSound;

class Chicken extends Animal {
    public const BABY_SCALE = 0.5;

    protected bool $jockey = false;
    protected int $nextEggTimer = 0;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(4);

        $this->nextEggTimer = $nbt->getInt("EggLayTime", 6000);
        $this->jockey = (bool)$nbt->getByte("IsChickenJockey", 0);
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setInt("EggLayTime", $this->nextEggTimer);
        $nbt->setByte("IsChickenJockey", (int)$this->jockey);
        return $nbt;
    }

    public function getBreedOffspring(Animal $animal): Animal{
        return (new Chicken($animal->getLocation()))->setBaby(true);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.8, 0.6);
    }

    public static function getNetworkTypeId(): string{
        return EntityIds::CHICKEN;
    }

    public function getName(): string{
        return "Chicken";
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        if($this->motion->y < 0 && !$this->isOnGround()){
            $this->motion->y *= 0.6;
        }

        if(($this->nextEggTimer -= $tickDiff) <= 0) {
            $this->nextEggTimer = random_int(6000, 12000);
            $this->getWorld()->dropItem($this->location, VanillaItems::EGG());
            $this->getWorld()->addSound($this->location, new PopSound());
        }
        return parent::entityBaseTick($tickDiff);
    }

    protected function registerGoals(): void{
        $this->goalSelector->addGoal(new FloatGoal(0));
        $this->goalSelector->addGoal(new PanicGoal(1, 0.5));
        $this->goalSelector->addGoal(new BreedGoal(2, 0.25));
        $this->goalSelector->addGoal(new TemptGoal(3, 0.3, false));
        $this->goalSelector->addGoal(new FollowParentGoal(4, 0.4));
        $this->goalSelector->addGoal(new WaterAvoidingRandomStrollGoal(5));
        $this->goalSelector->addGoal(new LookAtPlayerGoal(6, Player::class, 6));
        $this->goalSelector->addGoal(new RandomLookAroundGoal(7));
    }

    public function getPickResult(): Item{
        return ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityLegacyIds::CHICKEN);
    }

    public function isFood(Item $item): bool{
        return $item->equals(VanillaItems::WHEAT_SEEDS()) || $item->equals(VanillaItems::PUMPKIN_SEEDS()) || $item->equals(VanillaItems::MELON_SEEDS()) || $item->equals(VanillaItems::BEETROOT_SEEDS());
    }

    public function getBabyChance(): int{
        return 5;
    }

    public function getDrops(): array{
        if($this->isBaby()){
            return [];
        }
        return [
            VanillaItems::FEATHER()->setCount(random_int(0, 2)),
            ($this->isOnFire() ? VanillaItems::COOKED_CHICKEN() : VanillaItems::RAW_CHICKEN())
        ];
    }

    public function getBreedXpAmount(): int{
        return random_int(1, 7);
    }

    public function getXpDropAmount(): int{
        return random_int(1, 3);
    }

    public function getMovementSpeed(): float{
        return 0.25;
    }

    public function getDespawnDistance(): int{
        return MobsConfig::$chickenDespawnDistance;
    }

    public function getNoDespawnDistance(): int{
        return MobsConfig::$chickenNoDespawnDistance;
    }

    public function getFallDistance(): float{
        return 0.0;
    }

    public function getEyeHeight(): float{
        return $this->isBaby() ? parent::getEyeHeight() / 2 : parent::getEyeHeight();
    }

    public function setJockey(bool $jockey): void{
        $this->jockey = $jockey;
    }

    public function isJockey(): bool{
        return $this->jockey;
    }
}