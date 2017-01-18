<?php

namespace App\Models\Term;

/**
 * This is the model if you want to get the terms of the Category Taxonomy
 *
 * Class CategoryModel
 * @package App\Models
 */
final class Category extends Term
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