<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use Exception;

final class BuiltinFieldResolver implements FieldResolver {
	public function __construct(
		public string $property,
		public string $type
	) {
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["property"],
			$an_array["type"]
		);
	}

	public function resolve($data): mixed {
		if ($data === null) {
			return null;
		}
		$changed = settype($data, $this->type);
		if (!$changed) {
			throw new Exception("Fail to set $this->property to $this->type");
		}

		return $data;
	}
}
