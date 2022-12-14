# Mobs
ðMob plugin for PocketMine-MP V4.*

# Features

```
âï¸ Highly configurable
âï¸ Easy to use
âï¸ Vanilla like mob behavior
âï¸ Made with ð
```

# Note
This project takes quite a lot of time, and I probably can not do it all myself. Every help is welcome and appreciated :)

This plugin is not meant to be efficient.
Here is a [timing](https://timings.pmmp.io/?id=250774) with 20 mobs

# Mob List

### Animals
| Name           | Implemented | Note                   |
|----------------|-----------|------------------------|
| `Allay`        | â         |
| `Axolotl`      | â         |
| `Bee`          | â         |
| `Cat`          | â         |
| `Camel`        | â         |
| `Cod`          | â         |
| `Cow`          | â         |
| `Chicken`      | â         | Jockey not implemented |
| `Dolphin`      | â         |
| `Fox`          | â         |
| `Frog`         | â         |
| `Goat`         | â         |
| `Horse`        | â         |
| `Iron Golem`   | â         |
| `Mushroom Cow` | â         |
| `Ocelot`       | â         |
| `Panda`        | â         |
| `Parrot`       | â         |
| `Pig`          | â         | Riding not implemented |
| `Polar Bear`   | â         |
| `Pufferfish`   | â         |
| `Rabbit`       | â         |
| `Salmon`       | â         |
| `Sheep`        | â         | `_jeb`  not implemented |
| `Snow Golem`   | â         |
| `Squid`        | â         |
| `Tropical Fish` | â         |
| `Turtle`       | â         |
| `Wolf`         | â         |

### Ambient
| Name  | Implemented | Note |
|-------|-----------|------|
| `Bat` | â         |

### Boss
| Name           | Implemented | Note |
|----------------|-----------|------|
| `Ender Dragon` | â         |
| `Wither`       | â         |

### Monster
| Name                  | Implemented | Note |
|-----------------------|-----------|------|
| `Blaze`               | â         |
| `Cave Spider`         | â         |
| `Creeper`             | â         |
| `Drowned`             | â         |
| `Elder Guardian`      | â         |
| `Ender Man`           | â         |
| `Endermite`           | â         |
| `Evoker`              | â         |
| `Ghast`               | â         |
| `Giant`               | â         |
| `Guardian`            | â         |
| `Hoglin`              | â         |
| `Husk`                | â         |
| `Illager`             | â         |
| `Illusioner`          | â         |
| `Magma Cube`          | â         |
| `Phantom`             | â         |
| `Piglin`              | â         |
| `Pillager`            | â         |
| `Shulker`             | â         |
| `Silverfish`          | â         |
| `Skeleton`            | â         |
| `Slime`               | â         |
| `Spellcaster Illager` | â         |
| `Spider`              | â         |
| `Stray`               | â         |
| `Strider`             | â         |
| `Vex`                 | â         |
| `Vindicator`          | â         |
| `Warden`              | â         |
| `Witch`               | â         |
| `Wither Skeleton`     | â         |
| `Zoglin`              | â         |
| `Zombie`              | â         |
| `Zombie Villager`     | â         |
| `Zombified Piglin`    | â         |

### NPC
| Name               | Implemented | Note |
|--------------------|-----------|------|
| `Villager`         | â         |
| `Wandering Trader` | â         |

### Vehicle
| Name                     | Implemented | Note                                       |
|--------------------------|-----------|--------------------------------------------|
| `Boat`                   | â         |
| `Chest Boat`             | â         |
| `Minecart`               | â         |
| `Minecart Chest`         | â         |
| `Minecart Command Block` | â         |
| `Minecart Furnace`       | â         |
| `Minecart Hopper`        | â         |
| `Minecart TNT`           | â         |
| `Minecart Spawner`       | â         | ItÂ´s a JE Feature, add with custom entity? |

# Missing Features

```
ð« Leashes
ð« Individual block cost
ð« Natural spawning
```


# Requires

[libPMMLP](https://github.com/PMMLP/libPMMLP)

# Screenshots

![Animals](https://github.com/PMMLP/Mobs/blob/V1.0.0/images/animals.png)

# Config

```
# Set the maximum pathfinder iterations (Higher => More CPU, more precise pathfinding)
maxPathfinderIterations: 24

# Set how much random positions should be generated (Higher => More CPU, better positions)
maxRandomPositionGeneratorIterations: 8

# Set if mob should be registered (Applies to every mob)
registerPigs: true
# Set at which distance this mob should be despawned (Applies to every mob)
pigDespawnDistance: 128
# Set distance in which this mob wonÂ´t despawn (Applies to every mob)
pigNoDespawnDistance: 32

registerCows: true
cowDespawnDistance: 128
cowNoDespawnDistance: 32

registerChickens: true
chickenDespawnDistance: 128
chickenNoDespawnDistance: 32

registerSheep: true
sheepDespawnDistance: 128
sheepNoDespawnDistance: 32

# Set if sheep can destroy grass while eating
sheepCanDestroyGrass: true


# Do not touch!
version: 1.0.0

```

# Donate

[Patreon](https://patreon.com/Matze998)

# Credits

This plugin is basically a copy of Minecraft JE mob system

Some parts where taken from [Altay](https://github.com/TuranicTeam/Altay) and from an old project of mine


Made by Matze, Dezember 2022
