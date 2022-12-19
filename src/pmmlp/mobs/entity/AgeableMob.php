<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity;

use pmmlp\mobs\entity\animal\Animal;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

abstract class AgeableMob extends PathfinderMob {
    public const AGE_BABY = 0;
    public const AGE_ADULT = 24000;

    public const BABY_SCALE = 0.5;

    protected bool $baby = false;
    protected int $age = 0;

    protected int $breedCooldown = 0;

   protected function initEntity(CompoundTag $nbt): void{
       parent::initEntity($nbt);
       $this->setBaby(random_int(0, 100) <= $this->getBabyChance());
   }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        if($this->isBaby()) {
            $this->age += $tickDiff;
            if($this->getAge() >= self::AGE_ADULT){
                $this->setBaby(false);
            }
        }
        if($this->breedCooldown > 0) {
            $this->breedCooldown -= $tickDiff;
        }
        return parent::entityBaseTick($tickDiff);
    }

    public function isBaby(): bool{
        return $this->baby;
    }

    public function setBaby(bool $baby): self{
        $this->baby = $baby;
        $this->age = $baby ? $this::AGE_BABY : $this::AGE_ADULT;
        $this->setScale($baby ? $this::BABY_SCALE : 1);
        return $this;
    }

    public function getAge(): int{
        return $this->age;
    }

    public function getBabyChance(): int {
       return 10;
    }

    public function resetBreedCooldown(): void {
       $this->breedCooldown = 6000;
    }

    public function hasBreedCooldown(): bool {
       return $this->breedCooldown > 0;
    }

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::BABY, $this->isBaby());
    }

    abstract public function getBreedOffspring(Animal $animal): Animal;
}