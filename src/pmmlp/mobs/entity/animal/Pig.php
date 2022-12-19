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

//TODO: Implement riding
class Pig extends Animal {
    public const BABY_SCALE = 0.5;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getBreedOffspring(Animal $animal): Animal{
        return (new Pig($animal->getLocation()))->setBaby(true);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 0.9, 0.25);
    }

    public static function getNetworkTypeId(): string{
        return EntityIds::PIG;
    }

    public function getName(): string{
        return "Pig";
    }

    protected function registerGoals(): void{
        $this->goalSelector->addGoal(new FloatGoal(0));
        $this->goalSelector->addGoal(new PanicGoal(1, 0.5));
        $this->goalSelector->addGoal(new BreedGoal(3, 0.25));
        $this->goalSelector->addGoal(new TemptGoal(4, 0.3, false));
        //$this->goalSelector->addGoal(new TemptGoal(4, 0.45, false, [VanillaItems::CARROT()])); //TODO: We first have to implement carrot on a stick
        $this->goalSelector->addGoal(new FollowParentGoal(5, 0.4));
        $this->goalSelector->addGoal(new WaterAvoidingRandomStrollGoal(6));
        $this->goalSelector->addGoal(new LookAtPlayerGoal(7, Player::class, 6));
        $this->goalSelector->addGoal(new RandomLookAroundGoal(8));
    }

    public function getPickResult(): Item{
        return ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityLegacyIds::PIG);
    }

    public function getMovementSpeed(): float{
        return 0.2;
    }

    public function isFood(Item $item): bool{
        return $item->equals(VanillaItems::CARROT()) || $item->equals(VanillaItems::POTATO()) || $item->equals(VanillaItems::BEETROOT());
    }

    public function getEyeHeight(): float{
        return $this->isBaby() ? parent::getEyeHeight() / 2 : parent::getEyeHeight();
    }

    public function getDrops(): array{
        if($this->isBaby()){
            return [];
        }
        return [
            ($this->isOnFire() ? VanillaItems::COOKED_PORKCHOP() : VanillaItems::RAW_PORKCHOP())
        ];
    }

    public function getBreedXpAmount(): int{
        return random_int(1, 7);
    }

    public function getXpDropAmount(): int{
        return random_int(1, 3);
    }

    public function getDespawnDistance(): int{
        return MobsConfig::$pigDespawnDistance;
    }

    public function getNoDespawnDistance(): int{
        return MobsConfig::$pigNoDespawnDistance;
    }

    public function getBabyChance(): int{
        return 5;
    }
}