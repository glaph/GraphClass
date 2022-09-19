<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

interface FieldResolver {
	public function resolve($data): mixed;
}
