<?php

declare(strict_types=1);

namespace GraphClass\Utils;

trait ClassArrayShapeTrait {
	public function offsetExists(mixed $offset): bool {
		return isset($this->$offset);
	}

	public function offsetGet(mixed $offset): mixed {
		return $this->$offset ?? null;
	}

	public function offsetSet(mixed $offset, mixed $value): void {
		$this->$offset = $value;
	}

	public function offsetUnset(mixed $offset): void {
		unset($this->$offset);
	}

	public function count(): int {
		$count = 0;
		foreach ($this as $name => $value) {
			if (!str_starts_with($name, "_")) {
				$count++;
			}
		}
		return $count;
	}
}
