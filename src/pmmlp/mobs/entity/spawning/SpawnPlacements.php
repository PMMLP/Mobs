<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\spawning;

use pmmlp\mobs\entity\spawning\rule\SpawnRule;

class SpawnPlacements {
    protected static array $rules = [];

    public static function register(string $entity, SpawnRule... $rules): void {
        self::$rules[$entity] = $rules;
    }

    public static function addRule(string $entity, SpawnRule $rule): void {
        self::$rules[$entity][] = $rule;
    }
}