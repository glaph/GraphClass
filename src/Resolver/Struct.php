<?php

namespace GraphClass\Resolver;

use GraphClass\Config\ConfigType;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Attribute\VirtualType;
use GraphClass\Type\Type;

final class Struct {
    /**
     * @param class-string<Type> $class
     * @param FieldResolver[] $fields
     * @param VirtualResolver[][] $virtuals
     */
    public function __construct(
        public string $class,
        public ?Group $group = null,
        public array  $fields = [],
        public array  $virtuals = []
    ) {
    }

    public function hasFieldResolver(string $field): bool {
        return isset($this->virtuals[$field]) || isset($this->fields[$field]);
    }

    public function instanceOf(Type $type): bool {
        return $type instanceof $this->class;
    }

    public function getField(string $field): FieldInfo {
        return new FieldInfo(
            name: $field,
            field: $this->fields[$field] ?? null,
            get: $this->virtuals[$field][VirtualType::Get->name] ?? null,
            set: $this->virtuals[$field][VirtualType::Set->name] ?? null
        );
    }

    public static function create(ConfigType $type): self {
        return new self(
            $type->class,
            $type->group,
            $type->fields,
            $type->virtuals
        );
    }
}
