<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\spawning\rule;

use pocketmine\world\Position;

abstract class SpawnRule {
    abstract public function appliesToPosition(Position $position): bool;
}