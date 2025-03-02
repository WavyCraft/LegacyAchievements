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

use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("LegacyAchievements", new AchievementCommand());
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}
