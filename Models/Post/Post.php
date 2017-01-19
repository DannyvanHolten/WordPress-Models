<?php

namespace App\Models\Post;

use WP_Query;
use WP_Post;
use Input;
use Config;

/**
 * The Post is used to implement the core functions of the WP_Query
 *
 * The Post is used for all functions that should be usable in all the other Models.
 * If you want to extend the Post do so by at least defining a Post Type in your extended class
 *
 * @see https://developer.wordpress.org/reference/classes/wp_query/
 *
 * Class Post
 * @package App\Models|Post
 */
class Post
{

	/**
	 * An array of arguments to register the post type
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * The actual WP Query
	 *
	 * @var WP_Query
	 */
	protected $query = null;

	/**
	 * The results
	 */
	protected $results = null;

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
	 * Return all posts.
	 * Note: Default limits to -1 (ALL). If you need less, adjust the limit.
	 * Note: DO NOT USE for archive pages. Use Archive instead.
	 *
	 * @see Post::take()
	 * @see Post::get()
	 *
	 * @example ExamplePost::all();
	 * @example ExamplePost::all(10);
	 *
	 * @param null|string|int $take
	 * @return array
	 */
	public static function all($take = '-1')
	{
		$instance = new static;

		return $instance->take($take)
			->get();
	}

	/**
	 * Return the posts respecting the the default post per page setting in WordPress.
	 *
	 * @see Post::take()
	 * @see Post::paginate()
	 *
	 * @example ExamplePost::archive();
	 * @example ExamplePost::archive(10);
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
	 * Find a single post by ID.
	 *
	 * @see Post::id()
	 * @see Post::first()
	 * @see Post::get()
	 *
	 * @example PageModel::find();
	 * @example ExamplePost::find([1,2,3]);
	 *
	 * @param null|int|array $id
	 * @return \WP_Post
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
	 * Return all post that fit the search query.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#search-parameters
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
	 * @see https://www.relevanssi.com/user-manual/functions/#relevanssi_do_query
	 * @see Post::paginate();
	 * @see Input::get();
	 *
	 * @example PostModel::search();
	 *
	 * @param string $search
	 * @return $instance
	 */
	public static function search($search = null)
	{
		$instance = new static;

		if ($search == null) {
			$search = Input::get('s');
		}

		/*
		 * Reset the order if relevanssi is active, because it has it's own order.
		 */
		if (function_exists('relevanssi_do_query')) {
			$instance->args['order'] = null;
		}

		$instance->args['s'] = $search;

		return $instance->paginate();
	}

	/**
	 * Return Posts where they are by a certain author
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#author-parameters
	 * @see https://developer.wordpress.org/reference/functions/get_current_user_id/
	 *
	 * @example ExamplePost::by('me')->get()
	 * @example ExamplePost::by(1)->first()
	 *
	 * @param null $author
	 * @param bool $exclude
	 * @return $this
	 */
	protected function by($author = null, $exclude = false)
	{
		if ($author == null) {
			$this->args['author'] = get_current_user_id();
		} elseif (is_array($author) && $exclude === false) {
			$this->args['author__in'] = $author;
		} elseif (is_array($author) && $exclude === true) {
			$this->args['author__not_in'] = $author;
		} elseif (is_int($author)) {
			$this->args['author'] = $author;
		} else {
			$this->args['author_name'] = $author;
		}

		return $this;
	}

	/**
	 * Return all posts connected to a certain post.
	 * Note: the Post2Post plugin needs to be activated
	 *
	 * @see https://github.com/scribu/wp-posts-to-posts/wiki
	 *
	 * @param $connectedType
	 * @param $connectedItems
	 * @param bool|false $noPaging
	 * @param bool|false $suppressFilters
	 * @param bool|false $connectedMeta
	 * @return $this
	 */
	protected function connected(
		$connectedType,
		$connectedItems,
		$noPaging = false,
		$suppressFilters = false,
		$connectedMeta = false
	) {
		$this->args['connected_type'] = $connectedType;
		$this->args['connected_items'] = $connectedItems;
		$this->args['nopaging'] = $noPaging;
		$this->args['suppress_filters'] = $suppressFilters;

		if ($connectedMeta !== false) {
			$this->args['connected_meta'] = $connectedMeta;
		}

		return $this;
	}

