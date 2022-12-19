<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use pmmlp\config\Config;

class MobsConfig extends Config {
    public const DEBUG = false;

    public static int $maxPathfinderIterations = 24;
    public static int $maxRandomPositionGeneratorIterations = 8;

    public static bool $registerPigs = true;
    public static int $pigDespawnDistance = 128;
    public static int $pigNoDespawnDistance = 32;

    public static bool $registerCows = true;
    public static int $cowDespawnDistance = 128;
    public static int $cowNoDespawnDistance = 32;

    public static bool $registerChickens = true;
    public static int $chickenDespawnDistance = 128;
    public static int $chickenNoDespawnDistance = 32;

    public static bool $registerSheep = true;
    public static int $sheepDespawnDistance = 128;
    public static int $sheepNoDespawnDistance = 32;
    public static bool $sheepCanDestroyGrass = true;
}