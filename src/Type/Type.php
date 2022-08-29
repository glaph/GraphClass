<?php

namespace GraphClass\Type;

use GraphClass\Resolver\Resolvable;
use GraphClass\Resolver\ResolverOptions;

interface Type extends Resolvable, \Iterator, \Countable, \ArrayAccess {
    public function retrieve(ResolverOptions $options): mixed;
    public function persist(ResolverOptions $options): mixed;
    public function getHash(): string;
}
