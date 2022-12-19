<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use http\Exception\RuntimeException;
use pmmlp\mobs\entity\animal\Animal;
use pmmlp\mobs\entity\Mob;
use pocketmine\item\Item;
use pocketmine\player\Player;

class TemptGoal extends Goal {
    protected int $calmDown = 0;

    protected ?Player $target = null;

    /** @var Animal  */
    protected Mob $mob;

    public function setMob(Mob $mob): void{
        if(!$mob instanceof Animal) {
            throw new RuntimeException("Mob has to be an instance of Animal");
        }
        $this->mob = $mob;
    }

    public function initFlags(): void{
        $this->addFlags(Flags::LOOK, Flags::MOVE);
    }

    /**
     * @param Item[]|null $items
     */
    public function __construct(
        int $priority,
        protected float $speed,
        protected bool $canScare,
        protected ?array $items = null
    ){
        parent::__construct($priority);
    }

    public function canUse(): bool{
        if($this->calmDown > 0) {
            $this->calmDown--;
            return false;
        }
        $this->target = $this->mob->getWorld()->getNearestEntity($this->mob->getPosition(), 10, Player::class);
        return $this->target !== null && $this->isTemptable($this->target->getInventory()->getItemInHand());
    }

    protected function isTemptable(Item $item): bool {
        if($this->items !== null) {
            foreach($this->items as $food) {
                if($food->equals($item)) {
                    return true;
                }
            }
            return false;
        }
        return $this->mob->isFood($item);
    }

    public function canContinueToUse(): bool{
        if($this->canScare) {
            //TODO: This is for foxes
            //if($this->mob->getPosition()->distanceSquared($this->target->getPosition()) < 36) {}
        }
        return $this->canUse();
    }

    protected function stop(): void{
        $this->mob->getNavigation()->stop();
        $this->mob->getLookControl()->setTarget(null);
        $this->calmDown = 100;
        $this->target = null;
    }

    public function tick(): void{
        $this->mob->getLookControl()->setTarget($this->target->getPosition());
        if($this->mob->getPosition()->distanceSquared($this->target->getPosition()) < 6.25) {
            $this->mob->getNavigation()->stop();
            return;
        }
        $this->mob->getNavigation()->findPath($this->target->getPosition(), true, $this->speed);
    }
}