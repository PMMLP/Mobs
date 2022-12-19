<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\entity\animal\Animal;
use pmmlp\mobs\entity\Mob;
use pocketmine\entity\Entity;
use RuntimeException;

class FollowParentGoal extends Goal {
    /** @var Animal  */
    protected Mob $mob;

    protected ?Entity $parent = null;

    protected int $ticksUntilRecalculate = 0;

    public function setMob(Mob $mob): void{
        if(!$mob instanceof Animal) {
            throw new RuntimeException("Mob has to be an instance of Animal");
        }
        $this->mob = $mob;
    }

    public function __construct(
        int $priority,
        protected float $speed
    ){
        parent::__construct($priority);
    }

    public function canUse(): bool{
        if(!$this->mob->isBaby()) {
            return false;
        }
        $parents = array_filter($this->mob->getWorld()->getNearbyEntities($this->mob->getBoundingBox()->expandedCopy(8, 4, 8), $this->mob), function(Entity $entity): bool {
            return $entity::class === $this->mob::class && $entity instanceof Animal && !$entity->isBaby();
        });
        if(count($parents) <= 0) {
            return false;
        }
        $list = [];
        $position = $this->mob->getPosition();
        foreach($parents as $parent) {
            $list[$position->distanceSquared($parent->getPosition())] = $parent;
        }
        ksort($list);
        $this->parent = array_shift($list);
        return true;
    }

    public function canContinueToUse(): bool{
        if(!$this->mob->isBaby()) {
            return false;
        }
        $parent = $this->parent;
        if($parent === null || !$parent->isAlive()) {
            return false;
        }
        $distance = $parent->getPosition()->distanceSquared($this->mob->getPosition());
        return $distance >= 12 && $distance <= 256;
    }

    protected function start(): void{
        $this->ticksUntilRecalculate = 0;
    }

    protected function stop(): void{
        $this->mob->getNavigation()->stop();
        $this->parent = null;
    }

    public function tick(): void{
        if(--$this->ticksUntilRecalculate <= 0) {
            $this->ticksUntilRecalculate = 10;

            $position = $this->parent->getPosition();
            $this->mob->getLookControl()->setTarget($position, false);
            $this->mob->getNavigation()->findPath($position->subtractVector($this->parent->getDirectionVector()->multiply(1.2)), false, $this->speed);
        }
    }

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE);
    }
}