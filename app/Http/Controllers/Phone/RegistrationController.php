<?php

namespace App\Http\Controllers\Phone;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use OnIt\Registration\Logic\RegistrationLogic;


class RegistrationController extends Controller
{
    /**
     * @param RegistrationLogic   $registrationLogic
     * @param RegistrationRequest $request
     *
     * @return array
     */
    public function registration(RegistrationLogic $registrationLogic, RegistrationRequest $request)
    {
        try
        {
            $user = $registrationLogic->register($request);

            return [
                'success' => true,
                'result'  => $user
            ];
        }
        catch (\Exception $exception)
        {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}
