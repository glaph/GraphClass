<?php

namespace GraphClass\Resolver;

use GraphClass\Type\Type;

interface FieldResolver {
    public function resolve($data): mixed;
}
