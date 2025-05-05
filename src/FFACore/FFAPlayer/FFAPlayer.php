<?php

namespace FFACore\FFAPlayer;

use FFACore\Session\Session;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;

class FFAPlayer extends Player {

    private Session $session;
    private string $currentArena = "";
    private int $kills = 0;
    private int $deaths = 0;
    private int $score = 0;
    private bool $vanished = false;
    private bool $inCombat = false;
    private int $combatTime = 0;

    public function __construct(PlayerInfo $playerInfo) {
        parent::__construct($playerInfo);
    }

    public function initSession(Session $session): void {
        $this->session = $session;
    }

    public function getSession(): Session {
        return $this->session;
    }

    public function getCurrentArena(): string {
        return $this->currentArena;
    }

    public function setCurrentArena(string $arenaId): void {
        $this->currentArena = $arenaId;
    }

    public function addKill(): void {
        $this->kills++;
        $this->score += 5;
    }

    public function addDeath(): void {
        $this->deaths++;
        $this->score = max(0, $this->score - 1);
    }

    public function getKills(): int {
        return $this->kills;
    }

    public function getDeaths(): int {
        return $this->deaths;
    }

    public function getScore(): int {
        return $this->score;
    }

    public function isVanished(): bool {
        return $this->vanished;
    }

    public function setVanished(bool $value): void {
        $this->vanished = $value;
        $this->onVanishUpdate();
    }

    private function onVanishUpdate(): void {
        foreach($this->getServer()->getOnlinePlayers() as $player) {
            if($this->vanished) {
                $player->hidePlayer($this);
            } else {
                $player->showPlayer($this);
            }
        }
    }

    public function isInCombat(): bool {
        return $this->inCombat && ($this->combatTime > time());
    }

    public function setInCombat(int $seconds = 15): void {
        $this->inCombat = true;
        $this->combatTime = time() + $seconds;
    }

    public function clearCombat(): void {
        $this->inCombat = false;
        $this->combatTime = 0;
    }
}