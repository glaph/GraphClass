<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

final class Request {
    public function __construct(
        public array $fields,
        public string $group,
        public Request\Keys $keys
    ) {
    }
}
