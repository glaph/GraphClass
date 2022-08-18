<?php

namespace App;

use GraphClass\Input\Args;
use GraphClass\Type\QueryType;

class Query extends QueryType {
    public function helloWorld(): string {
        return "hello world";
    }

    public function hello(Args $args): string {
        $name = $args->name ?? '';
        return "hello $name";
    }
}
