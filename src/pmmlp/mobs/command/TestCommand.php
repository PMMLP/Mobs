<?php

declare(strict_types=1);

namespace pmmlp\mobs\command;

use pmmlp\mobs\entity\animal\Pig;
use pmmlp\mobs\util\BlockFinder;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TestCommand extends Command {
    public function __construct(){
        parent::__construct("test");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player) {
            return;
        }
        if(isset($args[0])) {
            $ms = microtime(true);
            $block = BlockFinder::findBlockType($sender->getPosition(), VanillaBlocks::WATER(), (int)($args[0] ?? 4), (int)($args[1] ?? -1));
            $sender->sendMessage("Took ".round(microtime(true) - $ms, 5)."ms");
            if($block === null) {
                $sender->sendMessage("Block not found");
                return;
            }
            $sender->getWorld()->setBlock($block->getPosition(), VanillaBlocks::CONCRETE());
            $sender->sendMessage("Block found!");
            return;
        }

        $location = $sender->getLocation();
        $location->pitch = 0.0;
        $entity = new Pig($location);
        $entity->spawnToAll();

        //$sender->sendMessage("Height: ".BlockHeightCalculator::getMaxY($sender->getTargetBlock(10)));
    }
}