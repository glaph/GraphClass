<?php

declare(strict_types=1);

namespace GraphClass\Type;

use Exception;
use GraphClass\Config\ConfigType;
use GraphClass\Resolver\BuiltinFieldResolver;
use GraphClass\Resolver\FieldInfo;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Resolver\TypeResolver;
use GraphClass\Type\Connector\Connection;
use GraphClass\Type\Connector\Response;
use GraphQL\Deferred;

abstract class PersistedType extends FieldType {
	public function retrieve(ResolverOptions $options): Deferred {
		$response = $this->getResponse($options);

		return new Deferred(function () use ($options, $response) {
			$response->hydrateType($options->field, $this);
			return parent::retrieve($options);
		});
	}

	public function persist(ResolverOptions $options): self {
		/** @var ConfigType $configType */
		$configType = static::getConfig()->type;
		$connection = Connection::getInstance($configType->group->connectorClass, $configType->group->name);
		$builder = $connection->getBuilder($this);
		$count = 0;

		$onlyKeys = true;
		foreach ($this as $name => $property) {
			if (str_starts_with($name, '_')) {
				continue;
			}
			$count++;
			$onlyKeys = $onlyKeys && isset($configType->ids[$name]);
			if ($property instanceof Type) {
				$property = $property->persist($options);
				if ($property instanceof Type) {
					continue;
				}
			}
			$builder->addField(new FieldInfo($name, new BuiltinFieldResolver($name, '')), $property);
		}

		if ($onlyKeys && count($configType->ids) === $count) {
			return $this;
		}

		$key = $connection->getResponse($this)->submit();

		foreach ($key as $name => $value) {
			$this->$name = TypeResolver::getField($configType, $name)->field->resolve($value);
		}

		return $this;
	}

	public function serialize(): array {
		return [];
	}

	protected function getResponse(ResolverOptions $options): Response\Wrapper {
		if (!static::getConfig()->type->group) {
			throw new Exception("In PersistedType must exist attribute Group");
		}
		$connection = Connection::getInstance(static::getConfig()->type->group->connectorClass, static::getConfig()->type->group->name);

		$builder = $connection->getBuilder($this);
		$builder->addField($options->field);

		return $connection->getResponse($this);
	}
}
