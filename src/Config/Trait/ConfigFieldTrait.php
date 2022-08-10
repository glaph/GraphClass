<?php

namespace GraphClass\Config\Trait;

use GraphClass\Config\Exception\FieldException;
use GraphClass\Resolver\BuiltinFieldResolver;
use GraphClass\Resolver\ClassFieldResolver;
use GraphClass\Resolver\FieldResolver;
use GraphClass\Resolver\Resolvable;
use GraphClass\Type\Attribute\Field;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

trait ConfigFieldTrait
{
    /** @var FieldResolver[] */
    public readonly array $fields;

    /**
     * @throws ReflectionException
     * @throws FieldException
     */
    private function setFields(ReflectionClass $class): void {
        $fields = [];
        foreach ($class->getProperties() as $property) {
            $field = $property->getAttributes(Field::class)[0]?->newInstance();
            $type = $property->getType();
            if (!($field instanceof Field)) continue;
            if (!($type instanceof ReflectionNamedType)) throw new FieldException("Property with #[Field] Attribute must have single type");
            if ($type->isBuiltin()) {
                $fields[$field->name ?? $property->name] = new BuiltinFieldResolver($property->name, $type->getName());
            } else {
                $isResolvable = (new ReflectionClass($type->getName()))->implementsInterface(Resolvable::class);
                if (!$isResolvable) throw new FieldException("If a property with #[Field] Attribute is a class, must implement Resolvable");
                $fields[$field->name ?? $property->name] = new ClassFieldResolver($property->name, $type->getName(), []);
            }
        }
        $this->fields = $fields;
    }
}
