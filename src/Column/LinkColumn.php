<?php

namespace BalajiDharma\LaravelCrud\Column;

use BalajiDharma\LaravelCrud\Column;

class LinkColumn extends Column
{
    public $grid;

    public function renderData($model, $index, $field)
    {
        $value = $model->{$field["attribute"]};
        if (isset($field['list']['value'])) {
            $value = $field['list']['value']($model);
        }
        $url = route($field['list']['route'], $this->getRouteParams($field['list']['route_params'], $model));

        return '<a href="' . $url . '" '.render_form_attributes($field['list']['attr'] ?? []).'>' . $value . '</a>';
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