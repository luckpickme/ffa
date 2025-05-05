<?php

namespace FFACore\Arena;

use pocketmine\player\Player;
use pocketmine\world\World;

class ArenaManager {
    
    /** @var GameArena[] */
    private array $arenas = [];
    
    public function __construct(private \FFACore\Main $plugin) {
        $this->loadArenas();
    }
    
    private function loadArenas(): void {
        $config = $this->plugin->getConfig();
        
        foreach($config->get("arenas", []) as $arenaId => $arenaData) {
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($arenaData["world"]);
            $pos = new Position(
                $arenaData["spawn"]["x"],
                $arenaData["spawn"]["y"],
                $arenaData["spawn"]["z"],
                $world
            );
            
            $this->arenas[$arenaId] = new GameArena(
                $arenaId,
                ArenaType::from($arenaData["type"]),
                $pos
            );
        }
    }
    
    public function getArenaForPlayer(Player $player, ArenaType $type): ?GameArena {
        foreach($this->arenas as $arena) {
            if($arena->getType() === $type && count($arena->getPlayers()) < 2) {
                return $arena;
            }
        }
        return null;
    }
    
    public function createArena(string $id, ArenaType $type, Position $spawn): void {
        $this->arenas[$id] = new GameArena($id, $type, $spawn);
    }
}