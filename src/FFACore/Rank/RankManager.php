<?php

namespace FFACore\Rank;

use FFACore\Provider\LevelDBProvider;
use pocketmine\player\Player;

class RankManager {

/** @var Rank[] */
private array $ranks = [];

public function __construct(private Main $plugin) {
    $this->loadRanks();
}

private function loadRanks(): void {
    $config = $this->plugin->getConfig()->get("ranks", []);
    
    foreach($config as $id => $data) {
        $this->ranks[$id] = new Rank(
            $id,
            $data["name"] ?? $id,
            $data["prefix"] ?? "",
            $data["nameTagFormat"] ?? "{prefix}{name}",
            $data["permissions"] ?? []
        );
    }
}

public function getPlayerRank(Player $player): Rank {
    $data = $this->plugin->getProvider()->getPlayerData($player->getName());
    $rankId = $data["rank"] ?? "default";
    
    return $this->ranks[$rankId] ?? $this->ranks["default"];
}

public function setPlayerRank(Player $player, string $rankId): bool {
    if(!isset($this->ranks[$rankId])) {
        return false;
    }
    
    $this->plugin->getProvider()->setPlayerRank($player->getName(), $rankId);
    return true;
}

public function getAvailableRanks(): array {
    return $this->ranks;
}

public function formatNameTag(Player $player): string {
    $rank = $this->getPlayerRank($player);
    $format = $rank->getNameTagFormat();
    
    return str_replace(
        ["{prefix}", "{name}", "{score}"],
        [$rank->getPrefix(), $player->getName(), $player->getScore()],
        $format
    );
}
}