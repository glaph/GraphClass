<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Config\ConfigType;
use GraphClass\Input\Input;
use GraphClass\Resolver\ResolverOptions;

abstract class MutationType extends QueryType {
	public function mutate(ResolverOptions $options): void {
		$args = $options->args->getParsed();
		if ($method = $options->getField()->set?->method) {
			$this->$method($args);
			return;
		}

		foreach ($args as $input) {
			if ($options->args->hasMutator($input::class)) {
				$this->persist($input, $this->createType($input, $options), $options);
			}
		}

		$args->clearIterator();
	}

	private function createType(Input $input, ResolverOptions $options): Type {
		$configType = $options->args->getMutator($input::class);
		$type = $this->instanceType($input, $configType);
		$resolver = new ResolverOptions($configType, $options->args, $options->info);

		foreach ($input as $name => $value) {
			$field = $resolver->getField($name);
			if ($method = $field->set?->method) {
				$tmpType = new ($configType->class);
				$tmpType->$method($value);
				$this->extractProperties($type, $tmpType);
				continue;
			}

			$type->$name = $this->getValue($value, $options);
		}

		return $type;
	}

	private function instanceType(Input $input, ConfigType $configType): Type {
		$keys = [];
		if ($configType->group?->keys) {
			foreach ($configType->group->keys as $key) {
				if (!isset($input->$key)) {
					$keys = [];
					break;
				}

				$keys[$key] = $input->$key;
			}
		}

		return $keys ? $configType->class::create(...$keys) : new ($configType->class);
	}

	private function getValue(mixed $value, ResolverOptions $options): mixed {
		if ($value instanceof Input) {
			return $this->createType($value, $options);
		}

		return $value;
	}

	private function persist(Input $input, Type $type, ResolverOptions $options): void {
		$key = $type->persist($options);
		$configType = $options->args->getMutator($input::class);
		if ($configType->group) {
			$keyName = $configType->group->keys[0];
			$input->$keyName = $key;
		}
	}

	private function extractProperties(Type $type, Type $tempType): void {
		$vars = get_object_vars($tempType);
		foreach ($vars as $name => $value) {
			$type->$name = $value;
		}
	}
}
