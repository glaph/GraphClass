<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Resolver\ResolverOptions;

abstract class SubscriptionType extends QueryType {
	public function subscribe(ResolverOptions $options): void {
	}
}
