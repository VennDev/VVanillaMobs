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

declare(strict_types=1);

namespace vennv\vvanillamobs\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use pocketmine\Server;
use vennv\vvanillamobs\entity\entities\overworld\Zombie;

final class Registrations {

    public static function init() : void {
        self::registerEntities();
    }

    private static function registerEntities() : void {
        foreach (self::getEntities() as $name => $class) {
            EntityFactory::getInstance()->register($class, function (World $world, CompoundTag $nbt) use ($class) : BaseEntity {
                return new $class(EntityDataHelper::parseLocation($nbt, $world), $nbt);
            }, [$name]);

            Server::getInstance()->getLogger()->info("Registered entity: " . $name . " with class: " . $class);
        }
    }

    private static function getEntitiesOverWorld() : array {
        return [
            "Zombie" => Zombie::class,
        ];
    }

	private static function getEntitiesNether() : array {
        return [];
    }

	private static function getEntitiesEnd() : array {
        return [];
    }

	public static function getEntities() : array {
		return array_merge(
			self::getEntitiesOverWorld(),
			self::getEntitiesNether(),
			self::getEntitiesEnd()
		);
	}

	/**
	 * @param string $mob
	 * @param mixed ...$args - Example: Location, CompoundTag, etc.
	 * @return Entity|null
	 */
	public static function getEntity(string $mob, mixed...$args) : ?Entity {
		$entities = array_merge(
			self::getEntitiesOverWorld(),
			self::getEntitiesNether(),
			self::getEntitiesEnd()
		);

		$entity = null;
		if (isset($entities[$mob])) {
			try {
				$entity = new $entities[$mob](...$args);
			} catch (\Exception $e) {
				Server::getInstance()->getLogger()->error("Error while creating entity: " . $e->getMessage());
			}
		}

		return $entity;
	}

}