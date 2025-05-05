<?php

namespace FFA\Task;

use FFA\Main;
use pocketmine\scheduler\Task;

class GameTask extends Task {
    
    public function __construct(private Main $plugin) {}
    
    public function onRun(): void {
        foreach($this->plugin->getArenaManager()->getArenas() as $arena) {
            $arena->update();
        }
    }
}