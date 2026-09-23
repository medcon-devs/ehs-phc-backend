<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface AgendaDetailsInterface extends BaseInterface
{
    public function add(Request $request, $id);
}
