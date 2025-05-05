<?php

namespace FFA\Arena;

enum ArenaType: string {
    case NODEBUFF = "nodebuff";
    case SUMO = "sumo";
    
    public function getDisplayName(): string {
        return match($this) {
            self::NODEBUFF => "NoDebuff",
            self::SUMO => "Sumo"
        };
    }
}