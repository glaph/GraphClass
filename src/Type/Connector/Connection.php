<?php

namespace GraphClass\Type\Connector;

use GraphClass\Type\Type;

final class Connection {
    /** @var self[] */
    private static array $instances = [];
    /** @var Request\Builder[][] */
    private array $builders = [];
    /** @var Response\Wrapper[] */
    private array $responses = [];

    /**
     * @param class-string<Connector> $class
     */
    public function __construct(
        private readonly string $class,
    ) {
    }

    public function getBuilder(string $groupName, Type $type): Request\Builder {
        $hash = $type->getHash();
        if (!isset($this->builders[$groupName][$hash])) {
            $this->builders[$groupName][$hash] = new Request\Builder();
        }

        return $this->builders[$groupName][$hash];
    }

    public function getResponse(string $groupName, Type $type): Response\Wrapper {
        $hash = $type->getHash();
        if (!isset($this->responses[$groupName][$hash])) {
            $this->responses[$groupName][$hash] = new Response\Wrapper($this, new ($this->class));
        }

        return $this->responses[$groupName][$hash];
    }

    public function hydrateResponseWrappers(): void {
        if (!isset(self::$instances[$this->class])) return;

        $map = [];
        foreach ($this->builders as $groupName => $builders) {
            foreach ($builders as $hash => $builder) {
                $request = null;
                $response = null;
                foreach ($map as $values) {
                    if ($values[0]->fields == $builder->fields) {
                        $request = $values[0];
                        $response = $values[1];
                        break;
                    }
                }

                if (!$request) {
                    $request = new Request($builder->fields, $groupName, $builder->keys);
                    $response = new Response();
                    $map[$hash] = [$request, $response];
                }

                $request->keys->addValues($builder->keyValues);
                $this->responses[$groupName][$hash]->request = $request;
                $this->responses[$groupName][$hash]->response = $response;
            }
        }

        unset(self::$instances[$this->class]);
        unset($this->builders);
        unset($this->responses);
    }

    /**
     * @param class-string<Connector> $class
     */
    public static function getInstance(string $class): self {
        if (!isset(self::$instances["$class"])) {
            self::$instances["$class"] = new self($class);
        }

        return self::$instances["$class"];
    }
}
