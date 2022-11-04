<?php

declare(strict_types=1);

namespace App\Type;

use GraphClass\Type\Attribute\Field;
use GraphClass\Type\FieldType;

class Post extends FieldType {
	public function __construct(
		#[Field] public int $id,
		#[Field] public string $title,
		#[Field] public Author $author,
		#[Field] public ?string $body = null
	) {
	}

	public function serialize(): array {
		return [
			"id" => $this->id,
			"title" => $this->title,
			"body" => $this->body,
			"author" => $this->author
		];
	}
}
