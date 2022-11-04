<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

final class ArrayFieldResolver implements FieldResolver {
	public function __construct(
		public string $property,
		public FieldResolver $itemResolver
	) {
	}

	public function resolve($data): ?array {
		if ($data === null) {
			return null;
		}
		$newData = [];
		foreach ($data as $key => $item) {
			$newData[$key] = $this->itemResolver->resolve($item);
		}

		return $newData;
	}

	public function getProperty(): string {
		return $this->property;
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["property"],
			$an_array["type"]
		);
	}
}
