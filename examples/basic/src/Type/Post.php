<?php

namespace App\Type;

use GraphClass\Type\Attribute\Field;
use GraphClass\Type\FieldType;

class Post extends FieldType {
    #[Field] public int $id;
    #[Field] public string $title;
    #[Field] public ?string $body;
    #[Field] public Author $author;

    public static function create(...$data): self
    {
        $obj = new self();
        $obj->id = $data["id"];
        $obj->title = $data["title"];
        $obj->body = $data["body"] ?? null;
        $obj->author = $data["author"];

        return $obj;
    }

    public function serialize(): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "body" => $this->body,
            "author" => $this->author
        ];
    }
}
