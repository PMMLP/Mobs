<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\pathfinder\evaluator;

use pmmlp\mobs\entity\Mob;
use pocketmine\math\Vector3;

abstract class NodeEvaluator {
    protected bool $canPassDoors = true;
    protected bool $canOpenDoors = false;
    protected bool $canFloat = true;
    protected bool $canWalkOverFences = false;
    protected bool $canWalkOverLava = false;

    public function __construct(
        protected Mob $mob
    ){}

    public function canPassDoors(): bool{
        return $this->canPassDoors;
    }

    public function setCanPassDoors(bool $canPassDoors): void{
        $this->canPassDoors = $canPassDoors;
    }

    public function canOpenDoors(): bool{
        return $this->canOpenDoors;
    }

    public function setCanOpenDoors(bool $canOpenDoors): void{
        $this->canOpenDoors = $canOpenDoors;
    }

    public function canFloat(): bool{
        return $this->canFloat;
    }

    public function setCanFloat(bool $canFloat): void{
        $this->canFloat = $canFloat;
    }

    public function canWalkOverFences(): bool{
        return $this->canWalkOverFences;
    }

    public function setCanWalkOverFences(bool $canWalkOverFences): void{
        $this->canWalkOverFences = $canWalkOverFences;
    }

    public function canWalkOverLava(): bool{
        return $this->canWalkOverLava;
    }

    public function setCanWalkOverLava(bool $canWalkOverLava): void{
        $this->canWalkOverLava = $canWalkOverLava;
    }

    abstract public function evaluate(Vector3 $current, Vector3 $side): ?Vector3;
}