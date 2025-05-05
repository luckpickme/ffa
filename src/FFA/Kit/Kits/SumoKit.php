<?php

namespace FFA\Kit\Kits;

use FFA\Kit\Kit;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SumoKit extends Kit {
    
    public function getName(): string {
        return "Sumo";
    }
    
    public function getDescription(): string {
        return "No items, just knockback!";
    }
    
    public function applyTo(Player $player): void {
        $this->clearPlayer($player);
        
        $player->getEffects()->add(new \pocketmine\entity\effect\EffectInstance(
            \pocketmine\entity\effect\VanillaEffects::RESISTANCE(),
            20 * 60 * 5, // 5 минут
            4,
            false
        ));
        
        $player->getEffects()->add(new \pocketmine\entity\effect\EffectInstance(
            \pocketmine\entity\effect\VanillaEffects::SPEED(),
            20 * 60 * 5,
            1,
            false
        ));
    }
}