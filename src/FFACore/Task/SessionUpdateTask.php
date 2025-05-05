<?php

namespace FFACore\Task;

use FFACore\Main;
use pocketmine\scheduler\Task;

class QueryUpdateTask extends Task {

    public function __construct(private Main $plugin) {}

    public function onRun(): void {
        $this->plugin->getQueryManager()->updateQueryData();
    }
}