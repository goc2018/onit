<?php

namespace App\Http\Controllers\Phone;

use App\Http\Requests\RegistrationRequest;
use App\User;
use App\Http\Controllers\Controller;
use OnIt\Registration\Logic\RegistrationLogic;

class RegistrationController extends Controller
{
	/**
	 * @param RegistrationLogic   $registrationLogic
	 * @param RegistrationRequest $request
	 *
	 * @return User
	 */
	public function registration(RegistrationLogic $registrationLogic, RegistrationRequest $request)
	{
		return $registrationLogic->register($request->email);
	}
}
