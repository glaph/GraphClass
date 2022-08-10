<?php

namespace GraphClass\Resolver;


final class VirtualResolver {
    /**
     * @param FieldResolver[] $fields
     */
    public function __construct(
        public array  $fields,
        public string $method
    ) {
    }

    public static function __set_state(array $an_array): self{
        return new self(
            $an_array["fields"],
            $an_array["method"]
        );
    }
}
