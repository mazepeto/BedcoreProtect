<?php

/*
 *     ___         __                 ___           __          __
 *    / _ )___ ___/ /______  _______ / _ \_______  / /____ ____/ /_
 *   / _  / -_) _  / __/ _ \/ __/ -_) ___/ __/ _ \/ __/ -_) __/ __/
 *  /____/\__/\_,_/\__/\___/_/  \__/_/  /_/  \___/\__/\__/\__/\__/
 *
 * Copyright (C) 2019
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author matcracker
 * @link https://www.github.com/matcracker/BedcoreProtect
 *
*/

declare(strict_types=1);

namespace matcracker\BedcoreProtect\listeners;

use matcracker\BedcoreProtect\storage\queries\QueriesConst;
use matcracker\BedcoreProtect\utils\BlockUtils;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityExplodeEvent;

final class EntityListener extends BedcoreListener
{
    /**
     * @param EntityExplodeEvent $event
     * @priority MONITOR
     */
    public function trackEntityExplode(EntityExplodeEvent $event): void
    {
        if ($this->plugin->getParsedConfig()->getExplosions()) {
            $entity = $event->getEntity();
            $blocks = $event->getBlockList();

            if ($entity instanceof PrimedTNT) {
                $air = BlockUtils::createAir();
                $this->database->getQueries()->addBlocksLogByEntity($entity, $blocks, $air, QueriesConst::BROKE);
            }
        }
    }

    /**
     * @param EntityDeathEvent $event
     * @priority MONITOR
     */
    public function trackEntityDeath(EntityDeathEvent $event): void
    {
        if ($this->plugin->getParsedConfig()->getEntityKills()) {
            $entity = $event->getEntity();
            $ev = $entity->getLastDamageCause();
            if ($ev instanceof EntityDamageByEntityEvent) {
                $damager = $ev->getDamager();
                $this->database->getQueries()->addLogEntityByEntity($damager, $entity);
            }
        }
    }
}