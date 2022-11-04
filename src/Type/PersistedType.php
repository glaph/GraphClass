<?php

declare(strict_types=1);

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

	public function retrieve(ResolverOptions $options): Deferred {
		$response = $this->getResponse($options);

		return new Deferred(function () use ($options, $response) {
			$response->hydrateType($options->field, $this);
			return parent::retrieve($options);
		});
	}

	public function persist(ResolverOptions $options): mixed {
		$class = explode("\\", static::class);
		$className = array_pop($class);
		$configType = ConfigFinder::type($options->info->parentType, $className);
		$connection = Connection::getInstance($configType->group->connectorClass, $configType->group->name);
		$builder = $connection->getBuilder($this);
		$properties = [];

		foreach ($this as $name => $property) {
			if (str_starts_with($name, '_')) {
				continue;
			}
			$properties[$name] = $property;
		}
		if (count($properties) === count($configType->ids)) {
			$onlyKeys = true;
			$idName = '';
			foreach ($configType->ids as $idName => $resolver) {
				$onlyKeys = $onlyKeys && isset($properties[$idName]);
			}
			if ($onlyKeys) {
				return $properties[$idName];
			}
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
