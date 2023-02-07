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
use GraphClass\Type\Attribute\Id;
use GraphClass\Type\Attribute\Property;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

trait ConfigFieldTrait {
	/** @var FieldResolver[] */
	public readonly array $fields;
	/** @var FieldResolver[] */
	public readonly array $ids;

	/**
	 * @throws ReflectionException
	 * @throws FieldException
	 */
	private function setFields(ReflectionClass $class): void {
		$fields = [];
		$ids = [];
		foreach ($class->getProperties() as $property) {
			$type = $property->getType();
			if (!($type instanceof ReflectionNamedType)) {
				throw new FieldException("Property with #[Field] or #[ArrayField] Attribute must have single type");
			}
			$name = $property->name;
			$resolver = null;
			$hasId = false;

			$attrs = $property->getAttributes();
			foreach ($attrs as $attr) {
				$attr = $attr->newInstance();
				if ($attr instanceof Property) {
					$name = $attr->getName() ?: $name;
					$resolver = $resolver ?? $this->getResolver($property, $attr);
				}

				if ($attr instanceof Id) {
					$hasId = true;
				}
			}

			if ($resolver) {
				$fields[$name] = $resolver;

				if ($hasId) {
					$ids[$name] = $resolver;
				}
			}
		}
		$this->fields = $fields;
		$this->ids = $ids;
	}

	/**
	 * @throws ReflectionException
	 * @throws FieldException
	 */
	private function getResolver(ReflectionProperty $property, Property $field): ?FieldResolver {
		if ($field instanceof ArrayField) {
			return $this->getArrayFieldResolver($property, $field);
		}

		return $this->getFieldResolver($property);
	}

	/**
	 * @throws FieldException
	 * @throws ReflectionException
	 */
	private function getFieldResolver(ReflectionProperty $property): ?FieldResolver {
		$type = $property->getType();
		if ($type->isBuiltin()) {
			if ($type->getName() === "array") {
				throw new FieldException("An array property can't have #[Field] Attribute, use #[ArrayField] instead");
			}

			return new BuiltinFieldResolver($property->name, $type->getName());
		}

		$isResolvable = (new ReflectionClass($type->getName()))->implementsInterface(Resolvable::class);
		if (!$isResolvable) {
			throw new FieldException("If a property with #[Field] Attribute is a class, must implement Resolvable");
		}

		return ClassFieldResolver::createFromClassAndName($property->getType()->getName(), $property->name);
	}

	/**
	 * @throws FieldException
	 * @throws ReflectionException
	 */
	private function getArrayFieldResolver(ReflectionProperty $property, ArrayField $field): ?FieldResolver {
		$type = $property->getType();
		if (!$type->isBuiltin() && $type->getName() === "array") {
			throw new FieldException("The #[Field] Attribute only can be array type, use #[Field] instead");
		}
		$resolver = match ($field->type) {
			"bool", "boolean", "int", "integer", "float", "double", "string", "array" => new BuiltinFieldResolver($property->name, $field->type),
			default => ClassFieldResolver::createFromClassAndName($field->type, $property->name)
		};

		if ($resolver instanceof ClassFieldResolver) {
			$isResolvable = (new ReflectionClass($field->type))->implementsInterface(Resolvable::class);
			if (!$isResolvable) {
				throw new FieldException("If a property with #[ArrayField] Attribute has a class as type, that one must implement Resolvable");
			}
		}

		return new ArrayFieldResolver($property->name, $resolver);
	}
}
