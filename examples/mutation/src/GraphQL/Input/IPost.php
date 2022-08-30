<?php

namespace App\GraphQL\Input;

use App\GraphQL\Type;
use GraphClass\Input\BaseInput;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Mutator;

#[Mutator(Type\Post::class)]
class IPost extends BaseInput {
    #[Field] public int $id;
    #[Field] public string $title;
    #[Field] public ?string $body;
    #[Field] public IAuthor $author;
}
