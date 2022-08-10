<?php

namespace GraphClass\Type;

use App\Resolvers\FieldResolver;
use Exception;
use GraphClass\Resolver\BuiltinFieldResolver;
use GraphClass\Resolver\FieldInfo;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Connector\Connection;
use GraphClass\Type\Connector\Response;
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
            $response->hydrateType($options->field, $this);
            return parent::retrieve($options);
        });
    }

    public function persist(Group $group): Response\Keys
    {
        $connection = Connection::getInstance($group->connectorClass);
        $builder = $connection->getBuilder($group->name, $this);
        $builder->keys = isset($this->_keyValues) ? array_keys($this->_keyValues) : [];
        $builder->keyValues = isset($this->_keyValues) ? array_values($this->_keyValues) : [];

        foreach ($this as $name => $property) {
            if (str_starts_with($name, '_')) continue;
            if ($property instanceof Type) {
                $property = $property->persist($group)[0];
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
        if (!$options->group) throw new Exception("In PersistedType must exist attribute Group");
        $connection = Connection::getInstance($options->group->connectorClass);

        $builder = $connection->getBuilder($options->group->name, $this);
        $builder->keys = $options->group->keys;
        $builder->keyValues = $this->_keyValues;
        $builder->addField($options->field);

        return $connection->getResponse($this);
    }
}
