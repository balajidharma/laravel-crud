<?php

namespace BalajiDharma\LaravelCrud;

use BalajiDharma\LaravelFormBuilder\Facades\FormBuilder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrudBuilder
{
    public $dataProvider;

    public $fields;

    public $form;

    public $mode;

    public $request;

    private $crudHelper;

    public $title = 'Title';

    public $description = 'Description';

    public $model;

    public $route;

    public $routes = [];

    public $items = null;

    public $item = null;

    public $identifier;

    public $results = null;

    public $emptyCell = '&nbsp;';

    public $showFieldErrors = false;

    public $addtional = [];

    public function __construct()
    {
        $this->crudHelper = new CrudHelper();
    }

    public function columns()
    {
        return [];
    }

    public function setIdentifier()
    {
        if (!$this->identifier) {
            $this->identifier = strtolower(class_basename($this->dataProvider->getModel())).'_';
        }
    }

    public function setAddtional($addtional)
    {
        $this->addtional = array_merge($this->addtional, $addtional);
    }

    public function list($dataProvider)
    {
        if (!($dataProvider instanceof Builder)) {
            throw new Exception('dataProvider must be instance of '.Builder::class);
        }

        $this->mode = 'list';

        $this->request = request();

        $this->dataProvider = $dataProvider;

        $this->buildRows($this->columns());

        $this->setIdentifier();

        $this->applyFullTextSearch();

        $this->applyFilters();

        $this->applySorting();

        $this->routes = $this->buildRoutes($this->route ?? null);

        $this->results = $this->applyPagination();

        $this->items = $this->applyDisplayValue($this->results);

        return $this;
    }

    public function form($dataProvider = null)
    {
        $this->mode = 'create';

        if($dataProvider) {
            $this->mode = 'edit';
            $this->dataProvider = $dataProvider;
        } else {
            $this->dataProvider = new $this->model;
        }

        $this->request = request();

        $this->buildRows($this->columns());

        $this->setIdentifier();

        $this->routes = $this->buildRoutes($this->route ?? null);

        $this->form = $this->buildForm();

        return $this;
    }

    public function show($dataProvider)
    {
        $this->mode = 'show';

        $this->dataProvider = $dataProvider;

        $this->buildRows($this->columns());

        $this->setIdentifier();

        $this->routes = $this->buildRoutes($this->route ?? null);

        $this->item = $this->applyDisplayValue([$this->dataProvider])[0] ?? [];

        return $this;
    }

    private function buildRows($columns = [])
    {
        $model = $this->dataProvider->getModel();
        $tableName = $model->getTable();

        // Get all column names and their types for the table
        $tableColumns = Schema::getColumnListing($tableName);

        foreach ($columns as $column) {
            $attribute = $column['attribute'] ?? null;
            $type = $column['type'] ?? 'custom';
            $fillable = false;
            $primaryKey = false;

            if ($attribute && !isset($column['type']) && in_array($attribute, $tableColumns)) {
                $type = DB::getSchemaBuilder()->getColumnType($tableName, $attribute);
                $type = $this->crudHelper->getInputType($type);
                $primaryKey = $attribute == $model->getKeyName();
            }
            if($attribute)
            {
                $fillable = $model->isFillable($attribute);
            }

            $this->fields[] = $this->mergeOptions($this->columnDefault($attribute, $type, $fillable, $primaryKey), $column);
        }
    }

    private function columnDefault(
        $attribute, 
        $type = 'text', 
        $fillable = false,
        $primaryKey = false,
        $sortable = false,
        $filter = null)
    {
        return [
            'attribute' => $attribute,
            'type' => $type,
            'fillable' => $fillable,
            'primaryKey' => $primaryKey,
            'sortable' => $sortable,
            'filter' => $filter,
        ];
    }

    /**
     * Merge options array.
     *
     * @return array
     */
    public function mergeOptions(array $targetOptions, array $sourceOptions)
    {
        return array_replace_recursive($targetOptions, $sourceOptions);
    }


    public function applyFilters()
    {
        foreach ($this->fields as $field) {
            if (isset($field['filter'])) {
                // Check if the field is a relationship
                if ($this->request->filled($this->identifier.$field['attribute']) && isset($field['relation'])) {
                    $relation = $field['relation'];
                    $relationField = $field['relation_field'] ?? $field['attribute'];
                    $attribute = $field['attribute'];
                    $relationQuery = $this->dataProvider->whereHas($relation, function ($q) use ($relationField, $field, $attribute) {
                        $this->applyFieldFilter($q, $relationField, $field, $attribute);
                    });
                } else {
                    $this->applyFieldFilter($this->dataProvider, $field['attribute'], $field, $field['attribute']);
                }
            }
        }
    }

    public function applyFullTextSearch()
    {
        $searchQuery = $this->request->input($this->identifier.'search', '');
        if (!empty($searchQuery)) {
            $this->dataProvider->where(function ($query) use ($searchQuery) {
                foreach ($this->fields as $field) {
                    if (isset($field['searchable']) && $field['searchable']) {
                        $query->orWhere($field['attribute'], 'LIKE', "%{$searchQuery}%");
                    }
                }
            });
        }
    }

    private function applyFieldFilter(Builder $query, $field, $fieldConfig, $attribute)
    {
        // Specify the table name or alias to avoid ambiguity
        $table = $query->getModel()->getTable();
        $qualifiedField = "{$table}.{$field}";
    
        if (in_array($fieldConfig['filter'], ['like', 'ilike']) && $this->request->filled($this->identifier.$attribute)) {
            $query->where($qualifiedField, $fieldConfig['filter'], '%' . $this->request->input($this->identifier.$attribute) . '%');
        } elseif ($fieldConfig['filter'] === 'between') {
            $startKey = $attribute . '_start';
            $endKey = $attribute . '_end';
            if ($this->request->filled($this->identifier.$startKey) && $this->request->filled($this->identifier.$endKey)) {
                $query->whereBetween($qualifiedField, [$this->request->input($this->identifier.$startKey), $this->request->input($this->identifier.$endKey)]);
            } elseif ($this->request->filled($this->identifier.$startKey)) {
                $query->where($qualifiedField, '>=', $this->request->input($this->identifier.$startKey));
            } elseif ($this->request->filled($this->identifier.$endKey)) {
                $query->where($qualifiedField, '<=', $this->request->input($this->identifier.$endKey));
            }
        } elseif ($this->request->filled($this->identifier.$attribute)) {
            $query->where($qualifiedField, $fieldConfig['filter'] ?? '=', $this->request->input($this->identifier.$attribute));
        }
    }


    public function applySorting()
    {
        if ($this->request->has($this->identifier.'sort')) {
            $attribute = $this->request->input($this->identifier.'sort');
            $sortOrder = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sortOrder = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $field = collect($this->fields)->where('attribute', $attribute)->first() ?? null;

            if($field && isset($field['sortable']))
            {
                if(isset($field['relation'])) {
                    $relation = $field['relation'];
                    $relationField = $field['relation_field'] ?? $field['attribute'];
                    $this->dataProvider->whereHas($relation, function ($q) use ($relationField, $sortOrder) {
                        $table = $q->getModel()->getTable();
                        $qualifiedField = "{$table}.{$relationField}";
                        $q->orderBy($qualifiedField, $sortOrder);
                    });
                } else {
                    $table = $this->dataProvider->getModel()->getTable();
                    $this->dataProvider->orderBy("{$table}.{$attribute}", $sortOrder);
                }
            }
        }
    }

    public function applyPagination()
    {
        $perPage = $this->request->input('per_page', 20);
        return $this->dataProvider->paginate($perPage);
    }

    public function getRows()
    {
        return $this->fields;
    }

    public function applyDisplayValue($results)
    {
        $displayValues = [];
        foreach ($results as $index => $result) {
            $fields = $this->fields;
            foreach ($fields as $key => $field) {
                $display = $field[$this->mode] ?? true;
                if($display !== false) {
                    $displayValues[$fields[$key]['attribute']] = (new Column($this))->renderData($result, $index, $field);
                    continue;
                }
            }
            $result->setAppends(['display_values']);
            $result->display_values = $displayValues;
        }
        return $results;
    }

    public function buildRoutes($mainRoute = null)
    {
        if(!$mainRoute){
            $routeName = request()->route()->getName();
            $mainRoute = substr($routeName, 0, strrpos($routeName, '.'));
        }

        return [
            'index' => route($mainRoute.'.index'),
            'create' => route($mainRoute.'.create'),
            'store' => route($mainRoute.'.store'),
            'edit' => function ($id) use ($mainRoute) {
                return route($mainRoute.'.edit', $id);
            },
            'update' => function ($id) use ($mainRoute) {
                return route($mainRoute.'.update', $id);
            },
            'show' => function ($id) use ($mainRoute) {
                return route($mainRoute.'.show', $id);
            },
            'destroy' => function ($id) use ($mainRoute) {
                return route($mainRoute.'.destroy', $id);
            },
        ];
    }

    public function render($view)
    {
        switch ($view) {
            case 'list':
                return view('crud::list', [
                    'items' => $this->items,
                    'fields' => $this->fields,
                    'routes' => $this->routes,
                    'title' => $this->title,
                    'description' => $this->description,
                    'model' => $this->model,
                    'identifier' => $this->identifier,
                ]);
            case 'create':
            case 'edit':
                return view('crud::edit', [
                    'fields' => $this->fields,
                    'routes' => $this->routes,
                    'title' => $this->title,
                    'description' => $this->description,
                    'model' => $this->model,
                    'identifier' => $this->identifier,
                    'form' => $this->form,
                    'mode' => $this->mode,
                ]);
            case 'show':
                return view('crud::show', [
                    'item' => $this->item,
                    'fields' => $this->fields,
                    'routes' => $this->routes,
                    'title' => $this->title,
                    'description' => $this->description,
                    'model' => $this->model,
                    'identifier' => $this->identifier,
                ]);
        }
    }

    public function buildForm()
    {
        if ($this->mode == 'create') {
            $formOptions = [
                'url' => $this->routes['store'],
                'method' => 'POST'
            ];
            $submitLabel = __('Create');
        } elseif ($this->mode == 'edit') {
            $formOptions = [
                'url' => $this->routes['update']($this->dataProvider->{$this->dataProvider->getKeyName()}),
                'method' => 'PUT',
                'model' => $this->dataProvider
            ];
            $submitLabel = __('Update');
        }

        $form = FormBuilder::plain($formOptions);

        $form->setErrorsEnabled($this->showFieldErrors);

        foreach ($this->fields as $field) {
            if($field['fillable'] && isset($field['label']))
            {
                $attribute = $field['attribute'];
                $type = $field['type'];
                $fieldOptions = isset($field['form_options']) ? $field['form_options']($this->dataProvider) : [];
                if(!isset($fieldOptions['label']))
                {
                    $fieldOptions['label'] = $field['label'];
                }
                $hideField = $fieldOptions['hide'] ?? false;
                $type = $fieldOptions['field_type'] ?? $type;
                $attribute = $fieldOptions['attribute'] ?? $attribute;
                if(!$hideField) {
                    $form->add($attribute, $type, $fieldOptions);
                }
            }
        }

        $form->add('submit', 'submit', [
            'label' => $submitLabel,
        ]);

        return $form;
    }

}