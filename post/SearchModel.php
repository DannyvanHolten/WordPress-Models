<?php

namespace App\Models;

/**
 * This is the model if you want to get ALL post types.
 * Note: DO NOT use if you only need one post type.
 * Basically the only exclusion where you are allowed to use it will be the main search page.
 *
 * Class SearchModel
 * @package App\Models
 */
final class SearchModel extends BasePostModel
{
	/**
	 * SearchModel constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('any');
	}
}