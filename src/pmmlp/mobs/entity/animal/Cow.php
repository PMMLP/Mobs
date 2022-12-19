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
use pmmlp\mobs\world\sound\PlaySound;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class Cow extends Animal {
    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getDrops(): array{
        if($this->isBaby()){
            return [];
        }
        return [
            VanillaItems::LEATHER()->setCount(random_int(0, 2)),
            ($this->isOnFire() ? VanillaItems::STEAK() : VanillaItems::RAW_BEEF())
        ];
    }

    public function getBreedOffspring(Animal $animal): Animal{
        return (new Cow($animal->getLocation()))->setBaby(true);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.3, 0.9, 1.25);
    }

    public static function getNetworkTypeId(): string{
        return EntityIds::COW;
    }

    public function getName(): string{
        return "Cow";
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
        return ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityLegacyIds::COW);
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $inventory = $player->getInventory();
        $item = $inventory->getItemInHand();
        if($item->equals(VanillaItems::BUCKET(), true, false)) {
            $item->pop();
            $inventory->setItemInHand($item);
            $inventory->addItem(VanillaItems::MILK_BUCKET());

            $this->getWorld()->addSound($this->location, new PlaySound("mob.cow.milk"));
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }

    public function getBabyChance(): int{
        return 5;
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
        return MobsConfig::$cowDespawnDistance;
    }

    public function getNoDespawnDistance(): int{
        return MobsConfig::$cowNoDespawnDistance;
    }
}