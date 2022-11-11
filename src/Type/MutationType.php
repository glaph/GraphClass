<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Config\ConfigType;
use GraphClass\Input\Input;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Resolver\TypeResolver;

abstract class MutationType extends QueryType {
	public function mutate(ResolverOptions $options): void {
		$args = $options->args->getParsed();
		if ($method = $options->field->set?->method) {
			$this->$method($args);
			return;
		}

		foreach ($args as $input) {
			if ($type = $this->createType($input)) {
				$this->persist($input, $type, $options);
			}
		}
	}

	private function createType(Input $input): ?Type {
		$configType = $input::getMutatorConfig();
		if (!$configType) {
			return null;
		}
		$type = $this->instanceType($input, $configType);

		foreach ($input as $name => $value) {
			$field = TypeResolver::getField($configType, $name);
			if ($method = $field->set?->method) {
				$type->$method($value);
				continue;
			}

			if ($value instanceof Input && $childType = $this->createType($value)) {
				$type->$name = $childType;
				continue;
			}

			if ($fieldResolver = $field->field) {
				$type->$name = $fieldResolver->resolve($value);
			}
		}

		return $type;
	}

	private function instanceType(Input $input, ConfigType $configType): Type {
		$keys = [];
		foreach ($configType->ids as $name => $resolver) {
			if (!isset($input->$name)) {
				$keys = [];
				break;
			}

			$keys[$resolver->getProperty()] = $resolver->resolve($input->$name);
		}

		return $keys ? new $configType->class(...$keys) : (new \ReflectionClass($configType->class))->newInstanceWithoutConstructor();
	}

	private function persist(Input $input, Type $type, ResolverOptions $options): void {
		$type->persist($options);
		$configType = $type::getConfig()->type;
		foreach ($configType->ids as $name => $resolver) {
			$input->$name = $type->$name;
		}
	}
}
