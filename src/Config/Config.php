<?php

declare(strict_types=1);

namespace GraphClass\Config;

use Exception;
use GraphClass\Config\Exception\ConfigException;
use GraphClass\Config\Trait\SecureAssignationTrait;
use GraphClass\SchemaOptions;
use GraphClass\Type\QueryType;
use GraphQL\Language\AST\Node;
use GraphQL\Utils\AST;
use ReflectionClass;

final class Config extends Cache {
	use SecureAssignationTrait;

	public readonly Node $document;
	/** @var ConfigNode[] */
	public readonly array $nodes;

	public function serialize(): string {
		return var_export([
			"document" =>  AST::toArray($this->document),
			"nodes" => $this->nodes
		], true);
	}

	public static function __set_state(array $an_array): self {
		$obj = new self();
		$obj->document = AST::fromArray($an_array["document"]);
		$obj->secureAssignation($an_array, "nodes");

		return $obj;
	}

	/**
	 * @throws ConfigException
	 */
	public static function createBySchemaOptions(SchemaOptions $options): self {
		try {
			ConfigNode::loadIgnoredMethods();

			$obj = new self();
			$obj->document = ConfigFileManager::loadDocumentNode($options->schemaFilePath);
			$nodes = [];
			self::loadTypeNodes($options->root, $nodes);
			$obj->nodes = $nodes;

			return $obj;
		} catch (Exception $e) {
			throw new ConfigException("The configuration couldn't be created with the current options", previous: $e);
		}
	}

	/**
	 * @param ConfigNode[] $nodes
	 * @throws ConfigException
	 */
	private static function loadTypeNodes(QueryType $root, array &$nodes): void {
		$query = new ReflectionClass($root);

		try {
			foreach (ConfigFileManager::loadTypes(dirname($query->getFileName()), $query->getNamespaceName()) as $nodeClass) {
				$nodeRef = new ReflectionClass($nodeClass);
				$nodeName = $nodeRef->getShortName();
				self::setExplorerFunc($nodeRef, $nodes);

				if (isset($nodes[$nodeName])) {
					$nodes[$nodeName]->add($nodeRef);
				} else {
					$nodes[$nodeName] = ConfigNode::create($nodeRef);
				}
			}
		} catch (Exception $e) {
			throw new ConfigException("The config nodes couldn't be created", previous: $e);
		}
	}

	/**
	 * @param ConfigNode[] $nodes
	 */
	private static function setExplorerFunc(ReflectionClass $nodeRef, array &$nodes): void {
		$nodeRef->setStaticPropertyValue("_explorer", function (string $class) use (&$nodes) {
			$explode = explode("\\", $class);
			$className = array_pop($explode);
			if (!isset($nodes[$className])) {
				throw new Exception("Class $class can't be found in preloaded types");
			}

			return $nodes[$className];
		});
	}
}
