<?php

declare(strict_types=1);

namespace pmmlp\mobs\world\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\sound\Sound;

class PlaySound implements Sound {
    public function __construct(
        protected string $soundName,
        protected float $volume = 1.0,
        protected float $pitch = 1.0
    ){}


    public function encode(?Vector3 $pos): array{
        $pk = new PlaySoundPacket();
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->soundName = $this->soundName;
        $pk->volume = $this->volume;
        $pk->pitch = $this->pitch;
        return [$pk];
    }
}