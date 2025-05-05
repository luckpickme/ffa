<?php

namespace FFACore\Form;

use FFACore\Arena\ArenaManager;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ArenaSelectorForm implements Form {

    public function __construct(private Main $plugin) {}

    public function handleResponse(Player $player, $data): void {
        if($data === null) return;
        
        $arenaManager = $this->plugin->getArenaManager();
        $arenas = $arenaManager->getAvailableArenas();
        
        if(isset($arenas[$data])) {
            $arenaManager->addPlayerToArena($player, $arenas[$data]);
        }
    }

    public function jsonSerialize(): array {
        $arenas = $this->plugin->getArenaManager()->getAvailableArenas();
        $buttons = [];
        
        foreach($arenas as $id => $arena) {
            $buttons[] = [
                "text" => "§l§e" . $arena->getType() . " Arena\n§r§7ID: §f" . $id . 
                          "\n§7Players: §f" . count($arena->getPlayers()) . "/2"
            ];
        }
        
        return [
            "type" => "form",
            "title" => "§l§6Arena Selector",
            "content" => "§7Select an arena to join:",
            "buttons" => $buttons
        ];
    }
}