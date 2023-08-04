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

namespace vennv\vvanillamobs\utils\ai;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\entity\Entity;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;

final class AI {

	/**
	 * @param Entity $entity
	 * @param Vector3 $vector3
	 * @param array<int, Unbeatable> $blocksBlocked
	 * @return bool
	 */
	public static function canPass(Entity $entity, Vector3 $vector3, array $blocksBlocked = []) : bool {
		$down = $entity->getWorld()->getBlock($vector3->getSide(Facing::DOWN));

		foreach ($blocksBlocked as $block) {
			if ($down->getTypeId() === $block->getBlock()->getTypeId()) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param Vector3 $vector3
	 * @return Vector3
	 */
	public static function randomVector3(Vector3 $vector3) : Vector3 {
		$x = mt_rand(5, 10);
		$z = mt_rand(5, 10);

		$vector3->x += mt_rand(0, 1) === 1 ? $x : -$x;
		$vector3->z += mt_rand(0, 1) === 1 ? $z : -$z;

		return $vector3;
	}

}