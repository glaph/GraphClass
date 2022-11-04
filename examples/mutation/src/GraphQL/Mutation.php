<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\Author;
use App\GraphQL\Type\Comment;
use App\GraphQL\Type\Post;
use GraphClass\Input\Args;
use GraphClass\Type\MutationType;

class Mutation extends MutationType {
	public function post(Args $args): Post {
		return new Post($args->input->id);
	}

	public function author(Args $args): Author {
		return new Author($args->input->id);
	}

	public function comment(Args $args): Comment {
		return new Comment($args->input->id);
	}
}
