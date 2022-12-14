<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Resolver\Resolvable;
use GraphClass\Resolver\ResolverOptions;

interface Type extends Resolvable, \Countable, \ArrayAccess {
	public function retrieve(ResolverOptions $options): mixed;
	public function persist(ResolverOptions $options): mixed;
	public function getHash(): int;
}