	/**
	 * Get only certain fields instead of entire Post objects
	 *
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query#Return_Fields_Parameter
	 *
	 * @example ExamplePost::fields('ids')->get();
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
	 * Get the posts matching the ID's
	 * accepts an integer or an array
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#post-page-parameters
	 * @see https://developer.wordpress.org/reference/functions/get_queried_object/
	 *
	 * @example ExamplePost::id(1)->first();
	 * @example ExamplePost::id([1,2,3], true)->get();
	 * @example ExamplePost::id()->first();
	 *
	 * @param null|int|array $id
	 * @param bool $exclude
	 * @return $this
	 * @throws \Exception
	 */
	protected function id($id = null, $exclude = false)
	{

		if (is_array($id) && $exclude === false) {
			$this->args['post__in'] = $id;
		} elseif (is_array($id) && $exclude === true) {
			$this->args['post__not_in'] = $id;
		} elseif ($exclude === true) {
			$this->args['post__not_in'] = [$id];
		} elseif ($id === null) {
			$queriedObject = get_queried_object();

			if ($queriedObject instanceof WP_Post) {
				$this->args['p'] = $queriedObject->ID;
			} else {
				throw new \Exception('Post::id is null and cannot verify the queried object is a WP_Post object');
			}
		} else {
			$this->args['p'] = $id;
		}

		return $this;
	}

