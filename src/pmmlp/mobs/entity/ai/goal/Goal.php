<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\entity\Mob;

abstract class Goal {
    protected bool $running = false;

    protected Mob $mob;

    protected array $flags = [];

    public function __construct(
        protected int $priority
    ){}

    abstract public function initFlags(): void;

    public function getMob(): Mob{
        return $this->mob;
    }

    public function setMob(Mob $mob): void{
        $this->mob = $mob;
    }

    public function getPriority(): int{
        return $this->priority;
    }

    public function canBeReplacedBy(Goal $goal): bool {
        return $this->isInterruptable() && $goal->getPriority() < $this->getPriority();
    }

    public function isInterruptable(): bool {
        return true;
    }

    public function isRunning(): bool{
        return $this->running;
    }

    abstract public function canUse(): bool;

    public function canContinueToUse(): bool {
        return $this->canUse();
    }

    public function getFlags(): array{
        return $this->flags;
    }

    public function addFlag(int $flag): void {
        $this->flags[] = $flag;
    }

    public function addFlags(int ...$flags): void {
        foreach($flags as $flag) {
            $this->addFlag($flag);
        }
    }

    public function hasFlag(int $flag): bool {
        return in_array($flag, $this->flags, false);
    }

    protected function start(): void {}
    protected function stop(): void {}
    public function tick(): void {}

    public function internalStart(): void {
        if(!$this->running) {
            $this->running = true;
            $this->start();
        }
    }

    public function internalStop(): void {
        if($this->running) {
            $this->running = false;
            $this->stop();
        }
    }

    public function requiresUpdateEveryTick(): bool {
        return false;
    }
}