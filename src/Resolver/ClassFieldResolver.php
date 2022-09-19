<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use Exception;

final class ClassFieldResolver implements FieldResolver {
	/**
	 * @param class-string<Resolvable> $class
	 */
	public function __construct(
		public string $property,
		public string $class
	) {
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["property"],
			$an_array["class"]
		);
	}

	public function resolve($data): mixed {
		if ($data === null) {
			return null;
		}
		if (!class_exists($this->class)) {
			throw new Exception("Property $this->property has a class ($this->class) that doesn't exist");
		}
		$newData = is_array($data) ? $data : [$data];

		return $this->class::create(...$newData);
	}
}
