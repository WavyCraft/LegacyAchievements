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
 * Coded: March 2, 2025 at 2:23am PST
 *
 */

declare(strict_types=1);

namespace wavycraft\legacyachievements\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

class PlayerOpenInventoryEvent extends Event {

    public function __construct(protected Player $player) {
        $this->player = $player;
    }

    public function getPlayer() : Player{
        return $this->player;
    }
}