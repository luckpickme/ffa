<?php

namespace FFACore\Entity\NPC;

use FFACore\Form\ArenaSelectorForm;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;

class ArenaSelectorNPC extends Human {

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag("§aArena Selector\n§eClick to open");
    }

    public function attack(EntityDamageEvent $source): void {
        $source->cancel();
        
        $player = $source->getDamager();
        if($player instanceof Player) {
            $player->sendForm(new ArenaSelectorForm($this->plugin));
        }
    }
}