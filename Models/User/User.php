<?php

namespace App\Models\User;

use App\Models\Post\Post;

/**
 * The User is used to implement the core functions of get_users
 *
 * The User is used for all functions that should be usable in all the other Taxonomy Models.
 * If you want to extend the User do so by at least defining a taxonomy in your extended class
 *
 * @see https://developer.wordpress.org/reference/functions/get_terms/
 *
 * Class User
 * @package App\Models\User
 */
class User
{
	/**
	 * An array of arguments to get the terms
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * The Terms
	 *
	 * @var array
	 */
	protected $terms = null;

	/**
	 * The Post
	 *
	 * @var array
	 */
	protected $post = null;

	/**
	 * Send trough all our functions to real functions, so we don't need to make a new instance for everything
	 * inspired by Laravel
	 *
	 * @param $name
	 * @param $arguments
	 * @return $this
	 */
	public function __call($name, $arguments)
	{
		call_user_func_array([$this, $name], $arguments);

		return $this;
	}

	/**
	 * Send trough all our static functions to real functions, so we don't need to make a new instance for everything
	 * inspired by Laravel
	 *
	 * @param $name
	 * @param $arguments
	 * @return static
	 */
	public static function __callStatic($name, $arguments)
	{
		$instance = new static;
		call_user_func_array([$instance, $name], $arguments);

		return $instance;

	}

	/**
	 * Return all terms.
	 * Note: Default limits to 0 (ALL). If you need less, adjust the limit.
	 *
	 * @see User::take()
	 * @see User::hideEmpty()
	 * @see User::get()
	 *
	 * @example ExampleUser::all();
	 * @example ExampleUser::all(10);
	 *
	 * @param null|int $take
	 * @return $instance
	 */
	public static function all($take = -1)
	{
		$instance = new static;

		return $instance->take($take)
			->get();
	}

	/**
	 * Return the posts respecting the the default post per page setting in WordPress.
	 *
	 * @see User::take()
	 * @see User::paginate()
	 *
	 * @example ExampleUser::archive();
	 * @example ExampleUser::archive(10);
	 *
	 * @param null|string|int $take
	 * @return $instance
	 */
	public static function archive($take = null)
	{
		$instance = new static;

		return $instance->take($take)
			->paginate();
	}

	/**
	 * Find a single term by ID | Slug.
	 *
	 * @see User::whereIn();
	 * @see User::hideEmpty()
	 * @see User::get()
	 * @see User::first()
	 *
	 * @example ExampleUser::find();
	 * @example ExampleUser::find([1,2,3]);
	 *
	 * @param int|string|array $id
	 * @return $instance
	 */
	public static function find($id = null)
	{
		$instance = new static;

		$instance->id($id);

		if (is_array($id)) {
			return $instance->get();
		} else {
			return $instance->first();
		}
	}

	/**
	 * Return Posts where they are by a certain author
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#author-parameters
	 * @see https://developer.wordpress.org/reference/functions/get_current_user_id/
	 *
	 * @example ExampleUser::by('me')->get()
	 * @example ExampleUser::by(1)->first()
	 *
	 * @param null $author
	 * @param bool $exclude
	 * @return $this
	 */
	public function id($id = null, $exclude = false)
	{
		if ($id == null) {
			$this->args['include'] = [get_current_user_id()];
		} else {

			if (!is_array($id)) {
				$id = [$id];
			}

			if ((is_array($id) && is_int(current($id)) && $exclude === true)) {
				$this->args['exclude'] = $id;
			} elseif (is_array($id) && is_int(current($id))) {
				$this->args['include'] = $id;
			} elseif ($exclude === true) {
				$this->args['login__not_in'] = $id;
			} else {
				$this->args['login__in'] = $id;
			}

		}

		return $this;
	}

	/**
	 * Get only certain fields instead of entire Post objects
	 *
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Return_Fields_Parameter
	 *
	 * @example ExampleUser::fields('ids')->get();
	 *
	 * @param null|int $fields
	 * @return $this
	 */
	protected function fields($fields = null)
	{
		if ($fields !== null) {
			$this->args['fields'] = $fields;
		}

		return $this;
	}

	/**
	 * Order the results within the Query
	 * The first value is the order by value, the second is either ascending or descending
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_terms/#parameters
	 *
	 * @example ExampleUser::orderBy('date','ASC')->get();
	 * @example ExampleUser::take(3)->orderBy('online','ASC', 'status')->get();
	 *
	 * @param string $orderBy
	 * @param null|string $order (ASC or DESC)
	 * @param null|string $meta_key
	 * @return $this
	 */
	protected function orderBy($orderBy, $order = null, $meta_key = null)
	{

		$this->args['orderby'] = $orderBy;

		if ($order !== null) {
			$this->args['order'] = $order;
		}

		if ($meta_key !== null) {
			$this->args['meta_key'] = $meta_key;
		}

		return $this;
	}

	/**
	 * Skip the number of posts defined by skip
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_terms/#parameters
	 *
	 * @example ExampleUser::skip(3)->get();
	 *
	 * @param int $skip
	 * @return $this
	 */
	protected function skip($skip = 0)
	{
		if ($skip !== 0) {
			$this->args['offset'] = $skip;
		}

		return $this;
	}

	/**
	 * Return Posts where they are by a certain author
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#author-parameters
	 * @see https://developer.wordpress.org/reference/functions/get_current_user_id/
	 *
	 * @example ExampleUser::by('me')->get()
	 * @example ExampleUser::by(1)->first()
	 *
	 * @param null $author
	 * @param bool $exclude
	 * @return $this
	 */
	public function type($role = null, $exclude = false, $strict = false)
	{
		if (!is_array($role)) {
			$role = [$role];
		}

		if ($role !== null) {
			if ($exclude === true && $role) {
				$this->args['role__not_in'] = $role;
			} elseif ($strict === true) {
				$this->args['role'] = $role;
			} else {
				$this->args['role__in'] = $role;
			}
		}

		return $this;
	}

