<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use http\Exception\RuntimeException;
use pmmlp\mobs\entity\animal\Animal;
use pmmlp\mobs\entity\Mob;
use pocketmine\entity\Entity;

class BreedGoal extends Goal {
    /** @var Animal  */
    protected Mob $mob;

    protected ?Animal $partner = null;

    protected int $loveTicks = 0;

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE, Flags::LOOK);
    }

    public function setMob(Mob $mob): void{
        if(!$mob instanceof Animal) {
            throw new RuntimeException("Mob has to be an instance of Animal");
        }
        $this->mob = $mob;
    }

    public function __construct(
        int $priority,
        protected float $speed,
    ){
        parent::__construct($priority);
    }

    public function canUse(): bool{
        if(!$this->mob->isInLove()) {
            return false;
        }
        $this->partner = $this->findPartnerOnTinder();
        return $this->partner !== null;
    }

    public function canContinueToUse(): bool{
        return $this->partner->isAlive() && $this->partner->isInLove() && $this->loveTicks < 60;
    }

    protected function stop(): void{
        $this->mob->getLookControl()->setTarget(null);
        $this->partner = null;
        $this->loveTicks = 0;
    }

    public function tick(): void{
        $position = $this->partner->getPosition();

        $this->mob->getLookControl()->setTarget($position, false);
        $this->mob->getNavigation()->findPath($position, false);
        if(++$this->loveTicks >= 60 && $position->distanceSquared($this->mob->getPosition()) < 9) {
            $this->mob->spawnChildFromBreeding($this->partner);
        }
    }

    protected function findPartnerOnTinder(): ?Animal {
        $targets = array_filter($this->mob->getWorld()->getNearbyEntities($this->mob->getBoundingBox()->expandedCopy(8, 4, 8)), function(Entity $entity): bool {
            return $this->mob->canMate($entity);
        });
        if(count($targets) <= 0) {
            return null;
        }
        $position = $this->mob->getPosition();

        $nearest = null;
        $nearestVal = PHP_INT_MAX;
        foreach($targets as $target) {
            $distance = $position->distanceSquared($target->getPosition());
            if($nearest === null || $distance < $nearestVal) {
                $nearest = $target;
                $nearestVal = $distance;
            }
        }
        return $nearest;
    }
}