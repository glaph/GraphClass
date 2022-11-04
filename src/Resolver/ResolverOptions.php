<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use GraphClass\Input\ArgsBuilder;

final class ResolverOptions {
	public function __construct(
		public readonly FieldInfo $field,
		public readonly ArgsBuilder $args
	) {
	}
}
