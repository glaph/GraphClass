<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use Closure;
use GraphClass\Config\ConfigType;
use GraphClass\Input\ArgsBuilder;
use GraphClass\Type\Attribute\VirtualType;
use GraphClass\Type\MutationType;
use GraphClass\Type\QueryType;
use GraphClass\Type\SubscriptionType;
use GraphClass\Type\Type;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;

final class TypeResolver {
	private function __construct(
	) {
	}

	public static function getRootResolver(): Closure {
		return (new self())->resolveRootField(...);
	}

	public static function getTypeResolver(): Closure {
		return (new self())->resolveField(...);
	}

	/**
	 * @param QueryType[] $value
	 * @throws Exception
	 */
	private function resolveRootField(array $value, $args, $context, ResolveInfo $info) {
		$type = $value[$info->parentType->name];
		$argsBuilder = (new ArgsBuilder())->setArgs($args)->setDefs($info->fieldDefinition->args)->build();
		$options = new ResolverOptions(self::getField($type::getConfig()->root, $info->fieldName), $argsBuilder);
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
		$argsBuilder = (new ArgsBuilder())->setArgs($args)->setDefs($info->fieldDefinition->args)->build();

		return $type->retrieve(new ResolverOptions(self::getField($type::getConfig()->type, $info->fieldName), $argsBuilder));
	}

	public static function getField(ConfigType $type, string $field): FieldInfo {
		if (!self::hasFieldResolver($type, $field)) {
			throw new \Exception("Method or property $field in class {$type->class} must exist");
		}

		return new FieldInfo(
			name: $field,
			field: $type->fields[$field] ?? null,
			get: $type->virtuals[$field][VirtualType::Get->name] ?? null,
			set: $type->virtuals[$field][VirtualType::Set->name] ?? null
		);
	}

	private static function hasFieldResolver(ConfigType $type, string $field): bool {
		return isset($type->virtuals[$field]) || isset($type->fields[$field]);
	}
}
