<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

class FloatGoal extends Goal {
    public function initFlags(): void{
        $this->addFlags(Flags::JUMP);
    }

    public function canUse(): bool{
        return false;
    }

    public function requiresUpdateEveryTick(): bool{
        return true;
    }

    public function tick(): void{
        if($this->mob->isHeadInsideOfLiquid()) {
            $jumpVelocity = $this->mob->getJumpVelocity();
            $motion = $this->mob->getMotion();
            $motion->y += $jumpVelocity / 2;
            if($motion->y > $jumpVelocity) {
                $motion->y = $jumpVelocity;
            }
            $this->mob->setMotion($motion);
        }
    }
}