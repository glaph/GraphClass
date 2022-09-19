<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Request;

use Iterator;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<string, string[]|int[]>
 */
final class Keys implements IteratorAggregate {
	/** @var string[][] */
	public array $values = [];

	/**
	 * @param string[] $names
	 */
	public function __construct(
		public array $names
	) {
	}

	/**
	 * @param string[] $values
	 */
	public function addValues(array $values): void {
		if (count($values) !== count($this->names)) {
			throw new \Exception("Keys values must be the same number as the attribute Key");
		}
		$names = $this->names;
		$parsedValues = [];
		foreach ($names as $key => $name) {
			if (isset($values[$key])) {
				$parsedValues[$name] = $values[$key];
				continue;
			}
			if (isset($values[$name])) {
				$parsedValues[$name] = $values[$name];
				continue;
			}

			throw new \Exception("Key values must have the same order or name than the attribute Key");
		}
		$this->values[implode("-", $parsedValues)] = $parsedValues;
	}

	public function getParsedNames(): array {
		$names = [];
		foreach ($this->names as $name) {
			$names[$name] = null;
		}
		return $names;
	}

	/**
	 * @return Iterator<string, string[]|int[]>
	 */
	public function getIterator(): Iterator {
		return new \ArrayIterator($this->values);
	}
}
