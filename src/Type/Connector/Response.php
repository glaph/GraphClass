<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector;

final class Response {
    /** @var Response\Item[] */
    public array $items = [];

    public function addItem(Response\Item $item): void {
        $this->items[$item->hash] = $item;
    }
}
