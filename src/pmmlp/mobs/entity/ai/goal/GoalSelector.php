<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\entity\Mob;

class GoalSelector {
    /** @var Goal[]  */
    protected array $availableGoals = [];

    public function __construct(
        protected Mob $mob
    ){}

    public function getMob(): Mob{
        return $this->mob;
    }

    public function addGoal(Goal $goal): void {
        $goal->setMob($this->getMob());
        $this->availableGoals[] = $goal;
        $goal->initFlags();
    }

    public function removeAllGoals(): void {
        $this->availableGoals = [];
    }

    public function getAvailableGoals(): array{
        return $this->availableGoals;
    }

    public function tick(): void {
        $goals = $this->getAvailableGoals();

        foreach($goals as $goal) {
            if($goal->isRunning() && !$goal->canContinueToUse()) {
                $goal->internalStop();
            }

            if(!$goal->isRunning() && $goal->canUse()) {
                $canStart = true;
                foreach($goal->getFlags() as $flag) {
                    foreach(array_filter($goals, function(Goal $goal) use ($flag): bool {
                        return $goal->isRunning() && $goal->hasFlag($flag);
                    }) as $runningGoal) {
                        if($runningGoal->canBeReplacedBy($goal)) {
                            $runningGoal->internalStop();
                        } else {
                            $canStart = false;
                            break 2;
                        }
                    }
                }
                if($canStart) {
                    $goal->internalStart();
                }
            }

            if($goal->isRunning() || $goal->requiresUpdateEveryTick()) {
                $goal->tick();
            }
        }
    }
}