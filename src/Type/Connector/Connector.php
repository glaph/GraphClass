<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

use GraphClass\Type\Connector\Response\Key;

interface Connector {
	public function retrieve(Request $request, Response $response): void;
	public function submit(Request $request, Response $response): ?Key;
}
