<?php

declare(strict_types=1);

namespace pmmlp\mobs\util;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;

class NotPathfindableBlockList {
    /** @var int[]  */
    private static ?array $list = null;

    public static function contains(Block $block): bool {
        if(self::$list === null) {
            $blocks = [
                VanillaBlocks::ANVIL(),
                VanillaBlocks::MOB_HEAD(),
                VanillaBlocks::BAMBOO_SAPLING(),
                VanillaBlocks::BED(),
                VanillaBlocks::BELL(),
                VanillaBlocks::BREWING_STAND(),
                VanillaBlocks::CACTUS(),
                VanillaBlocks::CAKE(),
                VanillaBlocks::CHEST(),
                VanillaBlocks::COCOA_POD(),
                VanillaBlocks::DRAGON_EGG(),
                VanillaBlocks::ENCHANTING_TABLE(),
                VanillaBlocks::END_PORTAL_FRAME(),
                VanillaBlocks::ENDER_CHEST(),
                VanillaBlocks::FLOWER_POT(),
                VanillaBlocks::HOPPER(),
                VanillaBlocks::LANTERN(),
                VanillaBlocks::LECTERN(),
                VanillaBlocks::END_ROD(),
                VanillaBlocks::SEA_PICKLE(),
                VanillaBlocks::RAIL(),
                VanillaBlocks::ACTIVATOR_RAIL(),
                VanillaBlocks::DETECTOR_RAIL(),
                VanillaBlocks::POWERED_RAIL(),
                VanillaBlocks::FIRE(),
            ];
            foreach($blocks as $entry) {
                self::$list[] = $entry->getFullId();
            }
        }
        return in_array($block->getFullId(), self::$list, false);
    }
}