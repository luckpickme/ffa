<?php

namespace FFACore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class HubCommand extends Command {

    public function __construct(private Main $plugin) {
        parent::__construct("hub", "Teleport to hub", "/hub");
        $this->setPermission("ffa.command.hub");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game");
            return false;
        }

        $this->plugin->getHubManager()->teleportToHub($sender);
        $sender->sendMessage(
            $this->plugin->getLanguageManager()->getMessage($sender, "hub.teleported")
        );
        
        return true;
    }
}