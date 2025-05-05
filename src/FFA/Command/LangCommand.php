<?php

namespace FFA\Command;

use FFA\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class LangCommand extends Command {
    
    public function __construct(private Main $plugin) {
        parent::__construct("lang", "Change your language", "/lang <language>");
        $this->setPermission("ffa.command.lang");
        $this->setAliases(["language", "ffalang"]);
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if(!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . $this->plugin->getLanguageManager()->getMessage(null, "error.player.only"));
            return false;
        }
        
        if(count($args) === 0) {
            $currentLang = $languageManager->getPlayerLanguage($sender);
            $sender->sendMessage(TextFormat::GREEN . $languageManager->getMessage($sender, "language.current", [
                "language" => $currentLang
            ]));
            $this->sendLanguageList($sender);
            return true;
        }
        
        $langCode = strtolower($args[0]);
        $languageManager = $this->plugin->getLanguageManager();
        
        if($languageManager->setPlayerLanguage($sender, $langCode)) {
            $sender->sendMessage(TextFormat::GREEN . $languageManager->getMessage($sender, "language.changed", [
                "language" => $langCode
            ]));
        } else {
            $sender->sendMessage(TextFormat::RED . $languageManager->getMessage($sender, "language.invalid"));
            $this->sendLanguageList($sender);
        }
        
        return true;
    }
    
    private function sendLanguageList(Player $player): void {
        $languageManager = $this->plugin->getLanguageManager();
        $languages = $languageManager->getAvailableLanguages();
        
        $player->sendMessage(TextFormat::GOLD . "--- Available Languages ---");
        foreach($languages as $code => $name) {
            $player->sendMessage(TextFormat::WHITE . "- " . TextFormat::AQUA . $code . TextFormat::WHITE . ": " . $name);
        }
        $player->sendMessage(TextFormat::GOLD . "Usage: /lang <language-code>");
    }
}