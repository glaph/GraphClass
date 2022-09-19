<?php

declare(strict_types=1);

namespace GraphClass\Resolver;

final class FieldInfo {
	public function __construct(
		public string           $name,
		public ?FieldResolver   $field = null,
		public ?VirtualResolver $get = null,
		public ?VirtualResolver $set = null
	) {
	}

	public function getFieldResolvers(): array {
		if ($this->get) {
			return $this->get->fields;
		}
		if ($this->field) {
			return [$this->field];
		}

		return [];
	}
}
