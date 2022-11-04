<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Connector\JsonDBConnector;
use GraphClass\Type\Attribute\Field;
use GraphClass\Type\Attribute\Group;
use GraphClass\Type\Attribute\Id;
use GraphClass\Type\PersistedType;

#[Group('comment', JsonDBConnector::class)]
class Comment extends PersistedType {
	#[Field] public string $username;
	#[Field] public string $text;
	#[Field] public Post $post;

	public function __construct(
		#[Id] public int $id
	){
	}
}
