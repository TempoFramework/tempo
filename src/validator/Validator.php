<?php

namespace Tempo;

use ReflectionClass;
use ReflectionProperty;

use Tempo\Attributes;

class Validator
{
    private $data;
    public $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function check($object)
    {
        $reflectionClass = new ReflectionClass($object);

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes();
            $property->setAccessible(true); // Hacer la propiedad accesible
            $propertyName = $property->getName();

            if (!array_key_exists($propertyName, $this->data)) {
                continue; // Si el campo no está en los datos, saltar
            }

            $value = $this->data[$propertyName];

            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();

                // Validar `Required`
                if ($instance instanceof Attributes\Required) {
                    if (empty($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `Email`
                if ($instance instanceof Attributes\Email) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `MinLength`
                if ($instance instanceof Attributes\MinLength) {
                    if (strlen($value) < $instance->length) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Añadir más validaciones según sea necesario

                // Validar `OnlyNumbers`
                if ($instance instanceof Attributes\OnlyNumbers) {
                    if (!is_numeric($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `OnlyLetters`
                if ($instance instanceof Attributes\OnlyLetters) {
                    if (!ctype_alpha($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `MaxLength`
                if ($instance instanceof Attributes\MaxLength) {
                    if (strlen($value) > $instance->length) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `MinValue`
                if ($instance instanceof Attributes\MinValue) {
                    if ($value < $instance->value) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `MaxValue`
                if ($instance instanceof Attributes\MaxValue) {
                    if ($value > $instance->value) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `Date`
                if ($instance instanceof Attributes\Date) {
                    if (!strtotime($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `DateTime`
                if ($instance instanceof Attributes\DateTime) {
                    if (!strtotime($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `Time`
                if ($instance instanceof Attributes\Time) {
                    if (!strtotime($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `Url`
                if ($instance instanceof Attributes\Url) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `Boolean`
                if ($instance instanceof Attributes\Boolean) {
                    if (!is_bool($value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `minWords`
                if ($instance instanceof Attributes\minWords) {
                    $wordCount = str_word_count($value);
                    if ($wordCount < $instance->words) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `maxWords`
                if ($instance instanceof Attributes\maxWords) {
                    $wordCount = str_word_count($value);
                    if ($wordCount > $instance->words) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }

                // Validar `regex`
                if ($instance instanceof Attributes\regex) {
                    if (!preg_match($instance->regex, $value)) {
                        $this->errors[$propertyName] = $instance->message;
                    }
                }
            }
        }

        return $this;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
