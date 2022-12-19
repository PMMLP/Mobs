<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\pathfinder\evaluator;

use pmmlp\mobs\util\BlockHeightCalculator;
use pmmlp\mobs\util\NotPathfindableBlockList;
use pocketmine\block\Door;
use pocketmine\block\Fence;
use pocketmine\block\FenceGate;
use pocketmine\block\Lava;
use pocketmine\block\Liquid;
use pocketmine\block\Water;
use pocketmine\block\WoodenDoor;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class WalkNodeEvaluator extends NodeEvaluator {
    public function evaluate(Vector3 $current, Vector3 $side): ?Vector3{
        $world = $this->mob->getWorld();

        $currentBlock = $world->getBlock($current);

        $block = $world->getBlock($side);
        if($block instanceof Door && (($this->canOpenDoors() && $block instanceof WoodenDoor) || ($block->isOpen() && !$block->isTop() && $this->canPassDoors()))) {
            return $side;
        }

        if($this->isSafeToStand($side)) {
            return $side;
        }

        $currentBlockHeight = BlockHeightCalculator::getMaxY($currentBlock) + $current->getFloorY();

        //Jump Height Check
        for($y = 0; $y <= $this->mob->getMaxJumpHeight(); ++$y) {
            $tempSide = $side->add(0, $y, 0);
            if(!$this->isSafeToStand($tempSide)) {
                continue;
            }
            if(abs($tempSide->getY() + BlockHeightCalculator::getMaxY($world->getBlock($tempSide)) - $currentBlockHeight) > $this->mob->getMaxJumpHeight()) {
                continue;
            }
            $side->y += $y;
            return $side;
        }

        //Fall Distance Check
        for($y = 0; $y <= $this->mob->getMaxFallDistance(); ++$y) {
            if(!$this->isSafeToStand($side->subtract(0, $y, 0))) {
                continue;
            }
            $side->y -= $y;
            return $side;
        }
        return null;
    }

    private function isSafeToStand(Vector3 $vector3): bool {
        $world = $this->mob->getWorld();

        $below = $world->getBlock($vector3->down());
        if($below instanceof Water && (!$this->canFloat() || $this->mob->isOnFire())) {
            return false;
        }
        if($below instanceof Lava && !$this->canWalkOverLava()) {
            return false;
        }
        if(!$below->isSolid() || NotPathfindableBlockList::contains($below)) {
            return false;
        }
        if($below instanceof Fence && !$this->canWalkOverFences()) {
            return false;
        }

        $size = $this->mob->getSize();
        $halfWidth = $size->getWidth() / 2;

        foreach($world->getCollisionBlocks(new AxisAlignedBB(
            $vector3->getFloorX() + 0.5 - $halfWidth, $vector3->getFloorY() + $this->mob->getYSize(), $vector3->getFloorZ() + 0.5 - $halfWidth,
            $vector3->getFloorX() + 0.5 + $halfWidth, $vector3->getFloorY() + $this->mob->getYSize() + $size->getEyeHeight(), $vector3->getFloorZ() + 0.5 + $halfWidth,
        )) as $block) {
            if($block->isSolid() || NotPathfindableBlockList::contains($block)){
                return false;
            }
            if($block instanceof FenceGate && (!$this->canPassDoors() || !$block->isOpen())) {
                return false;
            }
            if($block instanceof Liquid) {
                return false;
            }
        }
        return true;
    }
}