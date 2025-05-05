<?php

namespace FFACore\Chat;

use FFACore\Punishments\PunishmentManager;
use FFACore\Rank\RankManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ChatManager {

    private bool $globalMute = false;
    private array $mutedPlayers = [];

    public function __construct(
        private Main $plugin,
        private RankManager $rankManager,
        private PunishmentManager $punishmentManager
    ) {}

    public function setGlobalMute(bool $value): void {
        $this->globalMute = $value;
    }

    public function isGlobalMute(): bool {
        return $this->globalMute;
    }

    public function mutePlayer(string $playerName, int $minutes): void {
        $this->mutedPlayers[strtolower($playerName)] = time() + ($minutes * 60);
    }

    public function unmutePlayer(string $playerName): void {
        unset($this->mutedPlayers[strtolower($playerName)]);
    }

    public function isMuted(Player $player): bool {
        return $this->globalMute || 
               isset($this->mutedPlayers[strtolower($player->getName())]) || 
               $this->punishmentManager->isMuted($player);
    }

    public function getMuteTimeLeft(Player $player): int {
        $name = strtolower($player->getName());
        if(isset($this->mutedPlayers[$name])) {
            return $this->mutedPlayers[$name] - time();
        }
        return $this->punishmentManager->getMuteTime($player);
    }

    public function formatMessage(Player $sender, string $message): string {
        if($this->isMuted($sender)) {
            $timeLeft = $this->getMuteTimeLeft($sender);
            $sender->sendMessage(TextFormat::RED . "You are muted for " . $timeLeft . " seconds");
            return "";
        }

        $rank = $this->rankManager->getPlayerRank($sender);
        return TextFormat::colorize(sprintf(
            "%s %s§f: %s",
            $rank->getPrefix(),
            $sender->getDisplayName(),
            $message
        ));
    }
}