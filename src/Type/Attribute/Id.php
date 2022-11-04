<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Id implements Property {
	public function __construct(
		public ?string $name = null
	) {
	}

	public function getName(): ?string {
		return $this->name;
	}
}
