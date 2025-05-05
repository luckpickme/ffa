<?php

namespace FFACore;

use FFACore\{
    Arena\ArenaManager,
    Chat\ChatManager,
    Command\AdminCommand,
    Command\FFACommand,
    Command\HubCommand,
    Command\LangCommand,
    Entity\EntityManager,
    Hub\HubManager,
    Knockback\KnockbackManager,
    Language\LanguageManager,
    Provider\LevelDBProvider,
    Punishments\PunishmentManager,
    Query\QueryManager,
    Rank\RankManager,
    Session\SessionManager,
    Spawn\SpawnProtection,
    Statistics\StatsManager,
    Task\QueryUpdateTask,
    World\WorldManager
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

    private ArenaManager $arenaManager;
    private ChatManager $chatManager;
    private EntityManager $entityManager;
    private HubManager $hubManager;
    private KnockbackManager $knockbackManager;
    private LanguageManager $languageManager;
    private LevelDBProvider $provider;
    private PunishmentManager $punishmentManager;
    private QueryManager $queryManager;
    private RankManager $rankManager;
    private SessionManager $sessionManager;
    private SpawnProtection $spawnProtection;
    private StatsManager $statsManager;
    private WorldManager $worldManager;

    public function onEnable(): void {
        $this->saveResources();
        $this->initManagers();
        $this->registerSystems();
        $this->loadWorlds();
        $this->startTasks();
    }

    private function saveResources(): void {
        $this->saveDefaultConfig();
        $this->saveResource("lang/en_US.ini");
        $this->saveResource("lang/ru_RU.ini");
    }

    private function initManagers(): void {
        $this->provider = new LevelDBProvider($this);
        $this->rankManager = new RankManager($this);
        $this->languageManager = new LanguageManager($this);
        $this->arenaManager = new ArenaManager($this);
        $this->worldManager = new WorldManager($this);
        $this->hubManager = new HubManager($this);
        $this->knockbackManager = new KnockbackManager($this);
        $this->statsManager = new StatsManager($this);
        $this->punishmentManager = new PunishmentManager($this);
        $this->sessionManager = new SessionManager($this);
        $this->chatManager = new ChatManager($this, $this->rankManager);
        $this->queryManager = new QueryManager($this);
        $this->spawnProtection = new SpawnProtection($this);
        $this->entityManager = new EntityManager($this);
    }

    private function registerSystems(): void {
        $this->getServer()->getPluginManager()->registerEvents($this->spawnProtection, $this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->registerCommands();
    }

    private function registerCommands(): void {
        $map = $this->getServer()->getCommandMap();
        $map->register("ffa", new FFACommand($this));
        $map->register("ffa", new LangCommand($this));
        $map->register("ffa", new HubCommand($this));
        $map->register("ffa", new AdminCommand($this));
    }

    private function loadWorlds(): void {
        $this->worldManager->createArenaWorld("ffa_nodebuff");
        $this->worldManager->createArenaWorld("ffa_sumo");
        $this->worldManager->loadWorld("lobby");
    }

    private function startTasks(): void {
        $this->getScheduler()->scheduleRepeatingTask(new QueryUpdateTask($this), 20 * 5);
        $this->getScheduler()->scheduleRepeatingTask(new SessionUpdateTask($this), 20 * 60);
    }

    public function getArenaManager(): ArenaManager { 
        return $this->arenaManager; 
    }

    public function getChatManager(): ChatManager { 
        return $this->chatManager; 
    }

    public function getEntityManager(): EntityManager { 
        return $this->entityManager; 
    }

    public function getHubManager(): HubManager { 
        return $this->hubManager; 
    }

    public function getKnockbackManager(): KnockbackManager { 
        return $this->knockbackManager; 
    }

    public function getLanguageManager(): LanguageManager { 
        return $this->languageManager; 
    }

    public function getProvider(): LevelDBProvider { 
        return $this->provider; 
    }

    public function getPunishmentManager(): PunishmentManager { 
        return $this->punishmentManager; 
    }

    public function getQueryManager(): QueryManager { 
        return $this->queryManager; 
    }

    public function getRankManager(): RankManager { 
        return $this->rankManager; 
    }

    public function getSessionManager(): SessionManager { 
        return $this->sessionManager; 
    }

    public function getSpawnProtection(): SpawnProtection { 
        return $this->spawnProtection; 
    }

    public function getStatsManager(): StatsManager { 
        return $this->statsManager; 
    }

    public function getWorldManager(): WorldManager { 
        return $this->worldManager; 
    }
}