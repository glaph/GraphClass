<?php

namespace GraphClass\Config\Trait;

trait SecureAssignationTrait
{
    private function secureAssignation(array $an_array, string $name): void {
        if (array_key_exists($name, $an_array)) {
            $this->$name = $an_array[$name];
        }
    }
}
