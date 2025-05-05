<?php

namespace FFACore\Form;

use pocketmine\form\Form;
use pocketmine\player\Player;

class StatsForm implements Form {

    public function __construct(private Main $plugin, private Player $target) {}

    public function handleResponse(Player $player, $data): void {}

    public function jsonSerialize(): array {
        $stats = $this->plugin->getStatsManager()->getPlayerStats($this->target->getName());
        
        return [
            "type" => "custom_form",
            "title" => "§l§6Player Stats - " . $this->target->getName(),
            "content" => [
                [
                    "type" => "label",
                    "text" => "§aKills: §f" . $stats['kills'] . 
                             "\n§cDeaths: §f" . $stats['deaths'] . 
                             "\n§eK/D Ratio: §f" . ($stats['deaths'] > 0 ? 
                                 round($stats['kills'] / $stats['deaths'], 2) : $stats['kills']) .
                             "\n\n§6Score: §f" . $stats['score'] .
                             "\n§bCurrent Streak: §f" . $stats['streak']
                ]
            ]
        ];
    }
}