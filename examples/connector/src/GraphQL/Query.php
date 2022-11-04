<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\Author;
use App\GraphQL\Type\Post;
use GraphClass\Type\QueryType;

class Query extends QueryType {
	public function lastPost(): Post {
		return new Post(2);
	}

	public function allAuthors(): array {
		return [
			new Author(0),
			new Author(1),
			new Author(2),
		];
	}
}
