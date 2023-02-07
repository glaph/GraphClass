<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Response;

final class Item {
	public function __construct(
		public int $hash,
		public array $values
	) {
	}
}
