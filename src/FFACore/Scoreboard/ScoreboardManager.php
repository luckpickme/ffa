<?php

namespace FFACore\Scoreboard;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreboardManager {
    
    private const DISPLAY_SLOT = "sidebar";
    private const OBJECTIVE_NAME = "ffa.score";
    private const CRITERIA_NAME = "dummy";
    
    /** @var array */
    private array $scoreboards = [];
    
    public function create(Player $player, string $title): void {
        $this->remove($player);
        
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = self::DISPLAY_SLOT;
        $pk->objectiveName = self::OBJECTIVE_NAME;
        $pk->displayName = $title;
        $pk->criteriaName = self::CRITERIA_NAME;
        $pk->sortOrder = 0;
        $player->getNetworkSession()->sendDataPacket($pk);
        
        $this->scoreboards[$player->getName()] = true;
    }
    
    public function remove(Player $player): void {
        if(isset($this->scoreboards[$player->getName()])) {
            $pk = new RemoveObjectivePacket();
            $pk->objectiveName = self::OBJECTIVE_NAME;
            $player->getNetworkSession()->sendDataPacket($pk);
            unset($this->scoreboards[$player->getName()]);
        }
    }
    
    public function setLines(Player $player, array $lines): void {
        if(!isset($this->scoreboards[$player->getName()])) {
            return;
        }
        
        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_REMOVE;
        $pk->entries[] = $this->getEntry($player, "", 0);
        $player->getNetworkSession()->sendDataPacket($pk);
        
        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_CHANGE;
        
        $lineCount = count($lines);
        foreach($lines as $line => $text) {
            $entry = $this->getEntry($player, $text, $lineCount - $line);
            $pk->entries[] = $entry;
        }
        
        $player->getNetworkSession()->sendDataPacket($pk);
    }
    
    private function getEntry(Player $player, string $text, int $scoreId): ScorePacketEntry {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = self::OBJECTIVE_NAME;
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $entry->customName = $text;
        $entry->score = $scoreId;
        $entry->scoreboardId = $scoreId;
        return $entry;
    }
    
    public function updateForArena(array $players, string $title, array $lines): void {
        foreach($players as $player) {
            if($player instanceof Player && $player->isOnline()) {
                $this->create($player, $title);
                $this->setLines($player, $lines);
            }
        }
    }
}