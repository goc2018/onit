<?php

namespace OnIt\Auth\Logic;

use App\User;
use Illuminate\Support\Facades\Auth;
use OnIt\Auth\Exception\FailedLoginException;
use OnIt\User\Repository\UserRepository;

class AuthLogic
{
	/** @var UserRepository */
	private $userRepository;

	/**
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param string $email
	 *
	 * @return string
	 *
	 * @throws FailedLoginException
	 */
	public function login(string $email): User
	{
		$user = $this->userRepository->getUserByEmail($email);
		if (!$user)
		{
			throw new FailedLoginException('Wrong email!');
		}

		return $user;
	}

	/**
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function check()
	{
		if (!Auth::check())
		{
			return null;
		}

		return Auth::user();
	}
}