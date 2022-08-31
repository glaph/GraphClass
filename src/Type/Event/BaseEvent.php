<?php

declare(strict_types=1);

namespace GraphClass\Type\Event;

use GraphClass\Type\Type;

abstract class BaseEvent {
    public readonly Type $target;
    public readonly array $payload;
}
