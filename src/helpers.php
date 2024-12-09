<?php

use BalajiDharma\LaravelCrud\CrudBuilder;

if (! function_exists('crud')) {

    function crud(CrudBuilder $crud, $view = 'list')
    {
        return $crud->render($view);
    }

}

if (! function_exists('crudRedirect')) {

    function crudRedirect($name, $message)
    {
        $redirectUrl = request()->input('_redirect', route($name));

        return redirect($redirectUrl)->withMessage(__($message));
    }

}
