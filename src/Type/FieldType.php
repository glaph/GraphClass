<?php

declare(strict_types=1);

namespace GraphClass\Type;

use GraphClass\Resolver\ResolverOptions;
use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ClassIteratorTrait;
use GraphClass\Utils\ResolvableTrait;

abstract class FieldType implements Type {
	use ClassArrayShapeTrait;
	use ClassIteratorTrait;
	use ResolvableTrait;

	public function retrieve(ResolverOptions $options): mixed {
		if ($method = $options->field->get?->method) {
			return $this->$method($options->args->getParsed());
		}
		if ($property = $options->field->field?->property) {
			return $this->$property ?? null;
		}

		throw new \Exception("Any implementation for {$options->field->name}");
	}

	public function persist(ResolverOptions $options): mixed {
		return $this->serialize();
	}

	public function getHash(): int {
		return spl_object_id($this);
	}
}
