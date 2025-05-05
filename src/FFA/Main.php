<?php

namespace FFA;

use FFA\Arena\ArenaManager;
use FFA\Kit\KitManager;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
    
    private ArenaManager $arenaManager;
    private KitManager $kitManager;
    private ScoreboardManager $scoreboardManager;
    
    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->arenaManager = new ArenaManager($this);
        $this->kitManager = new KitManager($this);
        $this->scoreboardManager = new ScoreboardManager();
        
        $this->getServer()->getCommandMap()->register("ffa", new Command\FFACommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new Listener\EventListener($this), $this);
        
        $this->getScheduler()->scheduleRepeatingTask(new Task\GameTask($this), 20);
    }
    
    public function getArenaManager(): ArenaManager {
        return $this->arenaManager;
    }
    
    public function getKitManager(): KitManager {
        return $this->kitManager;
    }

    public function getScoreboardManager(): ScoreboardManager {
        return $this->scoreboardManager;
    }
}