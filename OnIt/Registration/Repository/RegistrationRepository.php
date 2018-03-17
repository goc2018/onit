<?php

namespace OnIt\Registration\Repository;

use App\User;

class RegistrationRepository
{
	/**
	 * @param string $emailAddress
	 *
	 * @return User
	 */
	public function create($emailAddress): User
	{
		$model = new User();
		$model->email = $emailAddress;
		$model->save();

		return $model;
	}
}