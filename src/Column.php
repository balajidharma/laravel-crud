<?php

namespace BalajiDharma\LaravelCrud;

class Column
{
    public $grid;

    public function __construct($grid)
    {
        $this->grid = $grid;
    }

    public function renderData($model, $index, $field)
    {
        $mode = $this->grid->mode;

        if (isset($field['class'])) {
            $child = new $field['class']($this->grid);
            return $child->renderData($model, $index, $field);
        }
        if (isset($field[$mode]['class'])) {
            $child = new $field[$mode]['class']($this->grid);
            return $child->renderData($model, $index, $field);
        }
        if (isset($field['value'])) {
            if (is_callable($field['value'])) {
                $value = $field['value']($model, $index);
            } else {
                $value = $field['value'];
            }
        } else {
            $value = $model->{$field['attribute']};
        }
        return $value;
    }
}