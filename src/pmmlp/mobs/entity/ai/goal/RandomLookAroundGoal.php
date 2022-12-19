<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\util\RandomPositionGenerator;

class RandomLookAroundGoal extends Goal {
    protected int $lookTicks = 0;

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE, Flags::LOOK);
    }

    public function canUse(): bool{
        return random_int(0, 100) / 100 < 0.02 && $this->mob->getNavigation()->isDone();
    }

    protected function start(): void{
        $this->lookTicks = random_int(20, 40);

        $position = $this->mob->getPosition();
        $this->mob->getLookControl()->setTarget((RandomPositionGenerator::findRandomPosition($position, 2, 1) ?? $this->mob->getPosition())->withComponents(null, $position->y, 0));
    }

    protected function stop(): void{
        $this->mob->getLookControl()->setTarget(null);
    }

    public function canContinueToUse(): bool{
        return $this->lookTicks >= 0;
    }

    public function tick(): void{
        $this->lookTicks--;
    }
}