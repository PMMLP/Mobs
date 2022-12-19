<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use Closure;
use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\world\Position;

class BlockFinder {
    private static function findBlockInternal(Position $position, Closure $blockValidator, int $radius = 4, int $verticalLimit = -1): ?Block {
        $world = $position->getWorld();

        $posX = $position->getFloorX();
        $posY = $position->getFloorY();
        $posZ = $position->getFloorZ();

        for($size = 0; $size <= $radius; $size++) {
            $found = null;

            $yLimit = ($verticalLimit === -1 ? $radius : $verticalLimit);
            for($y = -$yLimit; $y <= $yLimit; $y++) {
                for($x = -$size; $x <= $size; $x++) {
                    if((bool)(($blockValidator)(($block = $world->getBlockAt($posX + $x, $posY + $y, $posZ - $size)))) === true) {
                        $found = self::compare($block, $found, $position);
                    }
                    if((bool)(($blockValidator)(($block = $world->getBlockAt($posX + $x, $posY + $y, $posZ + $size)))) === true) {
                        $found = self::compare($block, $found, $position);
                    }
                }

                for($z = -$size; $z <= $size; $z++) {
                    if((bool)(($blockValidator)(($block = $world->getBlockAt($posX + $size, $posY + $y, $posZ + $z)))) === true) {
                        $found = self::compare($block, $found, $position);
                    }
                    if((bool)(($blockValidator)(($block = $world->getBlockAt($posX - $size, $posY + $y, $posZ + $z)))) === true) {
                        $found = self::compare($block, $found, $position);
                    }
                }
            }
            if($found !== null) {
                return $found;
            }
        }
        return null;
    }

    private static function compare(Block $block1, ?Block $block2, Position $position): Block {
        if($block2 === null) {
            return $block1;
        }
        if($block1->getPosition()->distanceSquared($position) > $block2->getPosition()->distanceSquared($position)) {
            return $block2;
        }
        return $block1;
    }

    public static function findBlockType(Position $position, Block $search, int $radius = 4, int $verticalLimit = -1): ?Block {
        return self::findBlockInternal($position, function(Block $block) use ($search): bool {
            return $block->isSameType($search);
        }, $radius, $verticalLimit);
    }

    public static function findLandBlock(Position $position, int $radius = 4, int $verticalLimit = -1): ?Block {
        return self::findBlockInternal($position, function(Block $block): bool {
            return $block->isSolid() && $block->getSide(Facing::DOWN)->isSolid();
        }, $radius, $verticalLimit);
    }
}