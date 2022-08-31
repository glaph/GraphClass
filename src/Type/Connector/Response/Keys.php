<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Response;

final class Keys implements \IteratorAggregate {
    private array $keys = [];

    public function addKey(string $name, mixed $value): self {
        $this->keys[$name] = $value;
        return $this;
    }

    /**
     * @return \Iterator<string[]>
     */
    public function getIterator(): \Iterator {
        return new \ArrayIterator($this->keys);
    }
}
