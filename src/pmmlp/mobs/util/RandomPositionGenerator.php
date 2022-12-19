<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

class RandomPositionGenerator {
    public static function findRandomPositionAwayFrom(Position $position, Vector3 $avoid, int $xzRange, int $yRange = 2): ?Vector3 {
        self::findRandomPosition($position, $xzRange, $yRange, $avoid);
    }

    public static function findRandomPosition(Position $position, int $xzRange, int $yRange = 2, ?Vector3 $avoid = null): ?Vector3 {
        $world = $position->getWorld();

        $values = [-1, 1];

        $current = null;
        for($i = 0; $i <= MobsConfig::$maxRandomPositionGeneratorIterations; $i++) {
            for($y = -$yRange; $y <= $yRange; $y++) {
                $target = $position->add($values[array_rand($values)] * $xzRange, $yRange, $values[array_rand($values)] * $xzRange);
                if($avoid !== null && $target->distanceSquared($avoid) <= 1) {
                    continue;
                }
                if($world->getBlock($target)->isSameType(VanillaBlocks::GRASS())) {
                    $current = $target;
                    break 2;
                }
                if($current === null) {
                    $current = $target;
                }
            }
        }
        return $current;
    }
}