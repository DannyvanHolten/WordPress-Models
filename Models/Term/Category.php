<?php

namespace Models\Term;

/**
 * This is the model if you want to get the terms of the Category Taxonomy
 *
 * Class Category
 * @package App\Models|Term
 */
class Category extends Term
{

	/**
	 * Category constructor.
	 *
	 * Set all the arguments that are default for this Model
	 */
	public function __construct()
	{
		$this->type('category');
	}
}