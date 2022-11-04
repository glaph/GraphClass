<?php

declare(strict_types=1);

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
		$author0 = new Author(
			id: 0,
			name: "Pavo",
			surname: "Vilar"
		);
		$author1 = new Author(
			id: 1,
			name: "David",
			surname: "Lopez"
		);
		$author2 = new Author(
			id: 1,
			name: "Don",
			surname: "Nacho"
		);

		$post0 = new Post(
			id: 0,
			title: "Introduction",
			author: $author0,
			body: "Some text"
		);
		$post1 = new Post(
			id: 1,
			title: "Hello world",
			author: $author1,
			body: "Hello world"
		);
		$post2 = new Post(
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
