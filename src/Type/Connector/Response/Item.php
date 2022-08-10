<?php

namespace GraphClass\Type\Connector\Response;

final class Item {
    public function __construct(
        public string $hash,
        public array $values
    ) {
    }
}
