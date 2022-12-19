<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use pocketmine\block\Block;

class BlockHeightCalculator {
    private static array $maxYCache = [];

    public static function getMaxY(Block $block): float {
        $id = $block->getFullId();
        if(isset(self::$maxYCache[$id])) {
            return self::$maxYCache[$id];
        }
        $block = clone $block;
        $bb = $block->getCollisionBoxes();
        foreach($bb as $axisAlignedBB) {
            $length = $axisAlignedBB->getYLength();
            if(!isset(self::$maxYCache[$id]) || $length > self::$maxYCache[$id]) {
                self::$maxYCache[$id] = $length;
            }
        }
        if(count(self::$maxYCache) > 100) {
            array_shift(self::$maxYCache);
        }
        return self::$maxYCache[$id] ?? 0.0;
    }
}