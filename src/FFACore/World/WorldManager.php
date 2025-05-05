<?php

namespace FFACore\World;

use FFACore\World\Generator\VoidGenerator;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class WorldManager {

    public function __construct(private Main $plugin) {}

    public function createArenaWorld(string $name): void {
        $worldManager = $this->plugin->getServer()->getWorldManager();
        
        if(!$worldManager->isWorldGenerated($name)) {
            $options = new WorldCreationOptions();
            $options->setGeneratorClass(VoidGenerator::class);
            $worldManager->generateWorld($name, $options);
        }
    }

    public function loadWorld(string $name): bool {
        $worldManager = $this->plugin->getServer()->getWorldManager();
        
        if($worldManager->isWorldLoaded($name)) {
            return true;
        }
        
        if($worldManager->isWorldGenerated($name)) {
            return $worldManager->loadWorld($name);
        }
        
        return false;
    }

    public function unloadWorld(string $name): bool {
        $worldManager = $this->plugin->getServer()->getWorldManager();
        $world = $worldManager->getWorldByName($name);
        
        if($world instanceof World) {
            foreach($world->getPlayers() as $player) {
                $player->teleport($this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
            }
            return $worldManager->unloadWorld($world);
        }
        
        return false;
    }

    public function deleteWorld(string $name): bool {
        $worldManager = $this->plugin->getServer()->getWorldManager();
        
        if($worldManager->isWorldLoaded($name)) {
            $this->unloadWorld($name);
        }
        
        if($worldManager->isWorldGenerated($name)) {
            $worldManager->deleteWorld($name);
            return true;
        }
        
        return false;
    }
}