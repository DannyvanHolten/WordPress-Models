<?php

namespace Models\PostModel;

/**
 * This is the model if you want to get posts of all the post types.
 *
 * Class Post
 * @package Models\PostModel
 */
class Post extends PostModel
{
	/**
	 * PostModel constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('any');
	}
}