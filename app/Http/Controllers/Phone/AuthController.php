<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OnIt\Registration\Logic\RegistrationLogic;

class AuthController extends Controller
{
    public function registration()
    {
	   $foo = new RegistrationLogic();
	   $foo->foo();
    }
}
