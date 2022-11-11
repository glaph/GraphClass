<?php

declare(strict_types=1);

namespace GraphClass\Input;

use GraphClass\Config\ConfigType;
use GraphClass\Resolver\ClassFieldResolver;
use GraphClass\Resolver\FieldResolver;
use GraphClass\Resolver\Resolvable;
use GraphClass\Utils\ConfigFinder;
use GraphQL\Type\Definition\FieldArgument;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;

final class ArgsBuilder {
	private array $args = [];
	/** @var FieldArgument[] */
	private array $defs = [];
	private Args $parsed;

	public function setArgs(array $args): self {
		$this->args = $args;
		return $this;
	}

	/**
	 * @param FieldArgument[] $defs
	 */
	public function setDefs(array $defs): self {
		$this->defs = $defs;
		return $this;
	}

	public function getParsed(): Args {
		return $this->parsed;
	}

	public function build(): self {
		$this->parsed = new Args();
		foreach ($this->defs as $def) {
			$name = $def->name;
			if (!array_key_exists($name, $this->args)) {
				continue;
			}

			$type = $def->getType();
			$this->parsed->$name = self::resolve($type, $this->args[$name], $this->getResolver($type, $name));
		}

		return $this;
	}

	private function getResolver(Type $type, string $name): ?FieldResolver {
		if ($type instanceof NonNull) {
			$type = $type->getOfType();
		}
		if ($type instanceof InputObjectType) {
			$configInput = ConfigFinder::input($type);
			if ($configInput) {
				return new ClassFieldResolver($name, $configInput->class, []);
			}
		}

		return null;
	}

	private function resolve(Type $type, mixed $args, ?FieldResolver $resolver): mixed {
		$ret = $resolver ? $resolver->resolve($args) : $args;
		if ($type instanceof NonNull) {
			$type = $type->getOfType();
		}
		if ($ret instanceof Resolvable && $type instanceof InputObjectType) {
			$configInput = ConfigFinder::input($type);
			if (!$configInput) {
				return $ret;
			}
			foreach ($args as $propertyName => $value) {
				$ret->$propertyName = self::resolve($type->getField($propertyName)->getType(), $value, $configInput->fields[$propertyName]);
			}
		}

		return $ret;
	}
}
