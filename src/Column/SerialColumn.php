<?php

namespace BalajiDharma\LaravelCrud\Column;

use BalajiDharma\LaravelCrud\Column;

class SerialColumn extends Column
{
    public $grid;

    public function renderData($model, $index, $field)
    {
        return $this->getOffset() + $index + 1;
    }

    public function getOffset()
    {
        $pageSize = $this->grid->results?->currentPage() ?? 0;

        return $pageSize == 1 ? 0 : $this->grid->results?->perPage() * ($pageSize - 1);
    }
}