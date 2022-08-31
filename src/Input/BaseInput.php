<?php

declare(strict_types=1);

namespace GraphClass\Input;

use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ClassIteratorTrait;

abstract class BaseInput implements Input {
    use ClassArrayShapeTrait;
    use ClassIteratorTrait;

    public static function create(...$data): self {
        return new static();
    }

    public function serialize(): array {
        return [];
    }
}
