<?php

namespace GraphClass\Resolver;

use Closure;
use GraphClass\Type\MutationType;
use GraphClass\Type\QueryType;
use GraphClass\Type\SubscriptionType;
use GraphClass\Type\Type;
use Exception;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;

final class TypeResolver {
    private function __construct(
        private readonly Struct $struct
    ) {
    }

    public static function getRootResolver(Struct $struct): Closure {
        return (new self($struct))->resolveRootField(...);
    }

    public static function getTypeResolver(Struct $struct): Closure {
        return (new self($struct))->resolveField(...);
    }

    /**
     * @param QueryType[] $value
     * @throws Exception
     */
    private function resolveRootField(array $value, $args, $context, ResolveInfo $info) {
        $type = $value[$info->parentType->name];
        $options = new ResolverOptions($this->struct, $args, $info);
        if ($type instanceof MutationType) {
            $type->mutate($options);
        }
        if ($type instanceof SubscriptionType) {
            $type->subscribe($options);
        }

        return $type->retrieve($options);
    }

    /**
     * @throws Exception
     */
    private function resolveField(Type $type, $args, $context, ResolveInfo $info) {
        if (!$this->struct->instanceOf($type)) throw new Exception("Invalid type");
        if (!$this->struct->hasFieldResolver($info->fieldName)) throw new Exception("Method $info->fieldName in class {$this->struct->class} must exist");

        return $type->retrieve(new ResolverOptions($this->struct, $args, $info));
    }
}
