<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface UserInterface extends BaseInterface
{
    function login(Request $request);

    function loginByEmail(Request $request);

    function loginById($id);

    function forgotPassword(Request $request);

    function resetPassword(Request $request);

}
