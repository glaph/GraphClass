<?php

namespace GraphClass\Input;

use GraphClass\Config\ConfigInput;
use GraphClass\Input\BaseInput;
use GraphQL\Type\Definition\FieldArgument;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use JetBrains\PhpStorm\Internal\TentativeType;

final class Args extends BaseInput {
    public function __get(string $name): mixed {
        return null;
    }

    public function has(string $name): bool {
        return $this->offsetExists($name);
    }

    public function get(string $name): mixed {
        return $this->offsetGet($name);
    }
}
