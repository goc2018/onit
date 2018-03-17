<?php

namespace OnIt\Registration\Logic;


use App\User;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Support\Facades\DB;
use OnIt\Image\Logic\ImageLogic;
use OnIt\Registration\Repository\RegistrationRepository;


class RegistrationLogic
{
    /** @var ImageLogic */
    private $imageLogic;

    /** @var RegistrationRepository */
    private $registrationRepository;

    /**
     * @param ImageLogic             $imageLogic
     * @param RegistrationRepository $registrationRepository
     */
    public function __construct(ImageLogic $imageLogic, RegistrationRepository $registrationRepository)
    {
        $this->imageLogic             = $imageLogic;
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @param RegistrationRequest $request
     *
     * @return User
     *
     * @throws \Exception
     */
    public function register(RegistrationRequest $request): User
    {
        DB::beginTransaction();

        try
        {
            $user = $this->registrationRepository->create($request->email);

            $this->imageLogic->train($request, $user->getAttribute('id'));

            DB::commit();

            return $user;
        }
        catch (\Exception $ex)
        {
            DB::rollBack();

            throw $ex;
        }
    }
}
