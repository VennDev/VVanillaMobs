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
use pocketmine\math\Vector3;

final class Unbeatable {

	public const CAN_PASS = 0;
	public const CANNOT_PASS = 1;

	public int $canPass;

	public Block $block;

	public ?Vector3 $addVector;

	public function __construct(Block $block, int $canPass = self::CAN_PASS, ?Vector3 $addVector = null) {
		$this->block = $block;
		$this->canPass = $canPass;
		$this->addVector = $addVector;
	}

	public function getBlock() : Block {
		return $this->block;
	}

	public function getCanPass() : int {
		return $this->canPass;
	}

	public function getAddVector() : ?Vector3 {
		return $this->addVector;
	}

}