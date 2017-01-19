<?php

namespace App\Models\Post;

/**
 * This is the model if you want to get posts of the page post type.
 *
 * Class Page
 * @package App\Models|Post
 */
class Page extends Post
{

	/**
	 * PageModel constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('page');
	}

	/**
	 * Return a single template page.
	 * Based on the themosis page template postmeta
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
	 * @see Post::where()
	 *
	 * @param null $template
	 * @return $this
	 */
	public static function template($template = null)
	{
		$instance = new static;

		$instance->where('_wp_page_template', $template);

		return $instance->first();
	}
}