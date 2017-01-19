<?php

namespace App\Models\User;

/**
 * This is the model if you want to get the terms of the Category Taxonomy
 *
 * Class Subscriber
 * @package App\Models|User
 */
class Subscribe extends User
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