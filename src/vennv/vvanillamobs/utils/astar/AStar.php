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

use Generator;
use pocketmine\block\Block;
use Throwable;
use pocketmine\block\Air;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;

/**
 * Class AStar
 * @package vennv\vvanillamobs\utils\astar
 *
 * This class simply implements the A* algorithm.
 */
final class AStar {

	public Node $start;

	public Node $end;

	public array $openList = [];

	public array $closedList = [];

	public array $blocksBlocked = [];

	public World $world;

	public float $y;

	/**
	 * AStar constructor.
	 * @param array $start
	 * @param array $end
	 * @param World $world
	 * @param float $y
	 * @param array<int, Block> $blocksBlocked
	 */
	public function __construct(array $start, array $end, World $world, float $y, array $blocksBlocked = []) {
		$this->end = new Node(...$end);
		$this->start = new Node(...$start);
		$this->start->g = $this->start->getDistance($this->start);
		$this->start->h = $this->start->getDistance($this->end);
		$this->start->f = $this->start->g + $this->start->h;
		$this->openList[] = $this->start;
		$this->world = $world;
		$this->y = $y;
		$this->blocksBlocked = $blocksBlocked;
	}

	/**
	 * @throws Throwable
	 *
	 * Use Promise to wait for the path to be found and non-blocking.
	 */
	public function find() : Promise {
		return new Promise(function ($resolve) : void {
			while (count($this->openList) > 0) {
				$current = $this->openList[0];
				$currentIndex = 0;

				foreach ($this->openList as $index => $node) {
					if ($node->f < $current->f || ($node->f === $current->f && $node->h < $current->h)) {
						$current = $node;
						$currentIndex = $index;
					}
				}

				if ($currentIndex !== 0) {
					unset($this->openList[$currentIndex]);
				}

				$this->closedList[] = $current;

				/** @var Node $current */
				if ($current->equals($this->end)) {

					$count = 0;
					$path = [];
					$currentNode = $this->end;
					while ($currentNode !== null && $currentNode !== $this->start && ++$count < 50) {
						$path[] = $currentNode;
						$currentNode = $currentNode->parent;

						FiberManager::wait();
					}

					$resolve(array_reverse($path));
					break;
				}

				/** @var Neighbor $neighbor */
				foreach ($this->getNeighbors($current) as $neighbor) {

					$node = $neighbor->getNode();
					$costToNeighbor = $current->g + $current->getDistance($node);
					if (!in_array($neighbor, $this->openList) || $costToNeighbor < $node->g) {
						$node->setH($node->getDistance($this->end));
						$node->setG($costToNeighbor);
						$node->setParent($current);
						$node->calculateF();

						if (!in_array($node, $this->openList)) {
							$this->openList[] = $node;
						}
					}

					FiberManager::wait();
				}

				FiberManager::wait();
			}
		});
	}

	protected function getNeighbors(Node $node) : Generator {
		$directionsBlocked = [];

		for ($x = -1; $x <= 1; ++$x) {
			for ($z = -1; $z <= 1; ++$z) {
				if ($x === 0 && $z === 0) {
					continue;
				}

				if ($x < 0 && $z < 0) {
					continue;
				}

				$direction = Direction::getDirection($x, $z);

				$neighbor = new Node($node->x + $x, $node->z + $z);

				$neighbor->g = $neighbor->getDistance($this->start);
				$neighbor->h = $neighbor->getDistance($this->end);
				$neighbor->f = $neighbor->g + $neighbor->h;

				$vector = new Vector3($neighbor->x, $this->y + 1, $neighbor->z);
				if (!$this->world->getBlock($vector) instanceof Air) {
					$directionsBlocked[$direction] = true;

					$directionOpposite = Direction::getOpposite($direction);
					if ($directionOpposite !== null) {
						foreach ($directionOpposite as $dir) {
							$directionsBlocked[$dir] = true;
						}
					}
				}

				$vector = new Vector3($neighbor->x, $this->y - 0.5, $neighbor->z);
				foreach ($this->blocksBlocked as $block) {
					if ($this->world->getBlock($vector)->getTypeId() === $block->getTypeId()) {
						$directionsBlocked[$direction] = true;
						break;
					}
				}

				if (isset($directionsBlocked[$direction])) {
					continue;
				}

				yield new Neighbor($neighbor, $direction);
			}
		}
	}

}
