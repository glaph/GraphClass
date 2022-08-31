<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\Author;
use App\GraphQL\Type\Post;
use GraphClass\Input\Args;
use GraphClass\Type\QueryType;

class Query extends QueryType {
    private readonly string $root;
    public function __construct() {
        $this->root = dirname(__DIR__, 2);
    }

    public function lastPost(): ?Post {
        $data = $this->readJsonFile('post');
        $item = array_pop($data);
        return isset($item['id']) ? Post::create($item['id']) : null;
    }

    public function post(Args $args): ?Post {
        if (!isset($args->id)) {
            return null;
        }
        return Post::create($args->id);
    }

    public function allAuthors(): array {
        $data = $this->readJsonFile('author');
        return array_map(fn ($v) => Author::create($v['id']), $data);
    }

    private function readJsonFile(string $name): ?array {
        $jsonPath = "$this->root/db/$name.json";
        $json = file_get_contents($jsonPath);
        if ($json === false) {
            return null;
        }

        return (array) json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }
}
