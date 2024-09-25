<?php

namespace BalajiDharma\LaravelCrud\Column;

use BalajiDharma\LaravelCrud\Column;

class ListColumn extends Column
{
    public $grid;

    public function renderData($model, $index, $field)
    {
        $attribute = $field['list']["attribute"] ?? 'name';
        return $this->buildList($model->{$field["attribute"]}?->pluck($attribute));
    }

    function buildList($items) {
        $html = '<ul>';
        foreach ($items as $item) {
            $html .= '<li>' . trim($item) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}