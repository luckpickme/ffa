<?php

namespace FFACore\Rank;

use FFACore\Provider\LevelDBProvider;
use pocketmine\player\Player;

class Rank {

    public function __construct({
        private string $id,
        private string $name,
        private string $prefix,
        private string $nameTagFormat,
        private array $permissions
    })

    public function getId(): string { 
        return $this->id; 
    }

    public function getName(): string { 
        return $this->name; 
    }

    public function getPrefix(): string { 
        return $this->prefix; 
    }

    public function getNameTagFormat(): string { 
        return $this->nameTagFormat; 
    }

    public function getPermissions(): array { 
        return $this->permissions; 
    }
}