<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Resolver\ResolverOptions;
use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ClassIteratorTrait;

abstract class FieldType implements Type {
    use ClassArrayShapeTrait;
    use ClassIteratorTrait;

    public function retrieve(ResolverOptions $options): mixed {
        $field = $options->getField();
        if ($method = $field->get?->method) {
            return $this->$method($options->args->getParsed());
        }
        if ($property = $field->field?->property) {
            return $this->$property ?? null;
        }

        throw new \Exception("Any implementation for {$field->name}");
    }

    public function persist(ResolverOptions $options): mixed {
        return $this->serialize();
    }

    public function getHash(): string {
        return "useless";
    }
}
