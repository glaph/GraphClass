<?php

declare(strict_types=1);

namespace GraphClass;

use JsonException;

final class SchemaRequest {
	public function __construct(
		public string $query,
		public ?array $variables = null
	) {
	}

	/**
	 * @throws JsonException
	 */
	public static function fromJSON(string $json): self {
		$json = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
		if (!isset($json["query"])) {
			throw new \Exception("Query is mandatory");
		}

		return new self(
			$json["query"],
			$json["variables"] ?? null
		);
	}
}
