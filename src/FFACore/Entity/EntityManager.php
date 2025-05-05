<?php

namespace FFACore\Entity;

use FFACore\Entity\NPC\ArenaSelectorNPC;
use FFACore\Entity\NPC\ShopNPC;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class EntityManager {

    public function __construct(private Main $plugin) {
        $this->registerEntities();
    }

    private function registerEntities(): void {
        EntityFactory::getInstance()->register(
            ArenaSelectorNPC::class,
            function(World $world, CompoundTag $nbt): ArenaSelectorNPC {
                return new ArenaSelectorNPC(EntityDataHelper::parseLocation($nbt, $world), $nbt);
            },
            ['ArenaSelector']
        );
    }

    public function spawnArenaSelector(World $world, float $x, float $y, float $z): void {
        $nbt = new CompoundTag();
        $nbt->setString("npcType", "arena_selector");
        
        $entity = new ArenaSelectorNPC(
            new Location($x, $y, $z, $world, 0, 0),
            $nbt
        );
        $entity->spawnToAll();
    }

    public function spawnShopNPC(World $world, float $x, float $y, float $z): void {
        $nbt = new CompoundTag();
        $nbt->setString("npcType", "shop");
        
        $entity = new ShopNPC(
            new Location($x, $y, $z, $world, 0, 0),
            $nbt
        );
        $entity->spawnToAll();
    }
}