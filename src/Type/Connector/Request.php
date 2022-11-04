<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

final class Request {
	public Request\Keys $keys;

	public function __construct(
		public array $fields,
		public string $group
	) {
		$this->keys = new Request\Keys();
	}
}
