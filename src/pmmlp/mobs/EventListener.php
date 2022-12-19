<?php

declare(strict_types=1);

namespace pmmlp\mobs;

use pmmlp\mobs\entity\animal\Chicken;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {
    public function onProjectileHit(ProjectileHitEvent $event): void {
        if($event->getEntity() instanceof Egg){
            if(random_int(0, 8) === 0) {
                $amount = 1;
                if(random_int(0, 32) === 0) {
                    $amount = 4;
                }

                $position = $event->getEntity()->getPosition();
                for($i = 1; $i <= $amount; $i++) {
                    $chicken = new Chicken(Location::fromObject($position, $position->world, random_int(0, 359), 0));
                    $chicken->setBaby(true);
                    $chicken->spawnToAll();
                }
            }
        }
    }
}