<?php

namespace GraphClass\Config\Trait;

use GraphClass\Resolver\VirtualResolver;
use GraphClass\Type\Attribute\Get;
use GraphClass\Type\Attribute\Set;
use GraphClass\Type\Attribute\VirtualType;
use ReflectionClass;
use ReflectionException;

trait ConfigVirtualsTrait {
    use ConfigFieldTrait;

    private static array $ignoredMethods = [];

    /** @var VirtualResolver[][] */
    public readonly array $virtuals;

    private function setVirtualsAndMethods(ReflectionClass $class): void {
        $virtuals = [];
        $added = false;
        foreach ($class->getMethods() as $method) {
            foreach ($method->getAttributes() as $attr) {
                $instance = $attr->newInstance();
                if ($instance instanceof Set || $instance instanceof Get) {
                    $fields = array_map(fn($f) => $this->fields[$f], $instance->fields);
                    $virtuals[$instance->name ?? $method->name][$instance::$type->name] = new VirtualResolver($fields, $method->name);
                    $added = true;
                }
            }

            if (!$added && !isset(self::$ignoredMethods[$method->name])) {
                $virtuals[$method->name][VirtualType::Get->name] = new VirtualResolver([], $method->name);
            }
        }

        $this->virtuals = $virtuals;
    }

    /**
     * @throws ReflectionException
     */
    public static function loadIgnoredMethodsByClass(string $class): void {
        $reflection = new ReflectionClass($class);
        foreach ($reflection->getMethods() as $method) {
            self::$ignoredMethods[$method->name] = $method->name;
        }
        self::$ignoredMethods["__construct"] = "__construct";
    }
}
