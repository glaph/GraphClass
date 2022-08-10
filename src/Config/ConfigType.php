<?php

namespace GraphClass\Config;

use GraphClass\Config\Exception\FieldException;
use GraphClass\Config\Trait\ConfigFieldTrait;
use GraphClass\Config\Trait\ConfigVirtualsTrait;
use GraphClass\Config\Trait\SecureAssignationTrait;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Type;
use ReflectionClass;
use ReflectionException;

final class ConfigType extends Cache {
    use SecureAssignationTrait;
    use ConfigFieldTrait;
    use ConfigVirtualsTrait;

    /** @var class-string<Type> */
    public readonly string $class;
    public readonly ?Group $group;

    public static function __set_state(array $an_array): self {
        $obj = new self();
        $obj->secureAssignation($an_array, "class");
        $obj->secureAssignation($an_array, "group");
        $obj->secureAssignation($an_array, "fields");
        $obj->secureAssignation($an_array, "virtuals");

        return $obj;
    }

    /**
     * @throws ReflectionException
     * @throws FieldException
     */
    public static function create(ReflectionClass $class = null): self {
        $obj = new self();
        $attrs = $class->getAttributes(Group::class);
        $obj->class = $class->name;
        $obj->group = isset($attrs[0]) ? $attrs[0]->newInstance() : null;
        $obj->setFields($class);
        $obj->setVirtualsAndMethods($class);

        return $obj;
    }
}
