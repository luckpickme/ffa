<?php

namespace FFACore\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerJoinEvent,
    PlayerQuitEvent,
    PlayerDeathEvent,
    PlayerChatEvent
};
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use FFACore\FFAPlayer\FFAPlayer;

class EventListener implements Listener {

    public function __construct(private Main $plugin) {}

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->plugin->getSessionManager()->createSession($player);
        $this->plugin->getHubManager()->teleportToHub($player);
        
        $welcomeMsg = $this->plugin->getLanguageManager()->getMessage($player, "welcome");
        $event->setJoinMessage($welcomeMsg);
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $this->plugin->getSessionManager()->endSession($player);
        $event->setQuitMessage("");
    }

    public function onChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        
        $formatted = $this->plugin->getChatManager()->handleChat($player, $message);
        if($formatted === "") {
            $event->cancel();
        } else {
            $event->setMessage($formatted);
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $arena = $this->plugin->getArenaManager()->getPlayerArena($player);
        
        if($arena !== null) {
            $event->setDeathMessage("");
            $this->plugin->getStatsManager()->addDeath($player->getName());
            
            $cause = $player->getLastDamageCause();
            if($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                if($killer instanceof Player) {
                    $this->plugin->getStatsManager()->addKill($killer->getName());
                    $arena->endGame($killer);
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;
        
        $arena = $this->plugin->getArenaManager()->getPlayerArena($entity);
        if($arena !== null) {
            $this->plugin->getKnockbackManager()
                ->handleKnockback($event, $arena->getType());
        } elseif($this->plugin->getSpawnProtection()->isInSpawn($entity)) {
            $event->cancel();
        }
    }

    public function onQuery(QueryRegenerateEvent $event): void {
        $info = $event->getQueryInfo();
        $queryData = $this->plugin->getQueryManager()->getQueryData();
        
        $info->setPlayerCount($queryData['players']);
        $info->setMaxPlayerCount($queryData['max_players']);
        $info->setWorld($queryData['map']);
        $info->setPlugins([$this->plugin->getDescription()->getName()]);
    }
}