	/**
	 * Limit the terms
	 * Use 0 for everything.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_terms/#parameters
	 *
	 * @example ExampleUser::take(3)->get();
	 *
	 * @param $take
	 * @return $this
	 */
	protected function take($take = null)
	{
		if ($take !== null) {
			$this->args['number'] = $take;
		}

		return $this;
	}

	/**
	 * Get terms by a certain meta query.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_terms/#parameters
	 *
	 * @example ExampleUser::where('active', 1)->where('spotlight', 'front')->get();
	 * @example ExampleUser::where('spotlight', 'footer', 'IN', 'OR')->where('spotlight', 'front')->get();
	 *
	 * @param $meta_key
	 * @param $meta_value
	 * @param string $meta_compare
	 * @return $this
	 */
	protected function where($meta_key, $meta_value, $meta_compare = '=', $meta_relation = 'AND')
	{
		// Check if there already is a query. If not, create a relation field first
		if (!isset($this->args['meta_query'])) {
			$this->args['meta_query'] = [
				'relation' => $meta_relation
			];
		}

		// Check if this key has already been set. If so, overwrite it.
		foreach ($this->args['meta_query'] as $key => $meta) {
			if (is_array($meta) && in_array($meta_key, $meta)) {
				unset($this->args['meta_query'][$key]);
			}
		}

		// Create a new query argument
		$this->args['meta_query'][] = [
			'key'     => $meta_key,
			'value'   => $meta_value,
			'compare' => $meta_compare
		];

		return $this;
	}

	/**
	 * Get terms by post
	 * Note: You can enter either an integer, string or an array.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_get_post_terms/
	 * @see https://developer.wordpress.org/reference/functions/get_queried_object/
	 *
	 * @example ExampleUser::wherePost()->get();
	 * @example ExampleUser::wherePost(10)->get();
	 *
	 * @param null|int $postID
	 * @return $this
	 * @throws \Exception
	 */
	protected function published($postID = null)
	{
		if ($postID === null) {
			$queriedObject = get_queried_object();

			if ($queriedObject instanceof WP_Post) {
				$postID = $queriedObject->ID;
			} else {
				throw new \Exception('User::wherePost is null and cannot verify the queried object is a WP_Post object');
			}
		}

		$this->args['include'] = Post::find($postID)->post_author;

		return $this;
	}

//	/**
//	 * Parse our query and execute all the functions to make our content super fancy
//	 *
//	 * @see https://developer.wordpress.org/reference/functions/wp_get_post_terms/
//	 * @see https://developer.wordpress.org/reference/functions/get_terms/
//	 *
//	 * @see BasePostModel::runquery();
//	 * @see BasePostModel::appendAcfFields();
//	 * @see BasePostModel::appendContent();
//	 * @see BasePostModel::appendExcerpt();
//	 * @see BasePostModel::appendPermalink();
//	 *
//	 * @example ExampleUser::take(10)->get();
//	 *
//	 * @return array|int|\WP_Error
//	 */
//	public function get()
//	{
//		if ($this->post !== null) {
//			$this->terms = wp_get_post_terms($this->post, $this->args['taxonomy'], $this->args);
//		} else {
//			$this->terms = get_terms($this->args);
//		}
//
//		$this->appendAcfFields()
//			->appendDescription()
//			->appendPermalink();
//
//		return $this->terms;
//	}
//
//	/**
//	 * Get the first result of our Query
//	 *
//	 * @see User::take();
//	 * @see User::get();
//	 *
//	 * @example ExampleUser::whereIn(1)->first();
//	 *
//	 * @return mixed
//	 */
//	public function first()
//	{
//		$this->take(1)
//			->get();
//
//		if (isset($this->terms[0])) {
//			return $this->terms[0];
//		}
//
//		return false;
//	}
//
//	/**
//	 * Count the results of our Query
//	 *
//	 * @see User::get();
//	 *
//	 * @example ExampleUser::where('active', 1)->count();
//	 *
//	 * @return mixed
//	 *
//	 * @todo: reform to users
//	 */
//	public function count()
//	{
//		if (!isset($this->terms)) {
//			$this->get();
//		}
//
//		return count($this->terms);
//	}
//
//	/**
//	 * Get all the ACF fields that are related to our posts
//	 *
//	 * @see https://www.advancedcustomfields.com/resources/get_fields/
//	 *
//	 * @return mixed
//	 */
//	private function appendAcfFields()
//	{
//		if (function_exists('get_fields')) {
//			foreach ($this->terms as $term) {
//				if ($term instanceof WP_Term) {
//					$term->fields = get_fields($term->taxonomy . '_' . $term->term_id);
//				}
//			}
//		}
//
//		return $this;
//	}
//
//	/**
//	 * Add P tags around our description.
//	 *
//	 * @see https://developer.wordpress.org/reference/functions/wpautop/
//	 *
//	 * @return mixed
//	 */
//	private function appendDescription()
//	{
//		foreach ($this->terms as $term) {
//			if ($term instanceof WP_Term) {
//				$term->description = wpautop($term->description);
//			}
//		}
//
//		return $this;
//	}
//
//	/**
//	 * Add the permalink to our query result
//	 *
//	 * @see https://developer.wordpress.org/reference/functions/get_term_link/
//	 *
//	 * @return mixed
//	 */
//	private function appendPermalink()
//	{
//		foreach ($this->terms as $term) {
//			if ($term instanceof WP_Term) {
//				$term->permalink = get_term_link($term->term_id);
//			}
//		}
//
//		return $this;
//	}

}