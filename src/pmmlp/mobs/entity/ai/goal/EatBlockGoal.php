<?php

declare(strict_types=1);

namespace pmmlp\mobs\entity\ai\goal;

use pmmlp\mobs\util\MobsConfig;
use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\world\particle\BlockBreakParticle;

class EatBlockGoal extends Goal {
    protected int $eatAnimationTick = 0;

    public function initFlags(): void{
        $this->addFlags(Flags::MOVE, Flags::LOOK, Flags::JUMP);
    }

    public function canUse(): bool{
        if(random_int(0, ($this->mob->isBaby() ? 50 : 1000)) !== 0) {
            return false;
        }
        return $this->mob->getWorld()->getBlock($this->mob->getPosition()->down())->isSameType(VanillaBlocks::GRASS());
    }

    protected function start(): void{
        $this->eatAnimationTick = 40;
        $this->mob->getWorld()->broadcastPacketToViewers($this->mob->getPosition(), ActorEventPacket::create($this->mob->getId(),ActorEvent::EAT_GRASS_ANIMATION, 0));
        $this->mob->getNavigation()->stop();
    }

    protected function stop(): void{
        $this->eatAnimationTick = 0;
    }

    public function canContinueToUse(): bool{
        return $this->eatAnimationTick > 0;
    }

    public function tick(): void{
        $this->mob->getNavigation()->stop();

        $this->eatAnimationTick--;
        if($this->eatAnimationTick <= 0) {
            $world = $this->mob->getWorld();
            $position = $this->mob->getPosition()->down();
            if($world->getBlock($position)->isSameType(VanillaBlocks::GRASS())) {
                $world->addParticle($position, new BlockBreakParticle(VanillaBlocks::GRASS()));
                if(MobsConfig::$sheepCanDestroyGrass) {
                    $world->setBlock($position, VanillaBlocks::DIRT());
                }
                $this->mob->ate();
            }
        }
    }
}