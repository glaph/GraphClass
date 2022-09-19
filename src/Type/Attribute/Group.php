<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;
use GraphClass\Type\Connector\Connector;

#[Attribute(Attribute::TARGET_CLASS)]
final class Group {
	/**
	 * @param class-string<Connector> $connectorClass
	 */
	public function __construct(
		public string $name,
		public string $connectorClass,
		public array $keys
	) {
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["name"],
			$an_array["connectorClass"],
			$an_array["keys"],
		);
	}
}
