<?php

declare(strict_types=1);

namespace App\Connector;

use GraphClass\Type\Connector\Connector;
use GraphClass\Type\Connector\Request;
use GraphClass\Type\Connector\Response;

class JsonDBConnector implements Connector {
	private readonly string $root;

	public function __construct() {
		$this->root = dirname(__DIR__, 2);
	}

	public function retrieve(Request $request, Response $response): void {
		$data = $this->readJsonFile($request->group);
		if (!$data) {
			return;
		}

		foreach ($request->keys as $hash => $key) {
			$values = $data;
			$keyValue = null;
			foreach ($key as $keyValue) {
				if (!isset($values[$keyValue])) {
					$values = null;
					break;
				}
				$values = $values[$keyValue];
			}
			if (!$values) {
				continue;
			}

			$itemValues = [];
			foreach ($request->fields as $fieldName => $value) {
				$this->tryToFindInOtherFile($values, $fieldName, $request->group, $keyValue);
				$itemValues[$fieldName] = $values[$fieldName];
			}
			$response->addItem(new Response\Item($hash, $itemValues));
		}
	}

	public function submit(Request $request, Response $response): ?Response\Key {
		$data = $this->readJsonFile($request->group);
		if (!$data) {
			return null;
		}

		$values = [];
		foreach ($request->fields as $fieldName => $value) {
			$values[$fieldName] = $value;
		}
		$key = null;
		foreach ($request->keys as $hash => $keys) {
			$key = $this->recursiveDataInjection($keys, $data, $values);
		}

		$jsonPath = "$this->root/db/$request->group.json";
		$encoded = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
		file_put_contents($jsonPath, $encoded);

		return $key;
	}

	private function recursiveDataInjection(array &$keys, ?array &$data, array $values): Response\Key {
		$keyName = key($keys);
		if ($keyName === null) {
			$data = array_merge($data ?? [], $values);
			return new Response\Key();
		}
		$value = current($keys) ?? count($data);
		unset($keys[$keyName]);
		$values[$keyName] = $value;
		$key = $this->recursiveDataInjection($keys, $data[$value], $values);

		return $key->addKey($keyName, $value);
	}

	private function readJsonFile(string $name): ?array {
		$jsonPath = "$this->root/db/$name.json";
		$json = file_get_contents($jsonPath);
		if ($json === false) {
			return null;
		}

		return (array) json_decode($json, true, flags: JSON_THROW_ON_ERROR);
	}

	private function tryToFindInOtherFile(array &$values, string $fieldName, string $groupName, null|string|int $keyValue): void {
		if (!isset($values[$fieldName]) || $keyValue === null) {
			$data = $this->readJsonFile($fieldName);
			if (!$data) {
				return;
			}

			$values[$fieldName] = [];
			foreach ($data as $item) {
				if (!isset($item[$groupName]) || $item[$groupName] !== $keyValue) {
					continue;
				}
				$values[$fieldName][] = $item[$groupName];
			}
		}
	}
}
