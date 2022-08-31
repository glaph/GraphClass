<?php

declare(strict_types=1);

namespace GraphClass\Config;

abstract class Cache {
    abstract public static function __set_state(array $an_array): self;
}
