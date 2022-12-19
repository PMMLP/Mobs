<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\navigation;

use pmmlp\mobs\entity\ai\pathfinder\Path;
use pmmlp\mobs\entity\Mob;
use pocketmine\math\Vector3;
use pocketmine\Server;

abstract class PathNavigation {
    protected ?Path $path = null;
    protected ?Vector3 $node = null;

    protected bool $done = true;

    protected bool $recalculateWhenStuck = true;
    protected ?float $customSpeed = null;

    protected int $stuckTicks = 0;
    protected ?Vector3 $lastPosition = null;

    protected ?Vector3 $target = null;

    protected int $nextRecomputeTick = 0;

    public function __construct(
        protected Mob $mob
    ){}

    public function getNode(): ?Vector3{
        return $this->node;
    }

    public function internalTick(): void {
        $this->tick();
       if(!$this->done && $this->target !== null) {
           $mob = $this->mob;
           $position = $mob->getPosition();
           if($this->lastPosition !== null && $position->distanceSquared($this->lastPosition) <= 0.01){
               if(++$this->stuckTicks > 30) {
                   if($this->recalculateWhenStuck) {
                       $this->recalculatePath();
                       return;
                   }
                   $this->stop();
                   return;
               }
           } else {
               $this->stuckTicks = 0;
           }
           $this->lastPosition = $position;
       }
    }

   abstract public function tick(): void;

    public function findPath(Vector3 $target, bool $recomputeWhenStuck = true, ?float $customSpeed = null): void {
        $this->target = $target;
        $this->recalculateWhenStuck = $recomputeWhenStuck;
        $this->customSpeed = $customSpeed;
        $this->stuckTicks = 0;
        $this->node = null;
        $this->done = false;
        if($this->mob->getPosition()->distanceSquared($target) > 1.0) {
            $this->path = $this->mob->getPathFinder()->findPath($target);
            $this->path?->pop();
        } else {
            $this->path = null;
        }
        $this->scheduleRecompute();
    }

    public function recalculatePath(): void {
        $this->findPath($this->target, $this->recalculateWhenStuck, $this->customSpeed);
    }

    public function stop(): void {
        $this->target = null;
        $this->done = true;
        $this->path = null;
    }

    public function scheduleRecompute(int $ticks = 40): void {
        $this->nextRecomputeTick = Server::getInstance()->getTick() + $ticks;
    }

    public function isDone(): bool{
        return $this->done;
    }
}