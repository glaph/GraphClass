<?php

declare(strict_types=1);

namespace GraphClass\Input;

use GraphClass\Config\ConfigType;
use GraphClass\Resolver\Resolvable;

interface Input extends Resolvable, \Countable, \ArrayAccess {
	public static function getMutatorConfig(): ?ConfigType;
}
