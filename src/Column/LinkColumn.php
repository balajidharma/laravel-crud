<?php

namespace BalajiDharma\LaravelCrud\Column;

use BalajiDharma\LaravelCrud\Column;

class LinkColumn extends Column
{
    public $grid;

    public function renderData($model, $index, $field)
    {

        $url = route($field['list']['route'], $this->getRouteParams($field['list']['route_params'], $model));

        return '<a href="' . $url . '">' . $model->{$field["attribute"]} . '</a>';
    }

    protected function getRouteParams($routeParams, $model)
    {
        $params = [];

        foreach ($routeParams as $key => $value) {
            $params[$key] = $model->{$value};
        }

        return $params;
    }
}