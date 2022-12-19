# Mobs
🐖Mob plugin for PocketMine-MP V4.*

# Features

```
✅️ Highly configurable
✅️ Easy to use
✅️ Vanilla like mob behavior
✅️ Made with 💖
```

# Note
This project takes quite a lot of time, and I probably can not do it all myself. Every help is welcome and appreciated :)

# Mob List

### Animals
| Name           | Implemented | Note                   |
|----------------|-----------|------------------------|
| `Allay`        | ❌         |
| `Axolotl`      | ❌         |
| `Bee`          | ❌         |
| `Cat`          | ❌         |
| `Camel`        | ❌         |
| `Cod`          | ❌         |
| `Cow`          | ✅         |
| `Chicken`      | ✅         | Jockey not implemented |
| `Dolphin`      | ❌         |
| `Fox`          | ❌         |
| `Frog`         | ❌         |
| `Goat`         | ❌         |
| `Horse`        | ❌         |
| `Iron Golem`   | ❌         |
| `Mushroom Cow` | ❌         |
| `Ocelot`       | ❌         |
| `Panda`        | ❌         |
| `Parrot`       | ❌         |
| `Pig`          | ✅         | Riding not implemented |
| `Polar Bear`   | ❌         |
| `Pufferfish`   | ❌         |
| `Rabbit`       | ❌         |
| `Salmon`       | ❌         |
| `Sheep`        | ✅         | `_jeb`  not implemented |
| `Snow Golem`   | ❌         |
| `Squid`        | ❌         |
| `Tropical Fish` | ❌         |
| `Turtle`       | ❌         |
| `Wolf`         | ❌         |

### Ambient
| Name  | Implemented | Note |
|-------|-----------|------|
| `Bat` | ❌         |

### Boss
| Name           | Implemented | Note |
|----------------|-----------|------|
| `Ender Dragon` | ❌         |
| `Wither`       | ❌         |

### Monster
| Name                  | Implemented | Note |
|-----------------------|-----------|------|
| `Blaze`               | ❌         |
| `Cave Spider`         | ❌         |
| `Creeper`             | ❌         |
| `Drowned`             | ❌         |
| `Elder Guardian`      | ❌         |
| `Ender Man`           | ❌         |
| `Endermite`           | ❌         |
| `Evoker`              | ❌         |
| `Ghast`               | ❌         |
| `Giant`               | ❌         |
| `Guardian`            | ❌         |
| `Hoglin`              | ❌         |
| `Husk`                | ❌         |
| `Illager`             | ❌         |
| `Illusioner`          | ❌         |
| `Magma Cube`          | ❌         |
| `Phantom`             | ❌         |
| `Piglin`              | ❌         |
| `Pillager`            | ❌         |
| `Shulker`             | ❌         |
| `Silverfish`          | ❌         |
| `Skeleton`            | ❌         |
| `Slime`               | ❌         |
| `Spellcaster Illager` | ❌         |
| `Spider`              | ❌         |
| `Stray`               | ❌         |
| `Strider`             | ❌         |
| `Vex`                 | ❌         |
| `Vindicator`          | ❌         |
| `Warden`              | ❌         |
| `Witch`               | ❌         |
| `Wither Skeleton`     | ❌         |
| `Zoglin`              | ❌         |
| `Zombie`              | ❌         |
| `Zombie Villager`     | ❌         |
| `Zombified Piglin`    | ❌         |

### NPC
| Name               | Implemented | Note |
|--------------------|-----------|------|
| `Villager`         | ❌         |
| `Wandering Trader` | ❌         |

### Vehicle
| Name                     | Implemented | Note                                       |
|--------------------------|-----------|--------------------------------------------|
| `Boat`                   | ❌         |
| `Chest Boat`             | ❌         |
| `Minecart`               | ❌         |
| `Minecart Chest`         | ❌         |
| `Minecart Command Block` | ❌         |
| `Minecart Furnace`       | ❌         |
| `Minecart Hopper`        | ❌         |
| `Minecart TNT`           | ❌         |
| `Minecart Spawner`       | ❌         | It´s a JE Feature, add with custom entity? |

# Missing Features

```
🚫 Leashes
🚫 Individual block cost
🚫 Natural spawning
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
# Set distance in which this mob won´t despawn (Applies to every mob)
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
