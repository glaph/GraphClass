<?php

namespace GraphClass\Type;

use GraphClass\Resolver\Resolvable;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Connector\Response\Keys;

interface Type extends Resolvable, \Iterator, \Countable, \ArrayAccess {
    public function retrieve(ResolverOptions $options): mixed;
    public function persist(Group $group): mixed;
    public function getHash(): string;
}
