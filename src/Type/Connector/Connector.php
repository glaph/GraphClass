<?php

namespace GraphClass\Type\Connector;

use GraphClass\Type\Connector\Response\Keys;

interface Connector {
    public function retrieve(Request $request, Response $response): void;
    public function submit(Request $request, Response $response): ?Keys;
}
