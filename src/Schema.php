<?php

declare(strict_types=1);

namespace GraphClass;

use GraphClass\Config\Config;
use GraphClass\Config\ConfigNode;
use GraphClass\Resolver\TypeResolver;
use GraphClass\Type\QueryType;
use GraphClass\Utils\ConfigFinder;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Utils\BuildSchema;

final class Schema {
	private \GraphQL\Type\Schema $schema;
	/** @var QueryType[] */
	private array $roots = [];

	public function __construct(
		Config $config
	) {
		$this->hydrateRoots($config);
		$typeConfigDecorator = function ($typeConfig) use ($config) {
			$this->config($typeConfig, $config);

			return $typeConfig;
		};

		$this->schema = BuildSchema::build($config->document, $typeConfigDecorator);
	}

	public function __invoke(SchemaRequest $request): ExecutionResult {
		return GraphQL::executeQuery($this->schema, $request->query, $this->roots, variableValues: $request->variables);
	}

	private function hydrateRoots(Config $config): void {
		foreach ($config->nodes as $node) {
			if (isset($node->root)) {
				$instance = new ($node->root->class);
				if (!($instance instanceof QueryType)) {
					throw new \Exception("Root types must be children of QueryType. $node->name it isn't");
				}
				$this->roots[$node->name] = $instance;
			}
		}

		if (!$this->roots) {
			throw new \Exception("There must be at least a root type");
		}
	}

	private function config(array &$typeConfig, Config $config): void {
		$name = $typeConfig["name"];
		/** @var Node $astNode */
		$astNode = $typeConfig["astNode"];
		$configNode = $config->nodes[$name] ?? null;

		if (!$configNode) {
			throw new \Exception("Doesn't exist any class implementation for $name");
		}

		$nodeConfig = match ($astNode->kind) {
			NodeKind::OBJECT_TYPE_DEFINITION => $this->configType($configNode),
			NodeKind::INPUT_OBJECT_TYPE_DEFINITION => $this->configInput($configNode, $config),
			NodeKind::SCALAR_TYPE_DEFINITION => $this->configScalar($configNode, $config),
			default => throw new \Exception("Doesn't exist any class implementation for $name")
		};
		$nodeConfig[ConfigFinder::FUNC_NAME] = fn ($nodeName) => $config->nodes[$nodeName] ?? null;

		$typeConfig = array_merge($typeConfig, $nodeConfig);
	}

	private function configType(ConfigNode $configNode): array {
		$typeConfig = [];

		if (isset($this->roots[$configNode->name])) {
			if (!isset($configNode->root)) {
				throw new \Exception("Class implementation for root type $configNode->name does not exist");
			}
			$typeConfig["resolveField"] = TypeResolver::getRootResolver($configNode->root);
		} else {
			if (!isset($configNode->type)) {
				throw new \Exception("Class implementation for type $configNode->name does not exist");
			}
			$typeConfig["resolveField"] = TypeResolver::getTypeResolver($configNode->type);
		}

		return $typeConfig;
	}

	private function configInput(ConfigNode $configNode, Config $config): array {
		if (!isset($configNode->input)) {
			throw new \Exception("Class implementation for input $configNode->name does not exist");
		}
		if (isset($configNode->input->mutator)) {
			if (!isset($config->nodes[$configNode->input->mutator]->type)) {
				throw new \Exception("Input $configNode->name has a non existent mutator class");
			}
		}

		return [];
	}

	private function configScalar(ConfigNode $configNode, Config $config): array {
		if (!isset($configNode->scalar)) {
			throw new \Exception("Class implementation for input $configNode->name does not exist");
		}

		return [];
	}
}
