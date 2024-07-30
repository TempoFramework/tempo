<?php

namespace Tempo;
use Tempo\DBConfig;

abstract class DbConfigConnection
{
    public function __construct()
    {
        $this->initializeDBConfigs();
    }

    private function initializeDBConfigs(): void
    {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(DBConfig::class);
            foreach ($attributes as $attribute) {
                /** @var DBConfig $instance */
                $instance = $attribute->newInstance();
                $property->setAccessible(true);
                $property->setValue($this, DBConnection::set($instance->tableName, $instance->entityClass));
            }
        }
    }
}