<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface EventInterface extends BaseInterface
{

    public function gallery(Request $request);
}
