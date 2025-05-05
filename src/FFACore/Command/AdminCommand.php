<?php

namespace FFACore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AdminCommand extends Command {

    public function __construct(private Main $plugin) {
        parent::__construct("ffaadmin", "FFA Admin commands", "/ffaadmin <setspawn|createarena>");
        $this->setPermission("ffa.admin");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return false;
        }

        if(count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }

        switch(strtolower($args[0])) {
            case "setspawn":
                $this->handleSetSpawn($sender);
                break;
                
            case "createarena":
                $this->handleCreateArena($sender, $args);
                break;
                
            default:
                $sender->sendMessage(TextFormat::RED . "Unknown subcommand");
                return false;
        }

        return true;
    }

    private function handleSetSpawn(Player $player): void {
        $config = $this->plugin->getConfig();
        $pos = $player->getPosition();
        
        $config->set("hub.world", $pos->getWorld()->getFolderName());
        $config->set("hub.x", $pos->getX());
        $config->set("hub.y", $pos->getY());
        $config->set("hub.z", $pos->getZ());
        $config->save();
        
        $player->sendMessage(TextFormat::GREEN . "Hub spawn point set!");
    }

    private function handleCreateArena(Player $player, array $args): void {
        if(count($args) < 3) {
            $player->sendMessage(TextFormat::RED . "Usage: /ffaadmin createarena <id> <type>");
            return;
        }
        
        $id = $args[1];
        $type = strtolower($args[2]);
        
        if(!in_array($type, ["nodebuff", "sumo"])) {
            $player->sendMessage(TextFormat::RED . "Invalid arena type! Available: nodebuff, sumo");
            return;
        }
        
        $pos = $player->getPosition();
        $this->plugin->getArenaManager()->createArena($id, $type, $pos);
        $player->sendMessage(TextFormat::GREEN . "Arena created successfully!");
    }
}