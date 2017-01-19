<?php

namespace Models\User;

/**
 * This is the model if you want to get the users of the rol subscriber
 *
 * Class Subscriber
 * @package App\Models|User
 */
class Subscriber extends User
{
	/**
	 * Subscriber constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('subscriber');
	}
}