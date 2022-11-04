<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Request;

use Iterator;
use IteratorAggregate;

final class Keys implements IteratorAggregate {
	/** @var string[][] */
	public array $values = [];

	/**
	 * @param string[] $values
	 */
	public function add(int $hash, array $values): void {
		if (!$values) {
			return;
		}

		$this->values[$hash] = $values;
	}

	/**
	 * @return Iterator<string, string[]|int[]>
	 */
	public function getIterator(): Iterator {
		return new \ArrayIterator($this->values);
	}
}
