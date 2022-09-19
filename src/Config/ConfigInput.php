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

final class ConfigInput extends Cache {
	use SecureAssignationTrait;
	use ConfigFieldTrait;
	use ConfigVirtualsTrait;

	public readonly string $class;
	public readonly string $mutator;

	public static function __set_state(array $an_array): self {
		$obj = new self();
		$obj->secureAssignation($an_array, "class");
		$obj->secureAssignation($an_array, "methods");
		$obj->secureAssignation($an_array, "mutator");
		$obj->secureAssignation($an_array, "fields");
		$obj->secureAssignation($an_array, "virtuals");

		return $obj;
	}

	/**
	 * @throws ReflectionException
	 * @throws FieldException
	 */
	public static function create(ReflectionClass $class): self {
		$obj = new self();
		$obj->class = $class->name;
		$attrs = $class->getAttributes(Mutator::class);
		if (isset($attrs[0])) {
			$typeClass = $attrs[0]->newInstance()->typeClass;
			$obj->mutator = (new ReflectionClass($typeClass))->getShortName();
		}
		$obj->setFields($class);
		$obj->setVirtualsAndMethods($class);

		return $obj;
	}
}
