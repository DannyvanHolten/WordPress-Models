<?php

namespace Models\User;

/**
 * This is the model if you want to get all the users
 *
 * Class User
 * @package App\Models|User
 */
class User extends WP_User
{
	/**
	 * Subscriber constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		// No type is defined because for users it is not necessary to do so
	}
}