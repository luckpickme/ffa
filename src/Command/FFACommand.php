<?php

namespace FFA\Command;

use FFA\Arena\ArenaType;
use FFA\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FFACommand extends Command {
    
    public function __construct(private Main $plugin) {
        parent::__construct("ffa", "FFA command", "/ffa <mode>", ["freeforall"]);
        $this->setPermission("ffa.command.use");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can be used only in-game!");
            return false;
        }
        
        if(count($args) < 1) {
            $sender->sendMessage(TextFormat::YELLOW . "Available modes: " . implode(", ", $this->plugin->getKitManager()->getAvailableKits()));
            return false;
        }
        
        $mode = strtolower($args[0]);
        $kit = $this->plugin->getKitManager()->getKit($mode);
        
        if($kit === null) {
            $sender->sendMessage(TextFormat::RED . "Unknown FFA mode!");
            return false;
        }
        
        $arenaType = match($mode) {
            "nodebuff" => ArenaType::NODEBUFF,
            "sumo" => ArenaType::SUMO,
            default => null
        };
        
        if($arenaType === null) {
            $sender->sendMessage(TextFormat::RED . "Invalid arena type!");
            return false;
        }
        
        $arena = $this->plugin->getArenaManager()->getArenaForPlayer($sender, $arenaType);
        if($arena === null) {
            $sender->sendMessage(TextFormat::RED . "No available arenas!");
            return false;
        }
        
        $arena->addPlayer($sender);
        $this->plugin->getKitManager()->applyKit($sender, $mode);
        
        if(count($arena->getPlayers()) === 2) {
            $arena->startGame();
        }
        
        return true;
    }
}