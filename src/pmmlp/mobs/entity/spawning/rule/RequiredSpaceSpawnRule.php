<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\spawning\rule;

use pocketmine\world\Position;

class RequiredSpaceSpawnRule extends SpawnRule {
    public function __construct(
        protected int $height = 2,
        protected int $width = 1
    ){}

    public function appliesToPosition(Position $position): bool{
        $world = $position->getWorld();
        $width = $this->width - 1;
        for($x = -$width; $x <= $width; $x++) {
            for($z = -$width; $z <= $width; $z++) {
                for($y = 0; $y <= $this->height; $y++) {
                    if($world->getBlockAt($position->x + $x, $position->y + $y, $position->z + $z)->isSolid()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}