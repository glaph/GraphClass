<?php

declare(strict_types=1);

namespace GraphClass\Utils;

use GraphClass\Config\ConfigNode;

trait ResolvableTrait {
	protected static \Closure $_explorer;

	public static function getConfig(): ConfigNode {
		return (static::$_explorer)(get_called_class());
	}
}
