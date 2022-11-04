<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Config\ConfigNode;
use GraphClass\Resolver\Resolvable;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Utils\ResolvableTrait;
use GraphQL\Type\Definition\ScalarType;

abstract class Scalar implements Resolvable {
	use ResolvableTrait;

	public function serialize(): array {
		return [];
	}
}
