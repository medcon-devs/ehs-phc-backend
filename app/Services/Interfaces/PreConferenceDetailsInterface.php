<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface PreConferenceDetailsInterface extends BaseInterface
{
    public function add(Request $request, $id);
}
