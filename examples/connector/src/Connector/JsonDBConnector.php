<?php

declare(strict_types=1);

namespace App\Connector;

use GraphClass\Type\Connector\Connector;
use GraphClass\Type\Connector\Request;
use GraphClass\Type\Connector\Response;

class JsonDBConnector implements Connector {
	public function retrieve(Request $request, Response $response): void {
		$root = dirname(__DIR__, 2);
		$jsonPath = "$root/db/$request->group.json";
		$json = file_get_contents($jsonPath);

		if ($json === false) {
			return;
		}

		$data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
		foreach ($request->keys as $hash => $key) {
			$itemValues = $data;
			foreach ($key as $keyValue) {
				if (!isset($itemValues[$keyValue])) {
					$itemValues = null;
					break;
				}
				$itemValues = $itemValues[$keyValue];
			}
			if (!$itemValues) {
				continue;
			}

			$response->addItem(new Response\Item($hash, $itemValues));
		}
	}

	public function submit(Request $request, Response $response): ?int {
		return null;
	}
}
