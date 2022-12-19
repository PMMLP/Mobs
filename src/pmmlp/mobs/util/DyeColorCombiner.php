<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use pocketmine\block\utils\DyeColor as Color;

class DyeColorCombiner {
    public static function combine(Color $color1, Color $color2, bool $checkReverse = true): ?Color {
        if($color1->equals(Color::GRAY()) && $color2->equals(Color::WHITE())) {
            return Color::LIGHT_GRAY();
        }
        if($color1->equals(Color::WHITE()) && $color2->equals(Color::BLACK())) {
            return Color::GRAY();
        }
        if($color1->equals(Color::RED()) && $color2->equals(Color::YELLOW())) {
            return Color::ORANGE();
        }
        if($color1->equals(Color::GREEN()) && $color2->equals(Color::WHITE())) {
            return Color::LIME();
        }
        if($color1->equals(Color::WHITE()) && $color2->equals(Color::BLUE())) {
            return Color::LIGHT_BLUE();
        }
        if($color1->equals(Color::BLUE()) && $color2->equals(Color::GREEN())) {
            return Color::CYAN();
        }
        if($color1->equals(Color::RED()) && $color2->equals(Color::BLUE())) {
            return Color::PURPLE();
        }
        if($color1->equals(Color::PURPLE()) && $color2->equals(Color::PINK())) {
            return Color::MAGENTA();
        }
        if($color1->equals(Color::WHITE()) && $color2->equals(Color::RED())) {
            return Color::PINK();
        }
        return $checkReverse ? self::combine($color2, $color1, false) : null;
    }
}