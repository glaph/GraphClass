<?php

declare(strict_types=1);

namespace GraphClass\Type\Connector\Request;

use GraphClass\Resolver\FieldInfo;

final class Builder {
	public array $fields;

	public function __construct(
		public array $ids
	) {
	}

	public function addField(FieldInfo $info, $value = null): void {
		if ($info->get) {
			foreach ($info->get->fields as $field) {
				$this->fields[$field->property] = $value ?? null;
			}
		} elseif ($info->field) {
			$this->fields[$info->field->property] = $value ?? null;
		}
	}
}
