<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Get {
	public static VirtualType $type = VirtualType::Get;

	/**
	 * @param string|string[] $fields
	 */
	public function __construct(
		public array $fields = [],
		public ?string $name = null
	) {
	}
}
