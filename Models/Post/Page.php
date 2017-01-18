<?php

namespace App\Models\Post;

/**
 * This is the model if you want to get posts of the page post type.
 *
 * Class PageModel
 * @package App\Models
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
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
	 * @see BaseModel::meta()
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