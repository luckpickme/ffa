<?php

namespace FFACore\Punishments;

use FFACore\Provider\LevelDBProvider;
use pocketmine\player\Player;

class PunishmentManager {

    private array $mutes = [];
    private array $bans = [];

    public function __construct(private Main $plugin) {}

    public function mutePlayer(string $playerName, int $minutes, string $reason = "", string $staff = "Console"): void {
        $time = time() + ($minutes * 60);
        $this->mutes[strtolower($playerName)] = [
            "time" => $time,
            "reason" => $reason,
            "staff" => $staff
        ];
        
        $this->plugin->getProvider()->savePunishment($playerName, "mute", $time, $reason, $staff);
    }

    public function unmutePlayer(string $playerName): bool {
        if(isset($this->mutes[strtolower($playerName)])) {
            unset($this->mutes[strtolower($playerName)]);
            $this->plugin->getProvider()->removePunishment($playerName, "mute");
            return true;
        }
        return false;
    }

    public function isMuted(Player $player): bool {
        $data = $this->mutes[strtolower($player->getName())] ?? 
                $this->plugin->getProvider()->getActivePunishment($player->getName(), "mute");
        
        return $data && $data["time"] > time();
    }

    public function getMuteInfo(string $playerName): array {
        return $this->mutes[strtolower($playerName)] ?? 
               $this->plugin->getProvider()->getActivePunishment($playerName, "mute") ?? [];
    }

    public function banPlayer(string $playerName, int $hours, string $reason = "", string $staff = "Console"): void {
        $time = time() + ($hours * 3600);
        $this->bans[strtolower($playerName)] = [
            "time" => $time,
            "reason" => $reason,
            "staff" => $staff
        ];
        
        $this->plugin->getProvider()->savePunishment($playerName, "ban", $time, $reason, $staff);
        
        $player = $this->plugin->getServer()->getPlayerExact($playerName);
        if($player instanceof Player) {
            $player->kick("§cYou are banned!\n§fReason: §e" . $reason . 
                         "\n§fExpires: §e" . date("Y-m-d H:i", $time) . 
                         "\n§fStaff: §e" . $staff);
        }
    }
}