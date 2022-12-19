<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\pathfinder;

use pocketmine\math\Vector3;

class Path {
    /** @var Vector3[]  */
    private array $pathPoints = [];

    private float $time = 0.0;

    public function __construct(
        private Vector3 $startVector3,
        private Vector3 $targetVector3
    ){}

    public function getStartVector3(): Vector3{
        return $this->startVector3;
    }

    public function getTargetVector3(): Vector3{
        return $this->targetVector3;
    }

    public function getTime(): float{
        return $this->time;
    }

    public function setTime(float $time): void{
        $this->time = $time;
    }

    public function getPathPoints(): array{
        return $this->pathPoints;
    }

    public function addPathPoint(Vector3 $pathPoint): void {
        $this->pathPoints[] = $pathPoint;
    }

    public function pop(): ?Vector3 {
        return array_pop($this->pathPoints);
    }
}