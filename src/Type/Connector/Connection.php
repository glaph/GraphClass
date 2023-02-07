<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

use GraphClass\Type\Connector\Request\Keys;
use GraphClass\Type\Type;

final class Connection {
	/** @var self[][] */
	private static array $instances = [];
	/** @var Request\Builder[] */
	private array $builders = [];
	/** @var Response\Wrapper[] */
	private array $responses = [];

	/**
	 * @param class-string<Connector> $class
	 */
	public function __construct(
		private readonly string $class,
		private readonly string $group,
	) {
	}

	public function getBuilder(Type $type): Request\Builder {
		$hash = $type->getHash();
		if (!isset($this->builders[$hash])) {
			$ids = [];
			foreach ($type::getConfig()->type->ids as $name => $resolver) {
				$ids[$name] = $type->$name ?? null;
			}
			$this->builders[$hash] = new Request\Builder($ids);
		}

		return $this->builders[$hash];
	}

	public function getResponse(Type $type): Response\Wrapper {
		$hash = $type->getHash();
		if (!isset($this->responses[$hash])) {
			$this->responses[$hash] = new Response\Wrapper($this, new ($this->class));
		}

		return $this->responses[$hash];
	}

	public function hydrateResponseWrappers(): void {
		if (!isset(self::$instances[$this->class][$this->group])) {
			return;
		}

		$map = [];
		foreach ($this->builders as $hash => $builder) {
			$request = null;
			$response = null;
			foreach ($map as $values) {
				if ($values[0]->fields == $builder->fields) {
					list($request, $response) = $values;
					break;
				}
			}

			if (!$request) {
				$request = new Request($builder->fields, $this->group);
				$response = new Response();
				$map[$hash] = [$request, $response];
			}

			$request->keys->add($hash, $builder->ids);
			$this->responses[$hash]->request = $request;
			$this->responses[$hash]->response = $response;
		}

		unset(self::$instances[$this->class][$this->group]);
		unset($this->builders);
		unset($this->responses);
	}

	/**
	 * @param class-string<Connector> $class
	 */
	public static function getInstance(string $class, string $group): self {
		if (!isset(self::$instances[$class][$group])) {
			self::$instances[$class][$group] = new self($class, $group);
		}

		return self::$instances[$class][$group];
	}
}
