<?php

declare(strict_types=1);

namespace GraphClass\Config;

use GraphClass\Config\Exception\FieldException;
use GraphClass\Config\Trait\ConfigFieldTrait;
use GraphClass\Config\Trait\ConfigVirtualsTrait;
use GraphClass\Config\Trait\SecureAssignationTrait;
use GraphClass\Type\Attribute\Mutator;
use ReflectionClass;
use ReflectionException;

final class ConfigScalar extends Cache {
	use SecureAssignationTrait;

	public readonly string $class;

	public static function __set_state(array $an_array): self {
		$obj = new self();
		$obj->secureAssignation($an_array, "class");

		return $obj;
	}

	public static function create(ReflectionClass $class): self {
		$obj = new self();
		$obj->class = $class->name;

		return $obj;
	}
}
