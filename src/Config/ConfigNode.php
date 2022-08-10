<?php

namespace GraphClass\Config;

use Exception;
use GraphClass\Config\Exception\NodeException;
use GraphClass\Config\Trait\SecureAssignationTrait;
use GraphClass\Input\Input;
use GraphClass\Type\QueryType;
use GraphClass\Type\Type;
use ReflectionClass;
use ReflectionException;

final class ConfigNode extends Cache {
    use SecureAssignationTrait;

    public readonly string $name;
    public readonly ?ConfigType $root;
    public readonly ?ConfigType $type;
    public readonly ?ConfigInput $input;

    /**
     * @throws NodeException
     */
    public function add(ReflectionClass $node): void {
        try{
            if ($node->isSubclassOf(QueryType::class)) $this->root = ConfigType::create($node);
            if ($node->implementsInterface(Type::class)) $this->type = ConfigType::create($node);
            if ($node->implementsInterface(Input::class)) $this->input = ConfigInput::create($node);
        } catch (Exception $e) {
            throw new NodeException("The node $node->name can't be added", previous: $e);
        }
    }

    public static function __set_state(array $an_array): self {
        $obj = new self();
        $obj->secureAssignation($an_array, "name");
        $obj->secureAssignation($an_array, "root");
        $obj->secureAssignation($an_array, "type");
        $obj->secureAssignation($an_array, "input");

        return $obj;
    }

    /**
     * @throws NodeException
     */
    public static function create(ReflectionClass $node): self {
        $obj = new self();
        $obj->name = $node->getShortName();
        $obj->add($node);

        return $obj;
    }

    /**
     * @throws ReflectionException
     */
    public static function loadIgnoredMethods(): void {
        ConfigType::loadIgnoredMethodsByClass(Type::class);
        ConfigInput::loadIgnoredMethodsByClass(Input::class);
    }
}
