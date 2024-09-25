<?php
use BalajiDharma\LaravelCrud\CrudBuilder;

if (! function_exists('crud')) {

    function crud(CrudBuilder $crud, $view = 'list')
    {
        return $crud->render($view);
    }

}