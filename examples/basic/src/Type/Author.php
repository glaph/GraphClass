<?php

declare(strict_types=1);

namespace App\Type;

use GraphClass\Type\Attribute\ArrayField;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\FieldType;

class Author extends FieldType {
    #[Field] public int $id;
    #[Field] public string $name;
    #[Field] public string $surname;
    #[ArrayField(Post::class)] public ?array $posts;

    public static function create(...$data): self {
        $obj = new self();
        $obj->id = $data["id"];
        $obj->name = $data["name"];
        $obj->surname = $data["surname"];
        $obj->posts = $data["posts"] ?? null;

        return $obj;
    }

    public function serialize(): array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "surname" => $this->surname,
            "posts" => $this->posts
        ];
    }
}
