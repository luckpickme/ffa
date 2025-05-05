<?php

namespace FFACore\Session;

use pocketmine\player\Player;
use FFACore\Provider\LevelDBProvider;

class SessionManager {

    /** @var Session[] */
    private array $sessions = [];

    public function __construct(private Main $plugin) {}

    public function createSession(Player $player): void {
        $name = $player->getName();
        $data = $this->plugin->getProvider()->getPlayerData($name);
        
        $this->sessions[$name] = new Session(
            $name,
            $player->getNetworkSession()->getIp(),
            $data['rank'] ?? 'default',
            $data['stats'] ?? []
        );
    }

    public function endSession(Player $player): void {
        $name = $player->getName();
        if(isset($this->sessions[$name])) {
            $session = $this->sessions[$name];
            $this->saveSessionData($session);
            unset($this->sessions[$name]);
        }
    }

    private function saveSessionData(Session $session): void {
        $data = [
            'rank' => $session->getRank(),
            'stats' => $session->getStats(),
            'last_login' => time()
        ];
        $this->plugin->getProvider()->savePlayerData($session->getPlayerName(), $data);
    }

    public function getSession(Player $player): ?Session {
        return $this->sessions[$player->getName()] ?? null;
    }

    public function updateAllSessions(): void {
        foreach($this->sessions as $session) {
            $this->saveSessionData($session);
        }
    }
}