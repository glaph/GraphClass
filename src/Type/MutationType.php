<?php

namespace GraphClass\Type;

use GraphClass\Config\ConfigType;
use GraphClass\Input\ArgsBuilder;
use GraphClass\Input\Input;
use GraphClass\Resolver\ResolverOptions;
use GraphClass\Resolver\Struct;
use GraphClass\Type\Connector\Response\Keys;

abstract class MutationType extends QueryType {
    public function mutate(ResolverOptions $options): void {
        $args = $options->args->getParsed();
        if ($method = $options->field->set?->method) {
            $this->$method($args);
            return;
        }

        foreach ($args as $input) {
            if($options->args->hasMutator($input::class)) {
                $this->persist($input, $this->createType($input, $options->args), $options->args);
            }
        }

        $args->clearIterator();
    }

    private function createType(Input $input, ArgsBuilder $args): Type {
        $configType = $args->getMutator($input::class);
        $type = $this->instanceType($input, $configType);
        $struct = Struct::create($configType);

        foreach ($input as $name => $value) {
            $field = $struct->getField($name);
            if ($method = $field->set?->method) {
                $tmpType = new ($struct->class);
                $tmpType->$method($value);
                $this->extractProperties($type, $tmpType);
                continue;
            }

            $type->$name = $this->getValue($value, $args);
        }

        return $type;
    }

    private function instanceType(Input $input, ConfigType $configType): Type {
        $keys = [];
        if ($configType?->group?->keys) {
            foreach ($configType->group->keys as $key) {
                if (!isset($input->$key)) {
                    $keys = [];
                    break;
                }

                $keys[$key] = $input->$key;
            }
        }

        return $keys ? $configType->class::create(...$keys) : new ($configType->class);
    }

    private function getValue(mixed $value, ArgsBuilder $args): mixed {
        if ($value instanceof Input) {
            return $this->createType($value, $args);
        }

        return $value;
    }

    private function persist(Input $input, Type $type, ArgsBuilder $args): void {
        $keys = $type->persist($args->getMutator($input::class)->group);
        if ($keys instanceof Keys) {
            foreach ($keys as $name => $value) {
                $input->$name = $value;
            }
        }
    }

    private function extractProperties(Type $type, Type $tempType): void {
        $vars = get_object_vars($tempType);
        foreach ($vars as $name => $value) {
            $type->$name = $value;
        }
    }
}
