<?php

namespace FFACore\Arena;

use pocketmine\player\Player;
use pocketmine\world\Position;

class GameArena {
    
    private string $id;
    private string $scoreboardTitle;

    private ArenaType $type;
    private Position $spawnPosition;

    private array $players = [];
    private array $scoreboardLines = [];
    
    private bool $isRunning = false;
    
    public function __construct(string $id, ArenaType $type, Position $spawnPosition) {
        $this->id = $id;
        $this->type = $type;
        $this->spawnPosition = $spawnPosition;
        $this->scoreboardTitle = "§l§e" . $type->getDisplayName() . " FFA";
        $this->updateDefaultScoreboard();
    }

    private function updateDefaultScoreboard(): void {
        $this->scoreboardLines = [
            "§7----------------",
            "§fMode: §a" . $this->type->getDisplayName(),
            "§fPlayers: §a" . count($this->players) . "/2",
            "§7----------------",
            "§fWaiting for opponent...",
            "§7----------------"
        ];
    }

    private function updateScoreboard(): void {
        $this->plugin->getScoreboardManager()->updateForArena(
            $this->players,
            $this->scoreboardTitle,
            $this->scoreboardLines
        );
    }
    
    public function getId(): string {
        return $this->id;
    }
    
    public function getType(): ArenaType {
        return $this->type;
    }
    
    public function addPlayer(Player $player): void {
        $this->players[$player->getName()] = $player;
        $player->teleport($this->spawnPosition);
        $player->sendMessage("You joined {$this->type->getDisplayName()} arena!");
        $this->updateDefaultScoreboard();
        $this->updateScoreboard();
    }
    
    public function removePlayer(Player $player): void {
        unset($this->players[$player->getName()]);
        $this->plugin->getScoreboardManager()->remove($player);
        $this->updateDefaultScoreboard();
        $this->updateScoreboard();
        // Вернуть игрока в лобби
    }
    
    public function startGame(): void {
        $this->isRunning = true;
        $this->startTime = time();
        
        foreach($this->players as $player) {
            $player->sendTitle("§l§cFIGHT!", "§eGood luck!", 10, 40, 10);
        }
        
        $this->updateGameScoreboard();
    }

    private function updateGameScoreboard(): void {
        $players = array_values($this->players);
        $player1 = $players[0] ?? null;
        $player2 = $players[1] ?? null;
        
        $this->scoreboardLines = [
            "§7----------------",
            "§fMode: §a" . $this->type->getDisplayName(),
            "§7----------------",
            "§fPlayer 1: §a" . ($player1 ? $player1->getName() : "None"),
            "§fPlayer 2: §a" . ($player2 ? $player2->getName() : "None"),
            "§7----------------",
            "§fTime: §a" . date("i:s", time() - $this->startTime),
            "§7----------------"
        ];
        
        $this->updateScoreboard();
    }

    public function update(): void {
        if($this->isRunning) {
            $this->updateGameScoreboard();
        }
    }
    
    public function endGame(Player $winner): void {
        $this->isRunning = false;
        foreach($this->players as $player) {
            $player->sendMessage("Game over! Winner: " . $winner->getName());
        }
    }
}