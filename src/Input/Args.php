<?php

declare(strict_types=1);

namespace GraphClass\Input;

final class Args extends BaseInput {
	public function __get(string $name): mixed {
		return null;
	}

	public function has(string $name): bool {
		return $this->offsetExists($name);
	}

	public function get(string $name): mixed {
		return $this->offsetGet($name);
	}
}
