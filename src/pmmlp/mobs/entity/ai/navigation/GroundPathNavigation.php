<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\navigation;

use pmmlp\mobs\util\BlockHeightCalculator;

class GroundPathNavigation extends PathNavigation {
    private int $jumpTicks = 0;

    private float $lastBlockHeight = 1.0;

    public function tick(): void {
        $location = $this->mob->getLocation();
        if($this->path !== null) {
            $motion = $this->mob->getMotion();
            if($this->node === null || $location->distanceSquared($this->node) < 0.25) {
                if($this->node !== null) {
                    $this->lastBlockHeight = BlockHeightCalculator::getMaxY($location->getWorld()->getBlock($this->node->down()));
                }
                $this->node = $this->path->pop();
                if($this->node === null) {
                    $this->stop();
                    return;
                }
            }

            $rotation = $this->mob->getMovementRotation($this->node);
            $direction = $this->mob->getMovementDirection($rotation);
            $this->mob->setRotation($rotation, $location->getPitch());

            if($this->mob->isOnGround()) {
                if($this->jumpTicks > 0) {
                    $this->jumpTicks--;
                }

                if($this->mob->isCollidedHorizontally && $this->jumpTicks <= 0) {
                   if(!$this->mob->isBodyInsideOfLiquid()) {
                       $this->jumpTicks = 5;
                       $currentBlockHeight = BlockHeightCalculator::getMaxY($location->getWorld()->getBlock($this->node->down()));
                       if($this->lastBlockHeight !== 1.0 && $currentBlockHeight === 1.0) {
                           $motion->y = $this->mob->getJumpVelocity() / 1.2;
                       } else {
                           $motion->y = $this->mob->getJumpVelocity();
                       }
                   } else {
                       $directionVector = $direction->multiply($this->customSpeed ?? $this->mob->getMovementSpeed());
                       $motion->x = $directionVector->x * 1.5;
                       $motion->y = $this->mob->getJumpVelocity();
                       $motion->z = $directionVector->z * 1.5;
                   }
                } else {
                    $directionVector = $direction->multiply($this->customSpeed ?? $this->mob->getMovementSpeed());
                    $motion->x = $directionVector->x;
                    $motion->z = $directionVector->z;
                }

                if(($this->mob->fallDistance > 0.0) && $this->jumpTicks <= 0){
                    $motion->x = 0;
                    $motion->z = 0;
                }
            } elseif($this->jumpTicks > 0 && $this->node !== null) {
                $directionVector = $this->mob->getMovementDirection($this->node)->multiply($this->customSpeed ?? $this->mob->getMovementSpeed());
                $motion->x = $directionVector->x / 4;
                $motion->z = $directionVector->z / 4;
            }
            $this->mob->setMotion($motion);
        }
    }
}