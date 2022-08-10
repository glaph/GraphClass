<?php

namespace GraphClass\Input;

use GraphClass\Config\ConfigInput;
use GraphClass\Config\ConfigType;
use GraphClass\Input\BaseInput;
use GraphClass\Resolver\ClassFieldResolver;
use GraphClass\Resolver\FieldResolver;
use GraphClass\Resolver\Resolvable;
use GraphQL\Type\Definition\FieldArgument;
use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use JetBrains\PhpStorm\Internal\TentativeType;

final class ArgsBuilder {

    private array $args = [];
    /** @var FieldArgument[] */
    private array $defs = [];
    private array $mutators = [];
    private Args $parsed;

    public function setArgs(array $args): self {
        $this->args = $args;
        return $this;
    }

    /**
     * @param FieldArgument[] $defs
     */
    public function setDefs(array $defs): self {
        $this->defs = $defs;
        return $this;
    }

    /**
     * @param class-string<Input> $class
     */
    public function hasMutator(string $class): bool {
        return isset($this->mutators[$class]);
    }


    /**
     * @param class-string<Input> $class
     */
    public function getMutator(string $class): ConfigType {
        return $this->mutators[$class];
    }

    public function getParsed(): Args {
        return $this->parsed;
    }

    public function build(): self {
        $this->parsed = new Args();
        foreach ($this->defs as $def) {
            $name = $def->name;
            if (!array_key_exists($name, $this->args)) continue;

            $type = $def->getType();
            $this->parsed->$name = self::resolve($type, $this->args[$name], $this->getResolver($type, $name));
        }

        return $this;
    }

    private function getResolver(Type $type, string $name): ?FieldResolver {
        if ($type instanceof InputObjectType) {
            /** @var ConfigInput $configInput */
            $configInput = $type->config["configInput"];
            $class = $configInput->class;
            return new ClassFieldResolver($name, $class);
        }

        return null;
    }

    private function resolve(Type $type, mixed $args, ?FieldResolver $resolver): mixed {
        $ret = $resolver ? $resolver->resolve($args) : $args;
        if ($ret instanceof Resolvable && $type instanceof InputObjectType) {
            /** @var ConfigInput $configInput */
            $configInput = $type->config["configInput"];
            foreach ($args as $propertyName => $value) {
                $ret->$propertyName = self::resolve($type->getField($propertyName)->getType(), $value, $configInput->fields[$propertyName]);
            }

            if ($type->config["mutatorConfigType"]) {
                $this->mutators[$configInput->class] = $type->config["mutatorConfigType"];
            }
        }

        return $ret;
    }
}
