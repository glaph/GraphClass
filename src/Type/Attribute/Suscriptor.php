<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Suscriptor {
	/**
	 * @param class-string<> $listener
	 */
	public function __construct(
		public string $listener,
	) {
	}

	public static function __set_state(array $an_array): self {
		return new self(
			$an_array["listener"],
		);
	}
}
