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
 * Coded: December 20, 2024 at 1:46am PST
 *
 */

declare(strict_types=1);

namespace wavycraft\legacyachievements;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use wavycraft\legacyachievements\AchievementManager;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\CustomForm;

class AchievementCommand extends Command {

    public function __construct() {
        parent::__construct("achievements", "View achievements", "/achievements");
        $this->setPermission("legacyachievements.cmd");
    }

    public function execute(CommandSender $sender, string $label, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game...");
            return;
        }

        $this->sendMainMenu($sender);
    }

    private function sendMainMenu(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $this->sendPlayerAchievements($player);
                    break;
                case 1:
                    $this->sendOtherPlayersMenu($player);
                    break;
            }
        });

        $form->setTitle("Achievements");
        $form->addButton("View Achievements");
        $form->addButton("View Other Achievements");
        $player->sendForm($form);
    }

    private function sendPlayerAchievements(Player $player) : void{
        $manager = AchievementManager::getInstance();
        $achievements = $manager->getPlayerAchievements($player);

        $form = new ModalForm(function (Player $player, ?bool $data) {
            if ($data === null) {
                return;
            }

            if ($data) {
                return;
            } else {
                $this->sendMainMenu($player);
            }
        });

        $content = "Your Achievements:\n";
        foreach (AchievementManager::$list as $key => $achievement) {
            $status = $achievements[$key] ?? false;
            $color = $status ? "§a" : "§c";
            $content .= "{$color}{$achievement['name']}§r\n";
        }

        $form->setTitle("Your Achievements");
        $form->setContent($content);
        $form->setButton1("Done");
        $form->setButton2("Back");
        $player->sendForm($form);
    }

    private function sendOtherPlayersMenu(Player $player) : void{
        $manager = AchievementManager::getInstance();
        $playerData = $manager->getPlayerData();

        $form = new CustomForm(function (Player $player, ?array $data) use ($playerData) {
            if ($data === null || !isset($data[0])) {
                return;
            }

            $selectedPlayer = array_keys($playerData)[$data[0]];
            $this->sendOtherPlayerAchievements($player, $selectedPlayer);
        });

        $form->setTitle("Other Players' Achievements");
        $options = [];
        foreach ($playerData as $uuid => $data) {
            $options[] = $data["username"];
        }

        $form->addDropdown("Select a Player", $options);
        $player->sendForm($form);
    }

    private function sendOtherPlayerAchievements(Player $player, string $uuid) : void{
        $manager = AchievementManager::getInstance();
        $playerData = $manager->getPlayerData();
        $achievements = $playerData[$uuid]["achievements"] ?? [];
        $username = $playerData[$uuid]["username"] ?? "Unknown";

        $form = new ModalForm(function (Player $player, ?bool $data) {
            if ($data === null) {
                return;
            }

            if ($data) {
                return;
            } else {
                $this->sendMainMenu($player);
            }
        });

        $content = "{$username}'s Achievements:\n";
        foreach (AchievementManager::$list as $key => $achievement) {
            $status = $achievements[$key] ?? false;
            $color = $status ? "§a" : "§c";
            $content .= "{$color}{$achievement['name']}§r\n";
        }

        $form->setTitle("{$username}'s Achievements");
        $form->setContent($content);
        $form->setButton1("Done");
        $form->setButton2("Back");
        $player->sendForm($form);
    }
}