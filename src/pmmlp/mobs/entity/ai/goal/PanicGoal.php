<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\util\BlockFinder;
use pmmlp\mobs\util\RandomPositionGenerator;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;

class PanicGoal extends Goal {
    protected ?Vector3 $target = null;

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE);
    }

    public function __construct(
        int $priority,
        protected float $speed
    ){
        parent::__construct($priority);
    }

    public function canUse(): bool{
        if(!$this->shouldPanic()) {
            return false;
        }
        if($this->mob->isOnFire()) {
            $water = BlockFinder::findBlockType($this->mob->getLocation(), VanillaBlocks::WATER(), 5, 2);
            if($water !== null) {
                $this->target = $water->getPosition();
                return true;
            }
        }
        $avoid = $this->mob->getLastDamager();
        $this->target = RandomPositionGenerator::findRandomPosition($this->mob->getPosition(), 5, 2, $avoid?->getPosition());
        return true;
    }

    public function canContinueToUse(): bool{
        return !$this->mob->getNavigation()->isDone();
    }

    public function shouldPanic(): bool {
        return $this->mob->isOnFire() || $this->mob->getLastDamager() instanceof Entity;
    }

    protected function start(): void{
        $this->mob->getNavigation()->findPath($this->target, false, $this->speed);
    }

    public function isInterruptable(): bool{
        return false;
    }
}