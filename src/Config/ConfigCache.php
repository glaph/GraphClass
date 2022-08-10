<?php

namespace GraphClass\Config;

use GraphClass\Config\Exception\ConfigException;
use GraphClass\Config\Exception\IOException;
use GraphClass\SchemaOptions;

final class ConfigCache {
    public readonly Config $config;

    /**
     * @throws ConfigException
     */
    public function __construct(
        private readonly SchemaOptions $options
    ) {
        if ($this->options->cacheDirPath) {
            $cacheFilename = $this->getFilename();
            if (is_readable($cacheFilename)) {
                $this->config = Config::__set_state(require $cacheFilename);
                return;
            }
        }

        $this->config = Config::createBySchemaOptions($this->options);
    }

    /**
     * @throws IOException
     */
    public function persist(): void {
        ConfigFileManager::saveCache($this->getFilename(), $this->config->serialize());
    }

    private function getFilename(): string {
        if ($this->options->fileName) return $this->options->fileName;
        $schemaFilename = basename($this->options->schemaFilePath, ".gql");
        return "{$this->options->cacheDirPath}/{$schemaFilename}_cache.php";
    }
}
