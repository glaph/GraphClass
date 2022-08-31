<?php

declare(strict_types=1);

namespace GraphClass;

use GraphClass\Type\QueryType;

final class SchemaOptions {
    public function __construct(
        public string  $schemaFilePath,
        public QueryType $root,
        public ?string $cacheDirPath = null,
        public ?string $fileName = null
    ) {
    }
}
