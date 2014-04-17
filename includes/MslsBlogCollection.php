<?php
/**
 * MslsBlogCollection
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Collection of blog-objects
 * 
 * Implements the interface IMslsRegistryInstance because we want to 
 * work with a singleton instance of MslsBlogCollection all the time.
 * @package Msls
 */
class MslsBlogCollection implements IMslsRegistryInstance {

	/**
	 * ID of the current blog
	 * @var int
	 */
	private $current_blog_id;

	/**
	 * True if the current blog should be in the output
	 * @var bool
	 */
	private $current_blog_output;

	/**
	 * Collection of MslsBlog-objects
	 * @var array
	 */
	private $objects = array();

	/**
	 * Order output by language or description
	 * @var string
	 */
	private $objects_order;

	/**
	 * Active plugins in the whole network
	 * @var array
	 */
	private $active_plugins;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->current_blog_id     = get_current_blog_id();
		
		$options = MslsOptions::instance();

		$this->current_blog_output = isset( $options->output_current_blog );
		$this->objects_order       = $options->get_order();

		if ( ! $options->is_excluded() ) {
			if ( has_filter( 'msls_blog_collection_construct' ) ) {
				/**
				 * Returns custom filtered blogs of the blogs_collection
				 * @since 0.9.8
				 * @param array $blogs_collection
				 */
				$blogs_collection = (array) apply_filters(
					'msls_blog_collection_construct',
					$blogs_collection
				);
			}
			else {
				$reference_user   = (
					$options->has_value( 'reference_user' ) ?
					$options->reference_user :
					current( $this->get_users( 'ID', 1 ) )
				);
				$blogs_collection = get_blogs_of_user( $reference_user );
			}
			foreach ( $blogs_collection as $blog ) {
				/*
				 * get_user_id_from_string returns objects with userblog_id-members 
				 * instead of a blog_id ... so we need just some correction ;)
				 *
				 */
				if ( ! isset( $blog->userblog_id ) && isset( $blog->blog_id) ) {
					$blog->userblog_id = $blog->blog_id;
				}
				if ( $blog->userblog_id != $this->current_blog_id ) {
					$temp = get_blog_option( $blog->userblog_id, 'msls' );
					if ( is_array( $temp ) && empty( $temp['exclude_current_blog'] ) && $this->is_plugin_active( $blog->userblog_id ) )
						$this->objects[$blog->userblog_id] = new MslsBlog(
							$blog,
							$temp['description']
						);
				}
				else {
					$this->objects[$this->current_blog_id] = new MslsBlog(
						$blog,
						$options->description
					);
				}
			}
			uasort( $this->objects, array( 'MslsBlog', $this->objects_order ) );
		}
	}

	/**
	 * Get the id of the current blog
	 * @return int
	 */
	public function get_current_blog_id() {
		return $this->current_blog_id;
	}

	/**
	 * Check if current blog is in the collection
	 *
	 * @return bool
	 */
	public function has_current_blog() {
		return( isset( $this->objects[$this->current_blog_id] ) );
	}

	/**
	 * Get current blog as object
	 * @return MslsBlog|null
	 */
	public function get_current_blog() {
		return(
			$this->has_current_blog() ?
			$this->objects[$this->current_blog_id] :
			null
		);
	}

	/**
	 * Get an array with all blog-objects
	 * @return array
	 */
	public function get_objects() {
		return $this->objects;
	}

	/**
	 * Is plugin active in the blog with that blog_id 
	 * @param int $blog_id
	 * @return bool
	 */
	function is_plugin_active( $blog_id ) {
		if ( ! is_array( $this->active_plugins ) ) {
			$this->active_plugins = get_site_option( 'active_sitewide_plugins', array() );
		}
		if ( isset( $this->active_plugins[MSLS_PLUGIN_PATH] ) ) {
			return true;
		}
		$plugins = get_blog_option( $blog_id, 'active_plugins', array() );
		return( in_array( MSLS_PLUGIN_PATH, $plugins ) );
	}

	/**
	 * Get an array of all - but not the current - blog-objects
	 * @return array
	 */
	public function get() {
		$objects = $this->get_objects();
		if ( $this->has_current_blog() ) {
			unset( $objects[$this->current_blog_id] );
		}
		return $objects;
	}

	/**
	 * Get an array with filtered blog-objects
	 * @param bool $filter
	 * @return array
	 */
	public function get_filtered( $filter = false ) {
		if ( ! $filter && $this->current_blog_output ) {
			return $this->get_objects();
		}
		return $this->get();
	}

	/**
	 * Get the registered users of the current blog
	 * @param string $fields
	 * @param mixed $number
	 * @return WP_User|Array
	 */
	public function get_users( $fields = 'all', $number = '' ) {
		$args = array(
			'blog_id' => $this->current_blog_id,
			'orderby' => 'registered',
			'fields'  => $fields,
			'number'  => $number,
		);
		return get_users( $args );
	}

	/**
	 * Get or create a instance of MslsBlogCollection
	 * - until PHP 5.2 is not longer the minimum for WordPress -
	 * @return MslsBlogCollection
	 */
	static function instance() {
		$registry = MslsRegistry::singleton();
		$cls      = __CLASS__;
		$obj      = $registry->get_object( $cls );
		if ( is_null( $obj ) ) {
			$obj = new $cls;
			$registry->set_object( $cls, $obj );
		}
		return $obj;
	}

}
