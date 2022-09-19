<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

interface Connector {
	public function retrieve(Request $request, Response $response): void;
	public function submit(Request $request, Response $response): int|string|null;
}
