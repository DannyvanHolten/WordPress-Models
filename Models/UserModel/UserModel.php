<?php

namespace WordPressModels\UserModel;

use WordPressModels\PostModel\Post;
use WP_Post;
use WP_User;

/**
 * The WP_User is used to implement the core functions of get_users
 *
 * The WP_User is used for all functions that should be usable in all the other User Models.
 * If you want to extend the WP_User do so by at least extending the WP_User class
 *
 * @see https://developer.wordpress.org/reference/functions/get_users/
 *
 * Class UserModel
 * @package WordPressModels\UserModel
 */
abstract class UserModel
{
	/**
	 * An array of arguments to get the terms
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * The users
	 *
	 * @var array
	 */
	protected $users = null;

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
	 * Return all users.
	 * Note: Default limits to -1 (ALL). If you need less, adjust the limit.
	 *
	 * @see User::take()
	 * @see User::hideEmpty()
	 * @see User::get()
	 *
	 * @example ExampleUser::all();
	 * @example ExampleUser::all(10);
	 *
	 * @param null|int $take
	 *
	 * @return $instance
	 */
	public static function all($take = -1)
	{
		$instance = new static;

		return $instance->take($take)
			->get();
	}

	/**
	 * Return the users respecting the the default post per page setting in WordPress.
	 *
	 * @see User::take()
	 * @see User::paginate()
	 *
	 * @example ExampleUser::archive();
	 * @example ExampleUser::archive(10);
	 *
	 * @param null|string|int $take
	 *
	 * @return $instance
	 */
	public static function archive($take = null)
	{
		$instance = new static;

		return $instance->take($take)
			->paginate();
	}

	/**
	 * Find a single user by ID | Login.
	 *
	 * @see User::id()
	 * @see User::get()
	 * @see User::first()
	 *
	 * @example ExampleUser::find();
	 * @example ExampleUser::find([1,2,3]);
	 *
	 * @param int|string|array $id
	 *
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
	 * Return users where they have a certain ID / Login
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::by('me')->get()
	 * @example ExampleUser::by(1)->first()
	 *
	 * @param null|int|string|array $id
	 * @param bool $exclude
	 *
	 * @return $this
	 */
	protected function id($id = null, $exclude = false)
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
	 * Get only certain fields instead of entire WP_User objects
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::fields('ids')->get();
	 *
	 * @param null|int $fields
	 *
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
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::orderBy('date','ASC')->get();
	 * @example ExampleUser::take(3)->orderBy('online','ASC', 'status')->get();
	 *
	 * @param string $orderBy
	 * @param null|string $order (ASC or DESC)
	 * @param null|string $meta_key
	 *
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
	 * Execute UserModel::where with meta_relation OR
	 *
	 * @see UserModel::where();
	 *
	 * @example ExampleUser::where('active', 1)->orWhere('spotlight', 'front')->get();
	 *
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param string $meta_compare
	 *
	 * @return $this
	 */
	protected function orWhere($meta_key, $meta_value, $meta_compare = '=')
	{
		$this->where($meta_key, $meta_value, $meta_compare, 'OR');

		return $this;
	}

	/**
	 * Skip the number of users defined by skip
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::skip(3)->get();
	 *
	 * @param int $skip
	 *
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
	 * Return Users where they have a certain role
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_terms/#parameters
	 *
	 * @example ExampleUser::by('me')->get()
	 * @example ExampleUser::by(1)->first()
	 *
	 * @param null $role
	 * @param bool $exclude
	 * @param bool $strict
	 *
	 * @return $this
	 */
	protected function type($role = null, $exclude = false, $strict = false)
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
	 * Limit the users
	 * Use -1 for everything.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::take(3)->get();
	 *
	 * @param $take
	 *
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
	 * Get users by a certain meta query.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_user_query/prepare_query/#parameters
	 *
	 * @example ExampleUser::where('active', 1)->where('spotlight', 'front')->get();
	 * @example ExampleUser::where('spotlight', 'footer', 'IN', 'OR')->where('spotlight', 'front')->get();
	 *
	 * @param $meta_key
	 * @param $meta_value
	 * @param string $meta_compare
	 *
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
	 * Get users that published a certain post c.q. the post author.
	 *
	 * @see WP_Post::find();
	 *
	 * @example ExampleUser::published()->get();
	 * @example ExampleUser::published(100)->get();
	 *
	 * @param null|int $postID
	 *
	 * @return $this
	 *
	 * @throws \Exception
	 */
	protected function published($postID = null)
	{
		if ($postID === null) {
			$queriedObject = get_queried_object();

			if ($queriedObject instanceof WP_Post) {
				$postID = $queriedObject->ID;
			} else {
				throw new \Exception('User::published is null and cannot verify the queried object is a WP_Post object');
			}
		}

		$this->args['include'] = Post::find($postID)->post_author;

		return $this;
	}

	/**
	 * Parse our query and execute all the functions to make our content super fancy
	 *
	 * @see UserModel::runquery();
	 * @see UserModel::appendAcfFields();
	 * @see UserModel::appendPermalink();
	 *
	 * @example ExampleUser::take(10)->query	();
	 *
	 * @return array
	 */
	public function query()
	{
		$this->runQuery()
			->appendAcfFields()
			->appendPermalink();

		return collect($this->users);
	}

	/**
	 * Return all items of our collection build by the query function
	 *
	 * @see UserModel::query();
	 *
	 * @example ExampleUser::take(10)->get();
	 *
	 * @return array
	 */
	public function get()
	{
		return $this->query()
			->all();
	}

	/**
	 * Get the first result of our collection
	 *
	 * @see UserModel::take();
	 * @see UserModel::get();
	 *
	 * @example ExampleUser::id(1)->first();
	 *
	 * @return array
	 */
	public function first()
	{
		return $this->take(1)
			->query()
			->first();
	}

	/**
	 * Count the results of our Query
	 *
	 * @see User::get();
	 *
	 * @example ExampleUser::where('active', 1)->count();
	 *
	 * @return int
	 *
	 */
	public function count()
	{
		if (!isset($this->users)) {
			$this->get();
		}

		return count($this->users);
	}

	/**
	 * Get all the ACF fields that are related to our users
	 *
	 * @see https://www.advancedcustomfields.com/resources/get_fields/
	 *
	 * @return $this
	 */
	private function appendAcfFields()
	{
		if (function_exists('get_fields')) {
			foreach ($this->users as $user) {
				if ($user instanceof WP_User) {
					$user->fields = get_fields('user_' . $user->ID);
				}
			}
		}

		return $this;
	}


	/**
	 * Add the permalink to our query result
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_author_posts_url/
	 *
	 * @return $this
	 */
	private function appendPermalink()
	{
		foreach ($this->users as $user) {
			if ($user instanceof WP_User) {
				$user->permalink = get_author_posts_url($user->ID);
			}
		}

		return $this;
	}

	/**
	 * Create a new query
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_users/
	 *
	 * @return $this
	 */
	private function runQuery()
	{
		$this->users = get_users($this->args);

		return $this;
	}

}