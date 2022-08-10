<?php

namespace GraphClass\Type;

use GraphClass\Config\ConfigType;
use GraphClass\Input\Input;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Resolver\Struct;

abstract class SubscriptionType extends QueryType {
    public function subscribe(ResolverOptions $options): void {

    }
}
