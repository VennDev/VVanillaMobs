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

final class Direction {

	public const NORTH = [0, -1];

	public const SOUTH = [0, 1];

	public const EAST = [1, 0];

	public const WEST = [-1, 0];

	public const NORTH_EAST = [1, -1];

	public const NORTH_WEST = [-1, -1];

	public const SOUTH_EAST = [1, 1];

	public const SOUTH_WEST = [-1, 1];

	public const DIRECTIONS = [
		'NORTH' => self::NORTH,
		'SOUTH' => self::SOUTH,
		'EAST' => self::EAST,
		'WEST' => self::WEST,
		'NORTH_EAST' => self::NORTH_EAST,
		'NORTH_WEST' => self::NORTH_WEST,
		'SOUTH_EAST' => self::SOUTH_EAST,
		'SOUTH_WEST' => self::SOUTH_WEST
	];

	public static function getDirection(int $x, int $z) : string {
		foreach (self::DIRECTIONS as $direction => $value) {
			if ($value[0] === $x && $value[1] === $z) {
				return $direction;
			}
		}

		return 'UNKNOWN';
	}

	public static function getOpposite(string $direction) : ?array {
		return match ($direction) {
			'NORTH' => ['NORTH_EAST', 'NORTH_WEST'],
			'SOUTH' => ['SOUTH_EAST', 'SOUTH_WEST'],
			'EAST' => ['NORTH_EAST', 'SOUTH_EAST'],
			'WEST' => ['NORTH_WEST', 'SOUTH_WEST'],
			default => null
		};
	}

}
