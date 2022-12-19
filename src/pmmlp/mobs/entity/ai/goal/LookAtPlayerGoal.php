<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pocketmine\entity\Entity;

class LookAtPlayerGoal extends Goal {
    protected ?Entity $target = null;

    protected int $lookTicks = 0;

    public function initFlags(): void{
        $this->addFlags(Flags::LOOK);
    }

    public function __construct(
        int $priority,
        protected string $targetType,
        protected float $distance,
        protected float $probability = 0.02
    ){
        parent::__construct($priority);
    }

    public function canUse(): bool{
        if(random_int(0, 100) / 100 >= $this->probability) {
            return false;
        }
        $this->target = $this->mob->getWorld()->getNearestEntity($this->mob->getPosition(), $this->distance, $this->targetType);
        return $this->target !== null;
    }

    public function canContinueToUse(): bool{
        $target = $this->target;
        if($target === null) {
            return false;
        }
        if(!$target->isAlive()) {
            return false;
        }
        if($target->getPosition()->distanceSquared($this->mob->getPosition()) > ($this->distance ** 2)) {
            return false;
        }
        return $this->lookTicks > 0;
    }

    protected function start(): void{
        $this->lookTicks = random_int(40, 80);
    }

    protected function stop(): void{
        $this->mob->getLookControl()->setTarget(null);
        $this->target = null;
    }

    public function tick(): void{
        $this->mob->getLookControl()->setTarget($this->target->getPosition(), false);
        $this->lookTicks--;
    }
}