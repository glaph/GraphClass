<?php

namespace App;

use App\Type\Author;
use App\Type\Post;
use GraphClass\Type\QueryType;

class Query extends QueryType {
    public function lastPost(): Post {
        $info = $this->createSomeInfo();
        return $info["posts"][2];
    }

    /**
     * @return Author[]
     */
    public function allAuthors(): array {
        $info = $this->createSomeInfo();
        return $info["authors"];
    }

    private function createSomeInfo(): array {
        $author0 = Author::create(
            id: 0,
            name: "Pavo",
            surname: "Vilar"
        );
        $author1 = Author::create(
            id: 1,
            name: "David",
            surname: "Lopez"
        );
        $author2 = Author::create(
            id: 1,
            name: "Don",
            surname: "Nacho"
        );

        $post0 = Post::create(
            id: 0,
            title: "Introduction",
            body: "Some text",
            author: $author0
        );
        $post1 = Post::create(
            id: 1,
            title: "Hello world",
            body: "Hello world",
            author: $author1
        );
        $post2 = Post::create(
            id: 2,
            title: "Advanced",
            author: $author1
        );

        $author0->posts = [$post0];
        $author1->posts = [$post1, $post2];

        return [
            "authors" => [$author0, $author1, $author2],
            "posts" => [$post0, $post1, $post2]
        ];
    }
}
