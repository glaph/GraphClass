<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Resolver\Resolvable;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Utils\ResolvableTrait;

abstract class QueryType implements Resolvable {
	use ResolvableTrait;

	public function retrieve(ResolverOptions $options): mixed {
		if ($method = $options->field->get?->method) {
			return $this->$method($options->args->getParsed());
		}

		return null;
	}

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

	public function serialize(): array {
		return [];
	}
}
