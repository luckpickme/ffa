<?php

namespace FFA\Language;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use FFA\Main;

class LanguageManager {
    
    private Main $plugin;
    private array $messages = [];
    private string $defaultLang = "en_US";
    
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->loadLanguages();
    }
    
    private function loadLanguages(): void {
        foreach(glob($this->plugin->getDataFolder() . "lang/*.ini") as $file) {
            $lang = basename($file, ".ini");
            $this->messages[$lang] = parse_ini_file($file);
        }
        
        if(empty($this->messages)) {
            $this->plugin->saveResource("lang/en_US.ini");
            $this->plugin->saveResource("lang/ru_RU.ini");
            $this->loadLanguages();
        }
    }
    
    public function getMessage(Player $player, string $key, array $params = []): string {
        $lang = $this->getPlayerLanguage($player);
        $message = $this->messages[$lang][$key] ?? $this->messages[$this->defaultLang][$key] ?? $key;
        
        foreach($params as $param => $value) {
            $message = str_replace("{" . $param . "}", $value, $message);
        }
        
        return $message;
    }
    
    private function getPlayerLanguage(Player $player): string {
        return $this->defaultLang;
    }
    
    public function setPlayerLanguage(Player $player, string $lang): bool {
        if(isset($this->messages[$lang])) {
            return true;
        }
        return false;
    }
}