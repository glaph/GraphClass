<?php

namespace GraphClass\Config;

abstract class Cache
{
    public abstract static function __set_state(array $an_array): self;
}
