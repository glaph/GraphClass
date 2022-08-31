<?php

declare(strict_types=1);

namespace GraphClass\Utils;

trait ClassArrayShapeTrait {
    public function offsetExists(mixed $offset): bool {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->$offset ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->$offset);
    }

    public function count(): int {
        $privates = isset($this->_vars) ? 3 : 0;
        return count(get_object_vars($this)) - $privates;
    }
}
