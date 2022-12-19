<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\spawning\rule;

use pocketmine\world\Position;

class RequiredLightLevelSpawnRule extends SpawnRule {
    public function __construct(
        protected int $lightLevel
    ){}

    public function appliesToPosition(Position $position): bool{
        return $position->getWorld()->getFullLight($position) >= $this->lightLevel;
    }
}