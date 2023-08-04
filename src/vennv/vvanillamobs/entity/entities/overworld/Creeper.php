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

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Explosive;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\item\FlintSteel;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\world\Explosion;
use pocketmine\world\sound\FlintSteelSound;
use vennv\vvanillamobs\entity\types\GroundMonster;

class Creeper extends GroundMonster implements Explosive {

	public bool $canAttack = false;

	public const DEFAULT_FUSE = 30;

	public const INSANE_SPEED = 0.3;

	public const NORMAL_SPEED = 0.2;

	private bool $ignited = false;

	private bool $explode = false;

	private bool $powered = false;

	private int $fuse = self::DEFAULT_FUSE;

	private float $force = 3.0;

	public function __construct(Location $location, ?CompoundTag $nbt = null) {
		parent::__construct($location, $nbt);
	}

	public function getName() : string {
		return "Creeper";
	}

	public static function getNetworkTypeId() : string {
		return EntityIds::CREEPER;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo {
		return new EntitySizeInfo(1.8, 0.6);
	}

	public function getDrops() : array {
		return [
			VanillaItems::GUNPOWDER()->setCount(mt_rand(0, 2))
		];
	}

	public function getXpDropAmount() : int {
		return 7;
	}

	public function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);

		$this->force = $nbt->getFloat("Force", 3.0);
		$this->ignited = $nbt->getByte("ignited", 0) !== 0;
		$this->powered = $nbt->getByte("powered", 0) !== 0;
		$this->fuse = $nbt->getShort("Fuse", self::DEFAULT_FUSE);
	}

	public function onUpdate(int $currentTick = 1) : bool {
		$return = parent::onUpdate($currentTick);

		if (!$this->canAttack()) {
			return $return;
		}

		if (!$this->ignited && $this->nearTarget()) {
			if ($this->fuse < self::DEFAULT_FUSE) {
				$this->fuse++;
				$this->explode = false;
			} else if ($this->getMovementSpeed() < self::INSANE_SPEED) {
				$this->setMovementSpeed(self::INSANE_SPEED);
			}

			return $return;
		}

		$this->setMovementSpeed(self::NORMAL_SPEED);
		if (!$this->explode) {
			$this->getWorld()->addSound($this->location, new FlintSteelSound());
		} else {
			$this->explode = true;
		}

		if (--$this->fuse < 0) {
			$this->flagForDespawn();
			$this->explode();
		}

		return $return;
	}

	public function onInteract(Player $player, Vector3 $clickPos) : bool {
		$itemHand = $player->getInventory()->getItemInHand();

		if ($itemHand instanceof FlintSteel) {
			$this->ignite();
			$itemHand->applyDamage(1);
			$player->broadcastAnimation(new ArmSwingAnimation($player));
			$this->getWorld()->addSound($this->location, new FlintSteelSound());
		}

		return parent::onInteract($player, $clickPos);
	}

	public function explode() : void {
		$ev = new EntityPreExplodeEvent($this, $this->force);
		$ev->call();

		if (!$ev->isCancelled()) {
			$explosion = new Explosion($this->getPosition(), $ev->getRadius(), $this);
			if ($ev->isBlockBreaking()) {
				$explosion->explodeA();
			}

			$explosion->explodeB();
		}
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void {
		parent::syncNetworkData($properties);

		$properties->setInt(EntityMetadataProperties::FUSE_LENGTH, $this->fuse);
		$properties->setGenericFlag(EntityMetadataFlags::IGNITED, $this->explode);
		$properties->setGenericFlag(EntityMetadataFlags::POWERED, $this->powered);
	}

	public function saveNBT() : CompoundTag {
		$nbt = parent::saveNBT();
		$nbt->setShort("Fuse", $this->fuse);
		$nbt->setFloat("Force", $this->force);
		$nbt->setByte("ignited", $this->ignited ? 1 : 0);
		$nbt->setByte("powered", $this->powered ? 1 : 0);

		return $nbt;
	}

	public function isPowered() : bool {
		return $this->powered;
	}

	public function setPowered(bool $value) : void {
		$this->powered = $value;
	}

	public function getForce() : float {
		return $this->force;
	}

	public function setForce(float $force) : void {
		$this->force = $force;
	}

	public function getFuse() : int {
		return $this->fuse;
	}

	public function setFuse(int $fuse) : void {
		$this->fuse = $fuse;
	}

	public function ignite() : void {
		$this->ignited = true;
		$this->setMovementSpeed(0);
	}

}