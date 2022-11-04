<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

use GraphClass\Config\ConfigNode;

interface Resolvable {
	public function serialize(): array;
	public static function getConfig(): ConfigNode;
}
