<?php

namespace FFACore\Knockback;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class KnockbackManager {

    private array $knockbackProfiles = [];

    public function __construct(private Main $plugin) {
        $this->loadProfiles();
    }

    private function loadProfiles(): void {
        $this->knockbackProfiles = [
            "default" => [
                "horizontal" => 0.4,
                "vertical" => 0.4,
                "extra" => 0.0
            ],
            "nodebuff" => [
                "horizontal" => 0.35,
                "vertical" => 0.35,
                "extra" => 0.0
            ],
            "sumo" => [
                "horizontal" => 0.8,
                "vertical" => 0.5,
                "extra" => 0.1
            ]
        ];
    }

    public function handleKnockback(EntityDamageEvent $event, string $arenaType): void {
        if(!($event instanceof EntityDamageByEntityEvent)) {
            return;
        }

        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if(!($damager instanceof Player) || !($victim instanceof Player)) {
            return;
        }

        $profile = $this->knockbackProfiles[$arenaType] ?? $this->knockbackProfiles["default"];
        
        $event->setKnockBack(
            $profile["horizontal"],
            $profile["vertical"],
            $profile["extra"]
        );
    }

    public function setKnockbackProfile(string $name, float $horizontal, float $vertical, float $extra = 0.0): void {
        $this->knockbackProfiles[$name] = [
            "horizontal" => $horizontal,
            "vertical" => $vertical,
            "extra" => $extra
        ];
    }
}