<?php

namespace FFACore\Query;

use FFACore\Arena\ArenaManager;

class QueryManager {

    private array $queryData = [];

    public function __construct(private Main $plugin) {
        $this->updateQueryData();
    }

    public function updateQueryData(): void {
        $arenaManager = $this->plugin->getArenaManager();
        
        $this->queryData = [
            "map" => "FFA Arena",
            "players" => count($this->plugin->getServer()->getOnlinePlayers()),
            "max_players" => $this->plugin->getServer()->getMaxPlayers(),
            "arenas" => [
                "total" => $arenaManager->getArenaCount(),
                "active" => $arenaManager->getActiveArenaCount(),
                "types" => $arenaManager->getArenaTypes()
            ],
            "version" => $this->plugin->getDescription()->getVersion(),
            "motd" => $this->plugin->getConfig()->get("motd", "FFA Server")
        ];
    }

    public function getQueryData(): array {
        return $this->queryData;
    }

    public function getFormattedMotd(): string {
        $data = $this->queryData;
        return sprintf(
            "§l§e%s\n§r§fPlayers: §a%d/%d\n§fArenas: §b%d/%d",
            $data['motd'],
            $data['players'],
            $data['max_players'],
            $data['arenas']['active'],
            $data['arenas']['total']
        );
    }
}