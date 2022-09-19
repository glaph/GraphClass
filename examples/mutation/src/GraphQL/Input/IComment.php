<?php

declare(strict_types=1);

namespace App\GraphQL\Input;

use App\GraphQL\Type;
use GraphClass\Input\BaseInput;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Mutator;

#[Mutator(Type\Comment::class)]
class IComment extends BaseInput {
	#[Field] public int $id;
	#[Field] public string $username;
	#[Field] public string $text;
	#[Field] public IPost $post;
}
