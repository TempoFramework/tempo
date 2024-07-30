<?php

namespace Tempo;

#[\Attribute]
class DBConfig
{
    public string $tableName;
    public $entityClass;

    public function __construct(string $tableName, $entityClass)
    {
        $this->tableName = $tableName;
        $this->entityClass = $entityClass;
    }
}
