<?php

namespace GraphClass\Resolver;

use Exception;

final class ArrayFieldResolver implements FieldResolver{
    public function __construct(
        public string $property,
        public FieldResolver $itemResolver
    ) {
    }

    public static function __set_state(array $an_array): self {
        return new self(
            $an_array["property"],
            $an_array["type"]
        );
    }

    public function resolve($data): array {
        $newData = [];
        foreach ($data as $key => $item) {
            $newData[$key] = $this->itemResolver->resolve($item);
        }

        return $newData;
    }
}