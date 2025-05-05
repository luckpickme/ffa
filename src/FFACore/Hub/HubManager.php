<?php

namespace FFACore\Hub;

use FFACore\Entity\EntityManager;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HubManager {

    private Position $hubPosition;
    private array $hubItems = [];

    public function __construct(private Main $plugin) {
        $this->loadHubConfig();
        $this->initHubItems();
    }

    private function loadHubConfig(): void {
        $config = $this->plugin->getConfig();
        $world = $this->plugin->getServer()->getWorldManager()
            ->getWorldByName($config->get("hub.world", "lobby"));
        
        $this->hubPosition = new Position(
            $config->get("hub.x", 0),
            $config->get("hub.y", 64),
            $config->get("hub.z", 0),
            $world
        );
    }

    private function initHubItems(): void {
        $this->hubItems = [
            0 => ItemFactory::getInstance()->get(ItemIds::COMPASS)->setCustomName("§aArena Selector"),
            1 => ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§eStats"),
            //4 => ItemFactory::getInstance()->get(ItemIds::CHEST)->setCustomName("§6Shop"),
            7 => ItemFactory::getInstance()->get(ItemIds::CLOCK)->setCustomName("§bSettings"),
            8 => ItemFactory::getInstance()->get(ItemIds::REDSTONE)->setCustomName("§cLeave Queue")
        ];
    }

    public function teleportToHub(Player $player): void {
        $player->teleport($this->hubPosition);
        $this->giveHubItems($player);
        
        if($this->plugin->getEntityManager()->getNPCsCount() < 1) {
            $this->spawnHubNPCs();
        }
    }

    private function giveHubItems(Player $player): void {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getEffects()->clear();
        
        foreach($this->hubItems as $slot => $item) {
            $player->getInventory()->setItem($slot, $item);
        }
    }

    private function spawnHubNPCs(): void {
        $world = $this->hubPosition->getWorld();
        $this->plugin->getEntityManager()->spawnArenaSelector($world, 10, 64, 10);
        //$this->plugin->getEntityManager()->spawnShopNPC($world, -10, 64, 10);
    }

    public function getHubWorld(): ?World {
        return $this->hubPosition->getWorld();
    }
}