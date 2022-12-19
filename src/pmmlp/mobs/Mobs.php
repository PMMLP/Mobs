<?php

declare(strict_types=1);

namespace pmmlp\mobs;

use pmmlp\mobs\command\TestCommand;
use pmmlp\mobs\entity\animal\Chicken;
use pmmlp\mobs\entity\animal\Cow;
use pmmlp\mobs\entity\animal\Pig;
use pmmlp\mobs\entity\animal\Sheep;
use pmmlp\mobs\entity\Mob;
use pmmlp\mobs\entity\spawning\rule\RequiredBlockTypesSpawnRule;
use pmmlp\mobs\entity\spawning\rule\RequiredLightLevelSpawnRule;
use pmmlp\mobs\entity\spawning\rule\RequiredSpaceSpawnRule;
use pmmlp\mobs\entity\spawning\SpawnPlacements;
use pmmlp\mobs\util\MobsConfig;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier as IID;
use pocketmine\item\ItemIds as Ids;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\World;

class Mobs extends PluginBase {
    protected function onLoad(): void{
        new MobsConfig($this);

        $this->registerMobs();
    }

    protected function onEnable(): void{
        Server::getInstance()->getCommandMap()->register("mobs", new TestCommand());

        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    protected function registerMobs(): void {
        /** @var EntityFactory $factory */
        $factory = EntityFactory::getInstance();
        /** @var ItemFactory $itemFactory */
        $itemFactory = ItemFactory::getInstance();

        if(MobsConfig::$registerPigs) {
            $factory->register(Pig::class, function(World $world, CompoundTag $tag): Mob{
                return new Pig(EntityDataHelper::parseLocation($tag, $world), $tag);
            }, ["minecraft:pig", "Pig"], EntityLegacyIds::PIG);

            $itemFactory->register(new class(new IID(Ids::SPAWN_EGG, EntityLegacyIds::PIG), "Pig Spawn Egg") extends SpawnEgg{
                public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                    return new Pig(Location::fromObject($pos, $world, $yaw, $pitch));
                }
            });

            SpawnPlacements::register(Pig::class, new RequiredLightLevelSpawnRule(7), new RequiredBlockTypesSpawnRule(VanillaBlocks::GRASS()), new RequiredSpaceSpawnRule());
        }

        if(MobsConfig::$registerCows) {
            $factory->register(Cow::class, function(World $world, CompoundTag $tag): Mob{
                return new Cow(EntityDataHelper::parseLocation($tag, $world), $tag);
            }, ["minecraft:cow", "Cow"], EntityLegacyIds::COW);

            $itemFactory->register(new class(new IID(Ids::SPAWN_EGG, EntityLegacyIds::COW), "Cow Spawn Egg") extends SpawnEgg{
                public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                    return new Cow(Location::fromObject($pos, $world, $yaw, $pitch));
                }
            });

            SpawnPlacements::register(Cow::class, new RequiredLightLevelSpawnRule(9), new RequiredBlockTypesSpawnRule(VanillaBlocks::GRASS()), new RequiredSpaceSpawnRule());
        }

        if(MobsConfig::$registerChickens) {
            $factory->register(Chicken::class, function(World $world, CompoundTag $tag): Chicken{
                return new Chicken(EntityDataHelper::parseLocation($tag, $world), $tag);
            }, ["minecraft:chicken", "Chicken"], EntityLegacyIds::CHICKEN);

            $itemFactory->register(new class(new IID(Ids::SPAWN_EGG, EntityLegacyIds::CHICKEN), "Chicken Spawn Egg") extends SpawnEgg{
                public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                    return new Chicken(Location::fromObject($pos, $world, $yaw, $pitch));
                }
            });

            SpawnPlacements::register(Chicken::class, new RequiredLightLevelSpawnRule(9), new RequiredBlockTypesSpawnRule(VanillaBlocks::GRASS()), new RequiredSpaceSpawnRule());
        }

        if(MobsConfig::$registerSheep) {
            $factory->register(Sheep::class, function(World $world, CompoundTag $tag): Mob{
                return new Sheep(EntityDataHelper::parseLocation($tag, $world), $tag);
            }, ["minecraft:sheep", "Sheep"], EntityLegacyIds::SHEEP);

            $itemFactory->register(new class(new IID(Ids::SPAWN_EGG, EntityLegacyIds::SHEEP), "Sheep Spawn Egg") extends SpawnEgg{
                public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                    return new Sheep(Location::fromObject($pos, $world, $yaw, $pitch));
                }
            });

            SpawnPlacements::register(Sheep::class, new RequiredLightLevelSpawnRule(7), new RequiredBlockTypesSpawnRule(VanillaBlocks::GRASS()), new RequiredSpaceSpawnRule());
        }
    }
}