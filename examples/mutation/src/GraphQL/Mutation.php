<?php

namespace App\GraphQL;

use App\GraphQL\Type\Author;
use App\GraphQL\Type\Comment;
use App\GraphQL\Type\Post;
use GraphClass\Input\Args;
use GraphClass\Type\MutationType;

class Mutation extends MutationType {
    public function post(Args $args): Post {
        return Post::create($args->input->id);
    }

    public function author(Args $args): Author {
        return Author::create($args->input->id);
    }

    public function comment(Args $args): Comment {
        return Comment::create($args->input->id);
    }
}
