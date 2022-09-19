<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

interface Resolvable {
	public static function create(...$data): self;
	public function serialize(): array;
}
