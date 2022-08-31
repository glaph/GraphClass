<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Field {
    public function __construct(
        public ?string $name = null
    ) {
    }
}
