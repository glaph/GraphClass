<?php

namespace GraphClass\Resolver;

use GraphClass\Input\ArgsBuilder;
use GraphClass\Type\Attribute\Group;
use GraphQL\Type\Definition\ResolveInfo;

final class ResolverOptions {
    public FieldInfo $field;
    public ArgsBuilder $args;
    public ?Group $group;

    public function __construct(
        Struct $struct,
        array $args,
        ResolveInfo $info,
    ) {
        $this->field = $struct->getField($info->fieldName);
        $this->args = (new ArgsBuilder)->setArgs($args)->setDefs($info->fieldDefinition->args)->build();
        $this->group = $struct->group;
    }
}
