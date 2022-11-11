<?php

declare(strict_types=1);

namespace GraphClass\Input;

use GraphClass\Config\ConfigType;
use GraphClass\Utils\ClassArrayShapeTrait;
use GraphClass\Utils\ResolvableTrait;

abstract class BaseInput implements Input {
	use ClassArrayShapeTrait;
	use ResolvableTrait;

	public function serialize(): array {
		return [];
	}

	public static function getMutatorConfig(): ConfigType {
		return (static::$_explorer)(static::getConfig()?->input->mutator)?->type;
	}
}
