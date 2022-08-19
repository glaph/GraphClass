<?php

namespace App\GraphQL\Type;

use App\Connector\JsonDBConnector;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\PersistedType;

#[Group('post', JsonDBConnector::class, ["id"])]
class Post extends PersistedType {
    #[Field] public int $id;
    #[Field] public string $title;
    #[Field] public ?string $body;
    #[Field] public Author $author;
}
