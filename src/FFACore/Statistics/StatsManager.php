<?php

namespace FFACore\Statistics;

use FFACore\Provider\LevelDBProvider;

class StatsManager {

    private array $statsCache = [];

    public function __construct(private Main $plugin) {}

    public function addKill(string $playerName): void {
        $this->updateStats($playerName, function(array &$stats) {
            $stats['kills']++;
            $stats['score'] += 5;
            $stats['streak']++;
            $stats['max_streak'] = max($stats['max_streak'] ?? 0, $stats['streak']);
        });
    }

    public function addDeath(string $playerName): void {
        $this->updateStats($playerName, function(array &$stats) {
            $stats['deaths']++;
            $stats['score'] = max(0, $stats['score'] - 1);
            $stats['streak'] = 0;
        });
    }

    public function addWin(string $playerName): void {
        $this->updateStats($playerName, function(array &$stats) {
            $stats['wins']++;
            $stats['score'] += 10;
        });
    }

    public function addLoss(string $playerName): void {
        $this->updateStats($playerName, function(array &$stats) {
            $stats['losses']++;
        });
    }

    private function updateStats(string $playerName, callable $callback): void {
        $stats = $this->getPlayerStats($playerName);
        $callback($stats);
        $this->savePlayerStats($playerName, $stats);
    }

    public function getPlayerStats(string $playerName): array {
        $key = strtolower($playerName);
        
        if(!isset($this->statsCache[$key])) {
            $this->statsCache[$key] = $this->plugin->getProvider()->getPlayerStats($playerName);
            
            $this->statsCache[$key] += [
                'kills' => 0,
                'deaths' => 0,
                'wins' => 0,
                'losses' => 0,
                'score' => 0,
                'streak' => 0,
                'max_streak' => 0
            ];
        }
        
        return $this->statsCache[$key];
    }

    public function savePlayerStats(string $playerName, array $stats): void {
        $key = strtolower($playerName);
        $this->statsCache[$key] = $stats;
        $this->plugin->getProvider()->savePlayerStats($playerName, $stats);
    }

    public function getTopPlayers(string $stat, int $limit = 10): array {
        return $this->plugin->getProvider()->getTopStats($stat, $limit);
    }
}