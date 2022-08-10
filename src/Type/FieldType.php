<?php

namespace GraphClass\Type;

use GraphClass\Resolver\ResolverOptions;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Connector\Response\Keys;
use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ClassIteratorTrait;

abstract class FieldType implements Type {
    use ClassArrayShapeTrait;
    use ClassIteratorTrait;

    public function retrieve(ResolverOptions $options): mixed {
        if ($method = $options->field->get?->method) {
            return $this->$method($options->args->getParsed());
        }
        if ($property = $options->field->field?->property) {
            return $this->$property;
        }

        throw new \Exception("Any implementation for {$options->field->name}");
    }

    public function persist(Group $group): mixed
    {
        return $this->serialize();
    }

    public function getHash(): string {
        return "useless";
    }
}
