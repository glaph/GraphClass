<?php

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
            $obj->nodes = self::loadTypeNodes($options->root);

            return $obj;
        } catch (Exception $e) {
            throw new ConfigException("The configuration couldn't be created with the current options", previous: $e);
        }
    }

    /**
     * @return ConfigNode[]
     * @throws ConfigException
     */
    private static function loadTypeNodes(QueryType $root): array {
        $query = new ReflectionClass($root);
        $nodes = [];

        try {
            foreach (ConfigFileManager::loadTypes(dirname($query->getFileName()), $query->getNamespaceName()) as $nodeClass) {
                $nodeRef = new ReflectionClass($nodeClass);
                $nodeName = $nodeRef->getShortName();

                if (isset($nodes[$nodeName])) {
                    $nodes[$nodeName]->add($nodeRef);
                } else {
                    $nodes[$nodeName] = ConfigNode::create($nodeRef);
                }
            }
        } catch (Exception $e) {
            throw new ConfigException("The config nodes couldn't be created", previous: $e);
        }

        return $nodes;
    }
}
