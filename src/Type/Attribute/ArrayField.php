<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ArrayField {
	public function __construct(
		public string $type,
		public ?string $name = null
	) {
	}
}
