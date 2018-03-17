<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\User;
use OnIt\Auth\Logic\AuthLogic;
use Illuminate\Http\Request;

class AuthController extends Controller
{
	/**
	 * @param AuthLogic $authLogic
	 * @param Request   $request
	 *
	 * @return array
	 */
    public function login(AuthLogic $authLogic, Request $request)
    {
    	$user = $authLogic->login($request->email);
    	return [
			'token'   => encrypt($user->id),
		    'user'    => $user,
		    'success' => true
 		];
    }

	/**
	 * @param AuthLogic $authLogic
	 *
	 * @return array|null
	 */
    public function check(AuthLogic $authLogic):? array
    {
    	$user = $authLogic->check();

    	return [
    		'success' => $user !== null,
		    'user'    => $user
	    ];
    }
}
