<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\control;

use pmmlp\mobs\entity\Mob;
use pocketmine\math\Vector3;

class LookControl implements Control {
    protected ?Vector3 $target = null;

    public function __construct(
        protected Mob $mob
    ){}

    public function getTarget(): ?Vector3{
        return $this->target;
    }

    public function setTarget(?Vector3 $target, bool $addEyeHeight = true): void{
        $this->target = $target?->add(0, ($addEyeHeight ? $this->mob->getEyeHeight() : 0), 0);
    }

    public function tick(): void {
        $this->rotateTowards();
    }

    public function rotateTowards(): void {
        $location = $this->mob->getLocation();
        $target = $this->getTarget();

        if($target === null) {
            if($this->mob->getNavigation()->isDone()) {
                $this->mob->setRotation($location->yaw, 0);
                $this->mob->setHeadYaw($location->yaw);
                return;
            }
            $target = $this->mob->getNavigation()->getNode();
            if($target === null) {
                return;
            }
            $pitch = 0;
        } else {
            $horizontal = sqrt(($target->x - $location->x) ** 2 + ($target->z - $location->z) ** 2);
            $vertical = $target->y - $location->y;
            $pitch = -atan2($vertical, $horizontal) / M_PI * 180;
        }

        $xDist = $target->x - $location->x;
        $zDist = $target->z - $location->z;
        $targetYaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if($targetYaw < 0) {
            $targetYaw += 360;
        }

        $this->mob->setHeadYaw($targetYaw);
        $this->mob->setRotation($location->yaw, $pitch + (random_int(0, 100) / 10000));//HACK
        $this->mob->setForceMovementUpdate();
        $this->mob->scheduleUpdate();
    }
}