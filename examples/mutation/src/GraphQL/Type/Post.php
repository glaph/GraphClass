<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Connector\JsonDBConnector;
use GraphClass\Type\Attribute\ArrayField;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Attribute\Id;
use GraphClass\Type\PersistedType;

#[Group('post', JsonDBConnector::class)]
class Post extends PersistedType {
	#[Field] public string $title;
	#[Field] public ?string $body;
	#[Field] public Author $author;
	#[ArrayField(Comment::class, 'comments')] public array $comment;

	public function __construct(
		#[Id] public int $id
	) {
	}
}
