<?php

declare(strict_types=1);

namespace GraphClass\Config\Trait;

use GraphClass\Config\Exception\FieldException;
use GraphClass\Resolver\ArrayFieldResolver;
use GraphClass\Resolver\BuiltinFieldResolver;
use GraphClass\Resolver\ClassFieldResolver;
use GraphClass\Resolver\FieldResolver;
use GraphClass\Resolver\Resolvable;
use GraphClass\Type\Attribute\ArrayField;
use GraphClass\Type\Attribute\Field;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

trait ConfigFieldTrait {
	/** @var FieldResolver[] */
	public readonly array $fields;

	/**
	 * @throws ReflectionException
	 * @throws FieldException
	 */
	private function setFields(ReflectionClass $class): void {
		$fields = [];
		foreach ($class->getProperties() as $property) {
			$type = $property->getType();
			if (!($type instanceof ReflectionNamedType)) {
				throw new FieldException("Property with #[Field] or #[ArrayField] Attribute must have single type");
			}

			$attr = $property->getAttributes(Field::class);
			$field = $attr ? $attr[0]?->newInstance() : null;
			$resolver = $this->getFieldResolver($type, $property->name, $field);

			$attr = $property->getAttributes(ArrayField::class);
			$field = $attr ? $attr[0]?->newInstance() : null;
			$resolver = $resolver ?? $this->getArrayFieldResolver($type, $property->name, $field);

			if (!$resolver) {
				continue;
			}
			$fields[$field->name ?? $property->name] = $resolver;
		}
		$this->fields = $fields;
	}

	/**
	 * @throws FieldException
	 * @throws ReflectionException
	 */
	private function getFieldResolver(ReflectionNamedType $type, string $propertyName, ?Field $field): ?FieldResolver {
		if (!$field) {
			return null;
		}
		if ($type->isBuiltin()) {
			if ($type->getName() === "array") {
				throw new FieldException("An array property can't have #[Field] Attribute, use #[ArrayField] instead");
			}
			return new BuiltinFieldResolver($propertyName, $type->getName());
		}

		$isResolvable = (new ReflectionClass($type->getName()))->implementsInterface(Resolvable::class);
		if (!$isResolvable) {
			throw new FieldException("If a property with #[Field] Attribute is a class, must implement Resolvable");
		}
		return new ClassFieldResolver($propertyName, $type->getName());
	}

	/**
	 * @throws FieldException
	 * @throws ReflectionException
	 */
	private function getArrayFieldResolver(ReflectionNamedType $type, string $propertyName, ?ArrayField $field): ?FieldResolver {
		if (!$field) {
			return null;
		}
		if (!$type->isBuiltin() && $type->getName() === "array") {
			throw new FieldException("The #[Field] Attribute only can be array type, use #[Field] instead");
		}
		$resolver = match ($field->type) {
			"bool", "boolean", "int", "integer", "float", "double", "string", "array" => new BuiltinFieldResolver($propertyName, $field->type),
			default => new ClassFieldResolver($propertyName, $field->type)
		};

		if ($resolver instanceof ClassFieldResolver) {
			$isResolvable = (new ReflectionClass($field->type))->implementsInterface(Resolvable::class);
			if (!$isResolvable) {
				throw new FieldException("If a property with #[ArrayField] Attribute has a class as type, that one must implement Resolvable");
			}
		}

		return new ArrayFieldResolver($propertyName, $resolver);
	}
}
