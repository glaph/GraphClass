<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\Author;
use App\GraphQL\Type\Post;
use GraphClass\Type\QueryType;

class Query extends QueryType {
	public function lastPost(): Post {
		return Post::create(2);
	}

	public function allAuthors(): array {
		return [
			Author::create(0),
			Author::create(1),
			Author::create(2)
		];
	}
}
