<?php

namespace FFA\Listener;

use FFA\Arena\ArenaType;
use FFA\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EventListener implements Listener {
    
    public function __construct(private Main $plugin) {}
    
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $player->sendMessage("Welcome to FFA! Use /ffa to join a game");
    }
    
    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof \pocketmine\player\Player) return;
        
        $arena = $this->plugin->getArenaManager()->getPlayerArena($entity);
        if($arena === null) return;
        
        if($arena->getType() === ArenaType::SUMO) {
            if($event instanceof EntityDamageByEntityEvent) {
                $event->setKnockBack($event->getKnockBack() * 1.5);
            }
            
            if(!$event instanceof EntityDamageByEntityEvent) {
                $event->cancel();
            }
        }
    }
    
    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $arena = $this->plugin->getArenaManager()->getPlayerArena($player);
        
        if($arena !== null) {
            $event->setDeathMessage("");
            
            $cause = $player->getLastDamageCause();
            if($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                if($killer instanceof Player) {
                    $arena->setScoreboardLines([
                        "§7----------------",
                        "§f" . $arena->getType()->getDisplayName() . " FFA",
                        "§7----------------",
                        "§cGame Over!",
                        "§7----------------",
                        "§fWinner: §a" . $killer->getName(),
                        "§fKills: §a1",
                        "§7----------------"
                    ]);
                    $arena->updateScoreboard();
                    $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($arena, $killer): void {
                        $arena->endGame($killer);
                    }), 20 * 3);
                }
            } else {
                $arena->removePlayer($player);
            }
        }
    }
    
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $arena = $this->plugin->getArenaManager()->getPlayerArena($player);
        
        if($arena !== null) {
            $arena->removePlayer($player);
        }
    }
}