<?php

namespace GraphClass\Resolver;

use GraphClass\Config\ConfigType;
use GraphClass\Input\ArgsBuilder;
use GraphClass\Type\Attribute\VirtualType;
use GraphQL\Type\Definition\ResolveInfo;

final class ResolverOptions {
    private FieldInfo $field;

    public function __construct(
        public ConfigType $type,
        public ArgsBuilder $args,
        public ResolveInfo $info,
    ) {
    }

    public function getField(string $field = ""): FieldInfo {
        if (!$field) {
            if (isset($this->field)) return $this->field;
            $field = $this->info->fieldName;
        }
        if (!$this->hasFieldResolver($field)) throw new \Exception("Method or property $field in class {$this->type->class} must exist");

        return $this->field = new FieldInfo(
            name: $field,
            field: $this->type->fields[$field] ?? null,
            get: $this->type->virtuals[$field][VirtualType::Get->name] ?? null,
            set: $this->type->virtuals[$field][VirtualType::Set->name] ?? null
        );
    }

    private function hasFieldResolver(string $field): bool {
        return isset($this->type->virtuals[$field]) || isset($this->type->fields[$field]);
    }
}
