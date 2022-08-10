<?php

namespace GraphClass\Type\Connector;

use GraphClass\Type\Connector\Request\Keys;

final class Request {
    public Request\Keys $keys;

    public function __construct(
        public array $fields,
        public string $group,
        array $keys
    ) {
        $this->keys = new Keys($keys);
    }
}
