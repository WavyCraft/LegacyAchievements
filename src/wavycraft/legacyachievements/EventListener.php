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

use pocketmine\event\Listener;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityItemPickupEvent;

use pocketmine\block\Furnace;

use pocketmine\item\StringToItemParser;
use pocketmine\item\Item;

use pocketmine\player\Player;

use pocketmine\Server;

class EventListener implements Listener {

    private array $furnaceToPlayer = [];

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        AchievementManager::getInstance()->initiateAchievements($event->getPlayer());
    }

    public function onItemPickup(EntityItemPickupEvent $event) : void{
        $entity = $event->getEntity();
        $item = $event->getItem();

        if ($entity instanceof Player) {
            $woodTypes = [
                "oak_log",
                "spruce_log",
                "birch_log",
                "jungle_log",
                "acacia_log",
                "dark_oak_log",
            ];

            foreach ($woodTypes as $woodName) {
                $wood = StringToItemParser::getInstance()->parse($woodName);
                if ($wood !== null && $item->equals($wood)) {
                    AchievementManager::getInstance()->unlockAchievement($entity, "mineWood");
                    return;
                }
            }

            $diamond = StringToItemParser::getInstance()->parse("diamond");
            if ($diamond !== null && $item->equals($diamond)) {
                AchievementManager::getInstance()->unlockAchievement($entity, "diamonds");
            }
        }
    }

    public function onCraftItem(CraftItemEvent $event) : void{
        $player = $event->getPlayer();
        $outputs = $event->getOutputs();
        $am = AchievementManager::getInstance();

        foreach ($outputs as $item) {//found better results using StringToItemParser::class.... dont even say anything....
            $craftingTable = StringToItemParser::getInstance()->parse("crafting_table");
            $pickaxe = StringToItemParser::getInstance()->parse("wooden_pickaxe");
            $betterPickaxes = [
                StringToItemParser::getInstance()->parse("stone_pickaxe"),
                StringToItemParser::getInstance()->parse("iron_pickaxe"),
                StringToItemParser::getInstance()->parse("diamond_pickaxe"),
                StringToItemParser::getInstance()->parse("netherite_pickaxe"),
            ];
            $bread = StringToItemParser::getInstance()->parse("bread");
            $furnace = StringToItemParser::getInstance()->parse("furnace");
            $hoes = [
                StringToItemParser::getInstance()->parse("wooden_hoe"),
                StringToItemParser::getInstance()->parse("stone_hoe"),
                StringToItemParser::getInstance()->parse("golden_hoe"),
                StringToItemParser::getInstance()->parse("iron_hoe"),
                StringToItemParser::getInstance()->parse("diamond_hoe"),
                StringToItemParser::getInstance()->parse("netherite_hoe"),
            ];
            $swords = [
                StringToItemParser::getInstance()->parse("wooden_sword"),
                StringToItemParser::getInstance()->parse("stone_sword"),
                StringToItemParser::getInstance()->parse("golden_sword"),
                StringToItemParser::getInstance()->parse("iron_sword"),
                StringToItemParser::getInstance()->parse("diamond_sword"),
                StringToItemParser::getInstance()->parse("netherite_sword"),
            ];
            $cake = StringToItemParser::getInstance()->parse("cake");
            $enchantmentTable = StringToItemParser::getInstance()->parse("enchanting_table");

            if ($craftingTable !== null && $item->equals($craftingTable)) {
                $am->unlockAchievement($player, "buildWorkBench");
            } elseif ($pickaxe !== null && $item->equals($pickaxe)) {
                $am->unlockAchievement($player, "buildPickaxe");
            } elseif ($furnace !== null && $item->equals($furnace)) {
                $am->unlockAchievement($player, "buildFurnace");
            } elseif ($this->matchesItem($item, $betterPickaxes)) {
                $am->unlockAchievement($player, "buildBetterPickaxe");
            } elseif ($bread !== null && $item->equals($bread)) {
                $am->unlockAchievement($player, "makeBread");
            } elseif ($this->matchesItem($item, $hoes)) {
                $am->unlockAchievement($player, "buildHoe");
            } elseif ($this->matchesItem($item, $swords)) {
                $am->unlockAchievement($player, "buildSword");
            } elseif ($cake !== null && $item->equals($cake)) {
                $am->unlockAchievement($player, "bakeCake");
            } elseif ($enchantmentTable !== null && $item->equals($enchantmentTable)) {
                $am->unlockAchievement($player, "enchantments");
            }
        }
    }

    private function matchesItem(Item $item, array $items) : bool{
        foreach ($items as $matchItem) {
            if ($matchItem !== null && $item->equals($matchItem)) {
                return true;
            }
        }
        return false;
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($block instanceof Furnace) {
            $this->furnaceToPlayer[$block->getPosition()->asVector3()->__toString()] = $player->getName();
        }
    }

    public function onFurnaceSmelt(FurnaceSmeltEvent $event) : void{
        $furnacePos = $event->getBlock()->getPosition()->asVector3()->__toString();
        $result = $event->getResult();

        if (isset($this->furnaceToPlayer[$furnacePos])) {
            $playerName = $this->furnaceToPlayer[$furnacePos];
            $player = Server::getInstance()->getPlayerExact($playerName);

            if ($player !== null) {
                $ironIngot = StringToItemParser::getInstance()->parse("iron_ingot");
                if ($ironIngot !== null && $result->equals($ironIngot)) {
                    AchievementManager::getInstance()->unlockAchievement($player, "acquireIron");
                }
            }
        }
    }

    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void{
        $player = $event->getPlayer();
        $item = $event->getItem();

        $notchApple = StringToItemParser::getInstance()->parse("enchanted_golden_apple");
        if ($notchApple !== null && $item->equals($notchApple)) {
            AchievementManager::getInstance()->unlockAchievement($player, "overpowered");
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
        $damager = $event->getDamager();

        if ($damager instanceof Player && $event->getFinalDamage() >= 18) {
            AchievementManager::getInstance()->unlockAchievement($damager, "overkill");
        }
    }
}
