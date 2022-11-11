<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Response;

use GraphClass\Resolver\FieldInfo;
use GraphClass\Type\Connector\Connector;
use GraphClass\Type\Connector\Connection;
use GraphClass\Type\Connector\Request;
use GraphClass\Type\Connector\Response;
use GraphClass\Type\PersistedType;
use GraphClass\Type\Type;

final class Wrapper {
	public Request $request;
	public Response $response;

	public function __construct(
		private readonly Connection $connection,
		private readonly Connector $connector
	) {
	}

	public function hydrateType(FieldInfo $field, PersistedType $type): void {
		$this->hydrateResponse();

		foreach ($field->getFieldResolvers() as $resolver) {
			$fieldName = $resolver->property;
			if (!isset($type->$fieldName) && isset($this->response->items[$type->getHash()]->values[$fieldName])) {
				$type->$fieldName = $resolver->resolve($this->response->items[$type->getHash()]->values[$fieldName]);
			}
		}
	}

	public function submit(): ?Key {
		$this->connection->hydrateResponseWrappers();

		return $this->connector->submit($this->request, $this->response);
	}

	private function hydrateResponse(): void {
		$this->connection->hydrateResponseWrappers();

		if (!$this->response->items) {
			$this->connector->retrieve($this->request, $this->response);
		}
	}
}
