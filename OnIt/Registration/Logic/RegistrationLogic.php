<?php
namespace OnIt\Registration\Logic;

use App\User;
use OnIt\Registration\Repository\RegistrationRepository;

class RegistrationLogic
{
	/** @var RegistrationRepository */
	private $registrationRepository;

	/**
	 * RegistrationLogic constructor.
	 *
	 * @param RegistrationRepository $registrationRepository
	 */
	public function __construct(RegistrationRepository $registrationRepository)
	{
		$this->registrationRepository = $registrationRepository;
	}

	/**
	 * @param string $email
	 *
	 * @return User
	 */
	public function register(string $email): User
	{
		return $this->registrationRepository->create($email);
	}
}