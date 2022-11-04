<?php

declare(strict_types=1);

namespace GraphClass\Input;

use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ClassIteratorTrait;
use GraphClass\Utils\ResolvableTrait;

abstract class BaseInput implements Input {
	use ClassArrayShapeTrait;
	use ClassIteratorTrait;
	use ResolvableTrait;

	public function serialize(): array {
		return [];
	}
}
