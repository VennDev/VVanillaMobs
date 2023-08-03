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

namespace vennv\vvanillamobs\utils\astar;

use pocketmine\math\Math;

final class Node {

	public float $x;

	public float $z;

	public float $g = 0;

	public float $h = 0;

	public float $f = 0;

	public ?Node $parent = null;

	public function __construct(float $x, float $z) {
		$this->x = $x;
		$this->z = $z;
	}

	public function equals(Node $node) : bool {
		return $this->getDistance($node) < 20;
	}

	public function getDistance(Node $node) : float {
		$dx = abs($this->x - $node->x);
		$dz = abs($this->z - $node->z);

		if ($dx > $dz) {
			return 14 * $dz + 10 * ($dx - $dz);
		}

		return 14 * $dx + 10 * ($dz - $dx);
	}

	public function calculateF() : void {
		$this->f = $this->g + $this->h;
	}

	public function setG(float $g) : void {
		$this->g = $g;
	}

	public function setH(float $h) : void {
		$this->h = $h;
	}

	public function setParent(Node $node) : void {
		$this->parent = $node;
	}

	public function __toString() : string {
		return "Node(x: {$this->x}, z: {$this->z}, g: {$this->g}, h: {$this->h}, f: {$this->f})";
	}

}