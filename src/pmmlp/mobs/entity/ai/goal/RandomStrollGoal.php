<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\util\RandomPositionGenerator;
use pocketmine\math\Vector3;
use pocketmine\Server;

class RandomStrollGoal extends Goal {
    protected ?Vector3 $target;

    protected int $next = 0;

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE);
    }

    public function canUse(): bool{
        if(Server::getInstance()->getTick() <= $this->next) {
            return false;
        }
        return ($this->target = $this->getPosition()) !== null;
    }

    public function canContinueToUse(): bool{
        return !$this->mob->getNavigation()->isDone();
    }

    protected function start(): void{
        $this->mob->getNavigation()->findPath($this->target, $this->mob->isBodyInsideOfLiquid());
        $this->mob->getLookControl()->setTarget(null);
    }

    protected function stop(): void{
        $this->next = Server::getInstance()->getTick() + 120;
    }

    public function getPosition(): ?Vector3 {
        return RandomPositionGenerator::findRandomPosition($this->mob->getPosition(), random_int(4, 8));
    }
}