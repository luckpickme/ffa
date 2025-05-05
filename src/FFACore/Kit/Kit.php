<?php

namespace FFACore\Kit;

use pocketmine\player\Player;
use pocketmine\item\Item;

abstract class Kit {
    
    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function applyTo(Player $player): void;
    
    protected function clearPlayer(Player $player): void {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getEffects()->clear();
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood(20);
    }
}