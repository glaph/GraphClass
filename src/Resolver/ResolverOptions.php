<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use GraphClass\Config\ConfigType;
use GraphClass\Input\ArgsBuilder;
use GraphClass\Type\Attribute\VirtualType;
use GraphQL\Type\Definition\ResolveInfo;

final class ResolverOptions {

	public function __construct(
		public readonly FieldInfo $field,
		public readonly ArgsBuilder $args
	) {
	}
}
