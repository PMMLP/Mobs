<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\spawning\rule;

use pocketmine\world\Position;

class AvoidBiomesSpawnRule extends SpawnRule {
    public function __construct(
        protected array $biomes
    ){}

    public function appliesToPosition(Position $position): bool{
        $biome = $position->getWorld()->getBiome($position->getFloorX(), $position->getFloorZ());
        return in_array($biome, $this->biomes, false);
    }
}