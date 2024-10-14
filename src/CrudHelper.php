<?php

namespace BalajiDharma\LaravelCrud;

class CrudHelper
{
    /**
     * Map database column types to HTML input types.
     *
     * @param  string $columnType
     * @return string
     */
    public function getInputType($columnType)
    {
        $typeMap = [
            'bigint' => 'number',
            'binary' => 'text',
            'bit' => 'checkbox',
            'blob' => 'file',
            'bool' => 'checkbox',
            'boolean' => 'checkbox',
            'char' => 'text',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'decimal' => 'number',
            'double' => 'number',
            'enum' => 'select',
            'float' => 'number',
            'geometry' => 'text',
            'int' => 'number',
            'integer' => 'number',
            'json' => 'textarea',
            'longblob' => 'file',
            'longtext' => 'textarea',
            'mediumblob' => 'file',
            'mediumint' => 'number',
            'mediumtext' => 'textarea',
            'numeric' => 'number',
            'point' => 'text',
            'password' => 'password',
            'set' => 'checkbox',
            'smallint' => 'number',
            'string' => 'text',
            'text' => 'textarea',
            'time' => 'time',
            'timestamp' => 'datetime-local',
            'tinyblob' => 'file',
            'tinyint' => 'number',
            'tinytext' => 'textarea',
            'varbinary' => 'text',
            'varchar' => 'text',
            'year' => 'number',
        ];

        return $typeMap[$columnType] ?? 'text';
    }
}