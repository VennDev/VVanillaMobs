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

namespace vennv\vvanillamobs\entity\entities\overworld;

use pocketmine\entity\Ageable;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use vennv\vvanillamobs\entity\types\GroundMonster;

class Zombie extends GroundMonster implements Ageable {

	protected bool $isBaby;

	public float $damageAttack = 2.0;

	public function __construct(Location $location, ?CompoundTag $nbt = null, bool $isBaby = false) {
		parent::__construct($location, $nbt);
		$this->isBaby = $isBaby;
	}

	public function getName() : string {
		return "Zombie";
	}

	public static function getNetworkTypeId() : string {
		return EntityIds::ZOMBIE;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo {
		return new EntitySizeInfo(1.8, 0.6);
	}

	public function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);

		$this->isBaby = $nbt->getByte("IsBaby", 0) !== 0;

		if ($this->isBaby()) {
			$this->setScale(0.5);
		}
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void {
		parent::syncNetworkData($properties);
		$properties->setGenericFlag(EntityMetadataFlags::BABY, $this->isBaby());
	}

	public function saveNBT() : CompoundTag {
		$nbt = parent::saveNBT();
		$nbt->setByte("IsBaby", $this->isBaby() ? 1 : 0);

		return $nbt;
	}

	public function getDrops() : array {
		$drops = [
			VanillaItems::ROTTEN_FLESH()->setCount(mt_rand(0, 2))
		];

		if (mt_rand(0, 199) < 5) {
			switch (mt_rand(0, 2)) {
				case 0:
					$drops[] = VanillaItems::IRON_INGOT();
					break;
				case 1:
					$drops[] = VanillaItems::CARROT();
					break;
				case 2:
					$drops[] = VanillaItems::POTATO();
					break;
			}
		}

		return $drops;
	}

	public function getXpDropAmount() : int {
		if ($this->isBaby()) {
			return 12;
		}

		return 5;
	}

	public function isBaby() : bool {
		return $this->isBaby;
	}

}