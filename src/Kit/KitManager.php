<?php

namespace FFA\Kit;

use FFA\Kit\Kits\NoDebuffKit;
use FFA\Kit\Kits\SumoKit;
use pocketmine\player\Player;

class KitManager {
    
    /** @var Kit[] */
    private array $kits = [];
    
    public function __construct(private \FFA\Main $plugin) {
        $this->registerDefaultKits();
    }
    
    private function registerDefaultKits(): void {
        $this->kits["nodebuff"] = new NoDebuffKit();
        $this->kits["sumo"] = new SumoKit();
    }
    
    public function getKit(string $name): ?Kit {
        return $this->kits[strtolower($name)] ?? null;
    }
    
    public function applyKit(Player $player, string $kitName): bool {
        $kit = $this->getKit($kitName);
        if($kit === null) {
            return false;
        }
        
        $kit->applyTo($player);
        return true;
    }
    
    public function getAvailableKits(): array {
        return array_keys($this->kits);
    }
}