	/**
	 * Order the results within the Query
	 * The first value is the order by value, the second is either ascending or descending
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters
	 *
	 * @example ExamplePost::orderBy('date','ASC')->paginate();
	 * @example ExamplePost::take(3)->orderBy('online','ASC', 'status')->get();
	 *
	 * @param string $orderBy
	 * @param null|string $order (ASC or DESC)
	 * @param null|string $meta_key
	 * @return $this
	 */
	protected function orderBy(
		$orderBy,
		$order = null,
		$meta_key = null
	) {

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
	 * Get the current page for paginated pages.
	 * Will be used when you call ->paginate();
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#pagination-parameters
	 * @see https://developer.wordpress.org/reference/functions/get_query_var/
	 * @see Post::paginate()
	 *
	 * @example ExamplePost::take(10)->paginate();
	 *
	 * @param null|int $paged
	 * @return $this
	 */
	protected function paged(
		$paged = null
	) {
		if ($paged == null) {
			$paged = get_query_var('paged');
		}

		$this->args['paged'] = $paged;

		return $this;
	}

	/**
	 * Skip the number of posts defined by skip
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#pagination-parameters
	 *
	 * @example ExamplePost::skip(3)->get();
	 *
	 * @param null|int $skip
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
	 * Limit the Query Posts Per Page
	 * Use -1 for everything.
	 * Leave empty for the default post per page setting defined in the WP Admin
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#pagination-parameters
	 *
	 * @example ExamplePost::take(3)->get();
	 *
	 * @param null|int $take
	 * @return $this
	 */
	protected function take($take = null)
	{
		if ($take !== null) {
			$this->args['posts_per_page'] = $take;
		}

		return $this;
	}

	/**
	 * Return all the post of a certain post type (or multiple)
	 * Note: DO NOT use this function as is. Create a new Model and extend the Post. For example NewsModel.
	 * And define it in the constructor.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#post-type-parameters
	 *
	 * @example PostModel::type([EXAMPLE_POST_TYPE, NEWS_POST_TYPE])->get();
	 * @example ExamplePost::all();
	 *
	 * @param null|string|array $postType
	 * @return $this
	 */
	protected function type($postType = null)
	{
		if ($postType !== null) {
			$this->args['post_type'] = $postType;
		}

		return $this;
	}

	/**
	 * Get pages with by a certain meta query.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
	 *
	 * @example ExamplePost::where('active', 1)->where('spotlight', 'front')->get();
	 * @example ExamplePost::where('spotlight', 'footer', 'IN', 'OR')->where('spotlight', 'front')->get();
	 *
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param string $meta_compare
	 * @param string $meta_relation
	 * @return $this
	 */
	protected function where(
		$meta_key,
		$meta_value,
		$meta_compare = '=',
		$meta_relation = 'AND'
	) {
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
	 * Get pages with by a certain meta query.
	 * The function accepts a strtotime() compatible string (ignores time values, only for dates)
	 * or an array containing any of the following [year, month, day, hour, minute, second]
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#date-parameters
	 * @see https://developer.wordpress.org/reference/functions/date_i18n/
	 *
	 * @example ExamplePost::whereDate(['year' => 2016)->get();
	 * @example ExamplePost::whereDate('1-1-2017', 'after', true)->get();
	 *
	 * @param string | array $date_value
	 * @param string $date_compare
	 * @param bool $date_inclusive
	 * @param string $meta_relation
	 * @return $this
	 */
	protected function whereDate(
		$date_value,
		$date_compare = null,
		$date_inclusive = false,
		$meta_relation = 'AND'
	) {
		// Check if there already is a query. If not, create a relation field first
		if (!isset($this->args['date_query'])) {
			$this->args['date_query'] = [
				'relation' => $meta_relation
			];
		}

		if (!is_array($date_value)) {
			$timestamp = strtotime($date_value);
			$date_value = [
				'year'  => date_i18n('Y', $timestamp),
				'month' => date_i18n('m', $timestamp),
				'day'   => date_i18n('d', $timestamp),
			];
		}

		if ($date_compare !== null) {
			$date_value = [
				$date_compare => $date_value,
				'inclusive'   => $date_inclusive
			];
		}

		// Create a new query argument
		$this->args['date_query'][] = [
			$date_value,
		];

		return $this;
	}

	/**
	 * Get pages with by a certain meta query.
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#taxonomy-parameters
	 *
	 * @example ExamplePost::whereTax(EXAMPLE_TAXONOMY, 1)->get();
	 * @example ExamplePost::whereTax(EXAMPLE_TAXONOMY, 1, 'term_id', IN', 'OR')->whereTax('spotlight', 'front')->get();
	 *
	 * @param $tax_key
	 * @param $tax_terms
	 * @param string $tax_compare
	 * @return $this
	 */
	protected function whereTax(
		$tax_key,
		$tax_terms,
		$tax_compare = '=',
		$tax_fields = 'term_id',
		$tax_relation = 'AND'
	) {
		// Check if there already is a query. If not, create a relation field first
		if (!isset($this->args['tax_query'])) {
			$this->args['tax_query'] = [
				'relation' => $tax_relation
			];
		}

		// Check if this key has already been set. If so, overwrite it.
		foreach ($this->args['tax_query'] as $key => $term) {
			if (is_array($term) && in_array($tax_key, $term)) {
				unset($this->args['tax_query'][$key]);
			}
		}

		// Create a new query argument
		$this->args['tax_query'][] = [
			'taxonomy' => $tax_key,
			'field'    => $tax_fields,
			'terms'    => $tax_terms,
			'compare'  => $tax_compare
		];

		return $this;
	}

	/**
	 * Parse our query and execute all the functions to make our content super fancy
	 *
	 * @see Post::runquery();
	 * @see Post::appendAcfFields();
	 * @see Post::appendContent();
	 * @see Post::appendExcerpt();
	 * @see Post::appendPermalink();
	 *
	 * @example ExamplePost::take(10)->get();
	 *
	 * @return array
	 */
	public function get()
	{
		$this->runQuery()
			->appendAcfFields()
			->appendContent()
			->appendExcerpt()
			->appendPermalink();

		return $this->query->posts;
	}

	/**
	 * Get the first result of our Query
	 *
	 * @see Post::take();
	 * @see Post::get();
	 *
	 * @example ExamplePost::id(1)->first();
	 *
	 * @return \WP_Post
	 */
	public function first()
	{
		$this->take(1)
			->get();

		if ($this->query->posts) {
			return $this->query->posts[0];
		}

		return false;
	}

	/**
	 * Parse our query and execute all the functions to make our content super fancy
	 * and return it paginated
	 *
	 * @see Post::paged();
	 * @see Post::runquery();
	 * @see Post::appendAcfFields();
	 * @see Post::appendContent();
	 * @see Post::appendExcerpt();
	 * @see Post::appendPermalink();
	 * @see Post::appendPagination();
	 *
	 * @example ExamplePost::where('active', 1)->paginate();
	 *
	 * @return mixed
	 */
	public function paginate()
	{
		$this->paged()
			->runQuery()
			->appendAcfFields()
			->appendContent()
			->appendExcerpt()
			->appendPermalink()
			->appendPagination();

		return $this->results;
	}

	/**
	 * Count the results of our Query
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/#properties
	 * @see Post::runQuery();
	 *
	 * @example ExamplePost::where('active', 1)->count();
	 *
	 * @return array
	 */
	public function count()
	{
		if (!isset($this->query->found_posts)) {
			$this->runQuery();
		}

		return $this->query->found_posts;
	}

	/**
	 * Get all the ACF fields that are related to our posts
	 *
	 * @see https://www.advancedcustomfields.com/resources/get_fields/
	 *
	 * @return $this
	 */
	private function appendAcfFields()
	{
		if (function_exists('get_fields')) {
			foreach ($this->query->posts as $post) {
				if ($post instanceof WP_Post) {
					$post->fields = get_fields($post->ID);
				}
			}
		}

		return $this;
	}

	/**
	 * Make our actual content fancy!
	 *
	 * @see https://developer.wordpress.org/reference/functions/apply_filters/
	 *
	 * @return $this
	 */
	private function appendContent()
	{
		foreach ($this->query->posts as $post) {
			if ($post instanceof WP_Post) {
				$post->post_content = apply_filters('the_content', $post->post_content);
			}
		}

		return $this;
	}

	/**
	 * Make our excerpt fancy!
	 *
	 * @see https://developer.wordpress.org/reference/functions/strip_shortcodes/
	 * @see https://developer.wordpress.org/reference/functions/apply_filters/
	 * @see https://developer.wordpress.org/reference/functions/wpautop/
	 *
	 * @return $this
	 */
	private function appendExcerpt()
	{
		foreach ($this->query->posts as $post) {

			if ($post instanceof WP_Post) {
				if ($post->post_excerpt === '') {
					$post->post_excerpt = strip_shortcodes($post->post_content);
					$post->post_excerpt = apply_filters('the_content', $post->post_excerpt);
					$post->post_excerpt = substr(strip_tags($post->post_excerpt), 0,
						Config::get('theme.excerpt-length') ?: 120);
				}

				$post->post_excerpt = wpautop($post->post_excerpt); //Used because we always want <p> tags around the excerpt
			}
		}

		return $this;
	}

	/**
	 * Return the WP-Pagenavi navigation / Default WordPress Paginate links.
	 *
	 * @see https://github.com/lesterchan/wp-pagenavi
	 * @see https://developer.wordpress.org/reference/functions/paginate_links/
	 *
	 * @return $this
	 */
	private function appendPagination()
	{
		if (!function_exists('wp_pagenavi')) {
			$this->results['pagination'] = wp_pagenavi([
				'query'         => $this->query,
				'echo'          => false,
				'wrapper_tag'   => 'nav',
				'wrapper_class' => 'pagination'
			]);
		} else {
			$this->results['pagination'] = paginate_links();
		}

		$this->results['posts'] = $this->query->posts;

		return $this;
	}

	/**
	 * Add the permalink to our query result
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_the_permalink/
	 *
	 * @return $this
	 */
	private function appendPermalink()
	{
		foreach ($this->query->posts as $post) {
			if ($post instanceof WP_Post) {
				$post->permalink = get_the_permalink($post->ID);
			}
		}

		return $this;
	}

	/**
	 * Create a new query
	 *
	 * @see https://developer.wordpress.org/reference/classes/wp_query/
	 * @see https://www.relevanssi.com/user-manual/functions/#relevanssi_do_query
	 * @see WP_QUERY
	 *
	 * @return $this
	 */
	private function runQuery()
	{
		if (!isset($this->query->posts)) {
			$this->query = new WP_Query($this->args);
		}

		/*
		 * Execute the revelanssi query function if relavanssi is active.
		 */
		if (isset($this->args['s']) && function_exists('relevanssi_do_query')) {
			relevanssi_do_query($this->query);
		}

		return $this;
	}
}