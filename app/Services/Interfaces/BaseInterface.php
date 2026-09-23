<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface BaseInterface
{

    public function paginate(Request $request);

    public function store(Request $request);

    public function update(Request $request, $id);

    public function getBuilder($columns);

    public function getColumns(Request $request);

    public function getListByColumns($columns);

    public function getOneByColumns($columns);

    public function delete($id);

}
