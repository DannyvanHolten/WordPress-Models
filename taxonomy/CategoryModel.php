<?php

namespace App\Models;

/**
 * This is the model if you want to get posts of the Category Taxonomy
 *
 * Class CategoryModel
 * @package App\Models
 */
final class CategoryModel extends BaseTaxonomyModel
{

	/**
	 * CategoryModel constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('category');
	}
}