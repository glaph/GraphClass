<?php

namespace GraphClass\Resolver;

interface Resolvable {
    public static function create(...$data): self;
    public function serialize(): array;
}
