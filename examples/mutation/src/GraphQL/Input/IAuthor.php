<?php

declare(strict_types=1);

namespace App\GraphQL\Input;

use App\GraphQL\Type;
use GraphClass\Input\BaseInput;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Mutator;

#[Mutator(Type\Author::class)]
class IAuthor extends BaseInput {
	#[Field] public int $id;
	#[Field] public string $name;
	#[Field] public string $surname;
}
