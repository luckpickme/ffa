<?php

namespace FFACore\Provider;

use LevelDB;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class LevelDBProvider {

    private \LevelDB $db;

    public function __construct(private Main $plugin) {
        $path = $this->plugin->getDataFolder() . "data/";
        if(!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        $this->db = new \LevelDB($path, [
            "create_if_missing" => true,
            "compression" => LEVELDB_SNAPPY_COMPRESSION
        ]);
    }

    public function getPlayerData(string $name): array {
        $data = $this->db->get("player_" . strtolower($name));
        return $data ? json_decode($data, true) : [];
    }

    public function savePlayerData(string $name, array $data): void {
        $this->db->put("player_" . strtolower($name), json_encode($data));
    }

    public function getStatsData(): array {
        $stats = [];
        $it = new \LevelDBIterator($this->db);
        foreach($it as $key => $value) {
            if(strpos($key, "player_") === 0) {
                $stats[substr($key, 7)] = json_decode($value, true);
            }
        }
        return $stats;
    }

    public function close(): void {
        unset($this->db);
    }
}