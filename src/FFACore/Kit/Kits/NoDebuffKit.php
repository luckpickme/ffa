<?php

namespace FFACore\Kit\Kits;

use FFACore\Kit\Kit;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;

class NoDebuffKit extends Kit {
    
    public function getName(): string {
        return "NoDebuff";
    }
    
    public function getDescription(): string {
        return "Diamond sword, potions and golden apples";
    }
    
    public function applyTo(Player $player): void {
        $this->clearPlayer($player);
        
        $sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
        $sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2));
        
        $player->getInventory()->addItem($sword);
        
        // java kit
        for($i = 0; $i < 3; $i++) {
            $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22)); // heal potions
            $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 7)); // fire resistance
        }
        
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 8));
        
        $helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
        $chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
        $leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
        $boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
        
        $player->getArmorInventory()->setHelmet($helmet);
        $player->getArmorInventory()->setChestplate($chestplate);
        $player->getArmorInventory()->setLeggings($leggings);
        $player->getArmorInventory()->setBoots($boots);
    }
}