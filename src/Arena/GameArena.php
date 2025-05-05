<?php

namespace FFA\Arena;

use pocketmine\player\Player;
use pocketmine\world\Position;

class GameArena {
    
    private string $id;
    private ArenaType $type;
    private Position $spawnPosition;
    private array $players = [];
    private bool $isRunning = false;
    
    public function __construct(string $id, ArenaType $type, Position $spawnPosition) {
        $this->id = $id;
        $this->type = $type;
        $this->spawnPosition = $spawnPosition;
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
        $player->sendMessage("You joined {$this->type->getDisplayName()} mode!");
    }
    
    public function removePlayer(Player $player): void {
        unset($this->players[$player->getName()]);
        // to lobby
    }
    
    public function startGame(): void {
        $this->isRunning = true;
        foreach($this->players as $player) {
            $player->sendTitle("FIGHT!", "", 10, 40, 10);
        }
    }
    
    public function endGame(Player $winner): void {
        $this->isRunning = false;
        foreach($this->players as $player) {
            $player->sendMessage("Game over! Winner: " . $winner->getName());
        }
    }
}