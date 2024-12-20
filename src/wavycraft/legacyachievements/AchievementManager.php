<?php

/*
 *
 *  _                                              _     _                                     _       
 * | |                                   /\       | |   (_)                                   | |      
 * | |     ___  __ _  __ _  ___ _   _   /  \   ___| |__  _  _____   _____ _ __ ___   ___ _ __ | |_ ___ 
 * | |    / _ \/ _` |/ _` |/ __| | | | / /\ \ / __| '_ \| |/ _ \ \ / / _ \ '_ ` _ \ / _ \ '_ \| __/ __|
 * | |___|  __/ (_| | (_| | (__| |_| |/ ____ \ (__| | | | |  __/\ V /  __/ | | | | |  __/ | | | |_\__ \
 * |______\___|\__, |\__,_|\___|\__, /_/    \_\___|_| |_|_|\___| \_/ \___|_| |_| |_|\___|_| |_|\__|___/
 *              __/ |            __/ |                                                                 
 *             |___/            |___/
 *
 * Created by Terpz710
 * Coded: December 3, 2024 at 6:30am PST
 *
 */

declare(strict_types=1);

namespace wavycraft\legacyachievements;

use pocketmine\Server;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TextColor;
use pocketmine\utils\SingletonTrait;

final class AchievementManager {
    use SingletonTrait;

    private $plugin;
    private $config;

    private array $playerData = [];

    /*
     * Thanks @Shoghicp for the list of achievements :)
     * Some legacy achievements are left out due to pmmp limitations....
     * Added some missing achievements but not all!
     */
    public static array $list = [
        //"openInventory" => [
            //"name" => "Taking Inventory",
            //"description" => "Press Inventory Key to open your inventory",
            //"requires" => [],
        //],
        "mineWood" => [
            "name" => "Getting Wood",
            "description" => "Attack a tree until a block of wood pops out",
            "requires" => [],
        ],
        "buildWorkBench" => [
            "name" => "Benchmarking",
            "description" => "Craft a workbench with four blocks of planks",
            "requires" => ["mineWood"],
        ],
        "buildPickaxe" => [
            "name" => "Time to Mine!",
            "description" => "Use planks and sticks to make a pickaxe",
            "requires" => ["buildWorkBench"],
        ],
        "buildFurnace" => [
            "name" => "Hot Topic",
            "description" => "Construct a furnace out of eight cobblestone blocks\Construct a furnace out of eight stone blocks",
            "requires" => ["buildPickaxe"],
        ],
        "acquireIron" => [
            "name" => "Acquire Hardware",
            "description" => "Smelt an iron ingot",
            "requires" => ["buildFurnace"],
        ],
        "buildHoe" => [
            "name" => "Time to Farm!",
            "description" => "Use planks and sticks to make a hoe",
            "requires" => ["buildWorkBench"],
        ],
        "makeBread" => [
            "name" => "Bake Bread",
            "description" => "Turn wheat into bread",
            "requires" => ["buildHoe"],
        ],
        "bakeCake" => [
            "name" => "The Lie",
            "description" => "Wheat, sugar, milk, and eggs\Bake cake using wheat, sugar, milk, and eggs!",
            "requires" => ["buildHoe"],
        ],
        "buildBetterPickaxe" => [
            "name" => "Getting an Upgrade",
            "description" => "Construct a better pickaxe",
            "requires" => ["buildPickaxe"],
        ],
        "buildSword" => [
            "name" => "Time to Strike!",
            "description" => "Use planks and sticks to make a sword",
            "requires" => ["buildWorkBench"],
        ],
        "diamonds" => [
            "name" => "DIAMONDS!",
            "description" => "Acquire diamonds with your iron tools",
            "requires" => ["acquireIron"],
        ],
        "enchantments" => [
            "name" => "Enchanter",
            "description" => "Use a book, obsidian and diamonds to construct an enchantment table",
            "requires" => ["diamonds"],
        ],
        "overkill" => [
            "name" => "Overkill",
            "description" => "Deal nine hearts of damage in a single hit",
            "requires" => ["enchantments"],
        ],
        "bookcase" => [
            "name" => "Librarian",
            "description" => "Build some bookshelves to improve your enchantment table",
            "requires" => ["enchantments"],
        ],
        "overpowered" => [
            "name" => "Overpowered",
            "description" => "Eat the Notch apple",
            "requires" => ["buildBetterPickaxe"],
        ],
    ];

    public function __construct() {
        $this->plugin = Loader::getInstance();
        $this->loadPlayerData();
    }

    public function initiateAchievements(Player $player) : void{
        $uuid = $player->getUniqueId()->toString();
        $username = $player->getName();

        if (!isset($this->playerData[$uuid])) {
            $this->playerData[$uuid] = [
                "username" => $username,
                "achievements" => []
            ];
            foreach (self::$list as $key => $achievement) {
                $this->playerData[$uuid]["achievements"][$key] = false;
            }
            $this->savePlayerData();
        } elseif ($this->playerData[$uuid]["username"] !== $username) {
            $this->playerData[$uuid]["username"] = $username;
            $this->savePlayerData();
        }
    }

    public function unlockAchievement(Player $player, string $achievementKey) : void{
        $uuid = $player->getUniqueId()->toString();

        if (!isset(self::$list[$achievementKey]) || empty($this->playerData[$uuid])) {
            return;
        }

        $achievement = self::$list[$achievementKey];
        $requires = $achievement["requires"];

        foreach ($requires as $requirement) {
            if (empty($this->playerData[$uuid]["achievements"][$requirement])) {
                return;
            }
        }

        if (!$this->playerData[$uuid]["achievements"][$achievementKey]) {
            $this->playerData[$uuid]["achievements"][$achievementKey] = true;
            Server::getInstance()->broadcastMessage(
                $player->getName() . " has just earned the achievement " . TextColor::GREEN . "[" . $achievement["name"] . "]"
            );
            $this->savePlayerData();
        }
    }

    public function getPlayerAchievements(Player $player) : array{
        $uuid = $player->getUniqueId()->toString();
        return $this->playerData[$uuid]["achievements"] ?? [];
    }

    public function getPlayerData() : array{
        return $this->playerData;
    }

    private function loadPlayerData() : void{
        $this->config = new Config($this->plugin->getDataFolder() . "achievements.json", Config::JSON);
        $this->playerData = $this->config->getAll();
    }

    private function savePlayerData() : void{
        $this->config->setAll($this->playerData);
        $this->config->save();
    }
}
