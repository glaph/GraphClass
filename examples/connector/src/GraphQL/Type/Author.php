<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Connector\JsonDBConnector;
use GraphClass\Type\Attribute\ArrayField;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Attribute\Id;
use GraphClass\Type\PersistedType;

#[Group('author', JsonDBConnector::class)]
class Author extends PersistedType {
	#[Field] public string $name;
	#[Field] public string $surname;
	#[ArrayField(Post::class)] public ?array $posts;

	public function __construct(
		#[Id] public int $id
	) {
	}
}
