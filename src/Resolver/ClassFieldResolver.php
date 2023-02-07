<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use Exception;
use ReflectionClass;

final class ClassFieldResolver implements FieldResolver {
	/**
	 * @param class-string<Resolvable> $class
	 */
	public function __construct(
		public string $property,
		public string $class,
		public array $constructParams
	) {
	}

	public function resolve($data): mixed {
		if ($data === null) {
			return null;
		}
		if (!class_exists($this->class)) {
			throw new Exception("Property $this->property has a class ($this->class) that doesn't exist");
		}
		$newData = is_array($data) ? $data : [$data];
		$newData = array_intersect_key($newData, $this->constructParams);

		if (count($newData) === count($this->constructParams)/2) {
			return new ($this->class)(...$newData);
		}

		return (new \ReflectionClass($this->class))->newInstanceWithoutConstructor();
	}

	public function getProperty(): string {
		return $this->property;
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["property"],
			$an_array["class"],
			$an_array["construct"]
		);
	}

	public static function createFromClassAndName(string $className, string $propertyName): self {
		$reflection = new ReflectionClass($className);
		$constructParams = [];
		$params = $reflection->getConstructor()?->getParameters() ?? [];
		foreach ($params as $index => $param) {
			$constructParams[$param->getName()] = null;
			$constructParams[$index] = null;
		}

		return new self(
			property: $propertyName,
			class: $className,
			constructParams: $constructParams
		);
	}
}
