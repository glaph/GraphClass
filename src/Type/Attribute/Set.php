<?php

declare(strict_types=1);

namespace GraphClass\Type\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Set {
    public static VirtualType $type = VirtualType::Set;

    public array $fields = [];

    public function __construct(
        public ?string $name = null
    ) {
    }
}
