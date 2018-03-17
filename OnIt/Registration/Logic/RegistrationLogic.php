<?php

namespace OnIt\Registration\Logic;


use App\User;
use App\Http\Requests\RegistrationRequest;
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
     */
    public function register(RegistrationRequest $request): User
    {
        $user = $this->registrationRepository->create($request->email);

        $this->imageLogic->train($request, $user->getAttribute('id'));

        return $user;
    }
}
