<?php

/**
 * VSharedData - PocketMine plugin.
 * Copyright (C) 2023 - 2025 VennDev
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace vennv\vvanillamobs;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use vennv\vvanillamobs\entity\Registrations;
use vennv\vapm\VapmPMMP;

final class VVanillaMobs extends PluginBase implements Listener {

	protected function onEnable() : void {
		VapmPMMP::init($this);
		Registrations::init();

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param string $entity
	 * @param mixed ...$args - Example: Location, CompoundTag, etc.
	 * @return bool
	 *
	 * This is a function that can be used to summon entities.
	 */
	public function summon(string $entity, ...$args) : bool {
		$entity = Registrations::getEntity($entity, ...$args);

		if ($entity !== null) {
			$entity->spawnToAll();
			return true;
		}

		return false;
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();
		$this->summon("Zombie", $player->getLocation());
	}

}