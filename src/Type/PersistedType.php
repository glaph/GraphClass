<?php

namespace GraphClass\Type;

use Exception;
use GraphClass\Resolver\BuiltinFieldResolver;
use GraphClass\Resolver\FieldInfo;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Type\Connector\Connection;
use GraphClass\Type\Connector\Response;
use GraphClass\Utils\ConfigFinder;
use GraphQL\Deferred;

abstract class PersistedType extends FieldType {
    /** @var string[] */
    public readonly array $_keyValues;
    public readonly string $_hash;

    /**
     * @param string[] $keys
     */
    public static function create(...$keys): static {
        if (self::class === static::class) throw new Exception("Do not try to instantiate an abstract class :)");
        $obj = new static;
        $obj->_keyValues = $keys;
        $obj->_hash = implode("-", $keys);

        return $obj;
    }

    public function retrieve(ResolverOptions $options): Deferred {
        $response = $this->getResponse($options);

        return new Deferred(function() use($options, $response) {
            $response->hydrateType($options->getField(), $this);
            return parent::retrieve($options);
        });
    }

    public function persist(ResolverOptions $options): mixed {
        $class = explode("\\", static::class);
        $className = array_pop($class);
        $configType = ConfigFinder::type($options->info->parentType, $className);
        $connection = Connection::getInstance($configType->group->connectorClass, $configType->group->name);
        $builder = $connection->getBuilder($this);
        $builder->keys = isset($this->_keyValues) ? array_keys($this->_keyValues) : [];
        $builder->keyValues = isset($this->_keyValues) ? array_values($this->_keyValues) : [];
        $properties = [];

        foreach ($this as $name => $property) {
            if (str_starts_with($name, '_')) continue;
            $properties[$name] = $property;
        }
        if (count($properties) === count($configType->group->keys)) {
            $onlyKeys = true;
            foreach ($configType->group->keys as $keyName) {
                $onlyKeys = $onlyKeys && isset($properties[$keyName]);
            }
            if ($onlyKeys) return $properties[$configType->group->keys[0]];
        }

        foreach ($properties as $name => $property) {
            if ($property instanceof Type) {
                $property = $property->persist($options);
            }

            $builder->addField(new FieldInfo($name, new BuiltinFieldResolver($name, '')), $property);
        }

        $response = $connection->getResponse($this);

        return $response->submit();
    }

    public function serialize(): array {
        return [];
    }

    public function getHash(): string {
        return $this->_hash ?? "new";
    }

    protected function getResponse(ResolverOptions $options): Response\Wrapper {
        if (!$options->type->group) throw new Exception("In PersistedType must exist attribute Group");
        $connection = Connection::getInstance($options->type->group->connectorClass, $options->type->group->name);

        $builder = $connection->getBuilder($this);
        $builder->keys = $options->type->group->keys;
        $builder->keyValues = $this->_keyValues;
        $builder->addField($options->getField());

        return $connection->getResponse($this);
    }
}
