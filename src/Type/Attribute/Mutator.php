<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Mutator {
	public function __construct(
		public string $typeClass
	) {
	}
}
