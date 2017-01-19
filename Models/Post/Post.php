<?php

namespace Models\Post;

/**
 * This is the model if you want to get posts of all the post types.
 *
 * Class Post
 * @package App\Models|Post
 */
class Post extends WP_Post
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