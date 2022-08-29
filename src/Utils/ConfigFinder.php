<?php

namespace GraphClass\Utils;

use GraphClass\Config\ConfigInput;
use GraphClass\Config\ConfigNode;
use GraphClass\Config\ConfigScalar;
use GraphClass\Config\ConfigType;
use GraphQL\Type\Definition\Type;

class ConfigFinder {
    public const FUNC_NAME = "getConfigNode";

    public static function root(Type $type, ?string $name = null): ?ConfigType {
        return self::node($type, $name)->root ?? null;
    }

    public static function type(Type $type, ?string $name = null): ?ConfigType {
        return self::node($type, $name)->type ?? null;
    }

    public static function input(Type $type, ?string $name = null): ?ConfigInput {
        return self::node($type, $name)->input ?? null;
    }

    public static function scalar(Type $type, ?string $name = null): ?ConfigScalar {
        return self::node($type, $name)->scalar ?? null;
    }

    public static function node(Type $type, ?string $name = null): ?ConfigNode {
        if (isset($type->config[self::FUNC_NAME])) return $type->config[self::FUNC_NAME]($name ?? $type->name);

        return null;
    }
}
