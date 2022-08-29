<?php

namespace GraphClass\Resolver;

use Closure;
use GraphClass\Config\ConfigType;
use GraphClass\Input\ArgsBuilder;
use GraphClass\Type\MutationType;
use GraphClass\Type\QueryType;
use GraphClass\Type\SubscriptionType;
use GraphClass\Type\Type;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;

final class TypeResolver {
    private function __construct(
        private readonly ConfigType $type
    ) {
    }

    public static function getRootResolver(ConfigType $type): Closure {
        return (new self($type))->resolveRootField(...);
    }

    public static function getTypeResolver(ConfigType $type): Closure {
        return (new self($type))->resolveField(...);
    }

    /**
     * @param QueryType[] $value
     * @throws Exception
     */
    private function resolveRootField(array $value, $args, $context, ResolveInfo $info) {
        $type = $value[$info->parentType->name];
        $argsBuilder = (new ArgsBuilder)->setArgs($args)->setDefs($info->fieldDefinition->args)->build();
        $options = new ResolverOptions($this->type, $argsBuilder, $info);
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
        if (!($type instanceof $this->type->class)) throw new Exception("Invalid type");
        $argsBuilder = (new ArgsBuilder)->setArgs($args)->setDefs($info->fieldDefinition->args)->build();

        return $type->retrieve(new ResolverOptions($this->type, $argsBuilder, $info));
    }
}
