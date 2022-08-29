<?php

namespace GraphClass\Utils;

trait ClassIteratorTrait {
    private mixed $_currentKey;
    private mixed $_currentValue;
    private array $_vars;

    public function current(): mixed {
        return $this->_currentValue;
    }

    public function next(): void {
        $this->_currentKey = array_key_last($this->_vars);
        $this->_currentValue = array_pop($this->_vars);
    }

    public function key(): mixed {
        return $this->_currentKey;
    }

    public function valid(): bool {
        return $this->_currentKey !== null;
    }

    public function rewind(): void {
        $this->clearIterator();
        $this->_vars = get_object_vars($this);
        $this->next();
    }

    public function clearIterator(): void {
        unset($this->_vars, $this->_currentKey, $this->_currentValue);
    }
}
