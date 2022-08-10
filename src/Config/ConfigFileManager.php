<?php

namespace GraphClass\Config;

use GraphClass\Config\Exception\IOException;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\Node;
use GraphQL\Language\Parser;

final class ConfigFileManager {

    /**
     * @throws IOException
     */
    public static function saveCache(string $filename, string $content) {
        if (is_readable($filename)) return;

        $response = file_put_contents($filename, "<?php\nreturn $content;\n");
        if ($response === false) throw new IOException("Unable to create cache file $filename");
    }

    /**
     * @throws IOException
     * @throws SyntaxError
     */
    public static function loadDocumentNode(string $schemaFilePath): Node {
        if (!is_readable($schemaFilePath)) throw new IOException("Unable to access schema file $schemaFilePath");

        return Parser::parse(file_get_contents($schemaFilePath));
    }

    public static function loadTypes(string $dir, string $baseNamespace): \Generator {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if (in_array($file, [".", ".."])) continue;
            $type = basename($file, ".php");
            $class = "$baseNamespace\\$type";
            if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) yield from self::loadTypes($dir.DIRECTORY_SEPARATOR.$file, $class);
            else yield $class;
        }
    }
}
