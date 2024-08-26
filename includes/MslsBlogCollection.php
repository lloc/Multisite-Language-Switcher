<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Collection of blog-objects
 *
 * @package Msls
 */
class MslsBlogCollection extends MslsRegistryInstance {

	/**
	 * ID of the current blog
	 *
	 * @var int
	 */
	private int $current_blog_id;

	/**
	 * True if the current blog should be in the output
	 *
	 * @var bool
	 */
	private bool $current_blog_output;

	/**
	 * Collection of MslsBlog-objects
	 *
	 * @var MslsBlog[]
	 */
	private array $objects = array();

	/**
	 * Order output by language or description
	 *
	 * @var string
	 */
	private string $objects_order;

	/**
	 * Active plugins in the whole network
	 *
	 * @var string[]
	 */
	private ?array $active_plugins = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! has_filter( 'msls_blog_collection_description' ) ) {
			add_filter( 'msls_blog_collection_description', array( $this, 'get_configured_blog_description' ), 10, 2 );
		}

		$this->current_blog_id = get_current_blog_id();

		$options = msls_options();

		$this->current_blog_output = isset( $options->output_current_blog );
		$this->objects_order       = $options->get_order();

		if ( ! $options->is_excluded() ) {
			/**
			 * Returns custom filtered blogs of the blogs_collection
			 *
			 * @param array $blogs_collection
			 *
			 * @since 0.9.8
			 */
			$blogs_collection = (array) apply_filters(
				'msls_blog_collection_construct',
				$this->get_blogs_of_reference_user( $options )
			);

			foreach ( $blogs_collection as $blog ) {
				$description = false;
				if ( $blog->userblog_id == $this->current_blog_id ) {
					$description = $options->description;
				} elseif ( ! $this->is_plugin_active( $blog->userblog_id ) ) {
					continue;
				}

				$description = apply_filters(
					'msls_blog_collection_description',
					$blog->userblog_id,
					$description
				);

				if ( false !== $description ) {
					$this->objects[ $blog->userblog_id ] = new MslsBlog( $blog, $description );
				}
			}

			uasort( $this->objects, array( MslsBlog::class, $this->objects_order ) );
		}
	}

	/**
	 * Returns the description of a configured blog or false if it is not configured
	 *
	 * @param int         $blog_id
	 * @param string|bool $description
	 *
	 * @return string|bool
	 */
	public static function get_configured_blog_description( int $blog_id, $description = false ) {
		if ( $description ) {
			return $description;
		}

		$temp = get_blog_option( $blog_id, 'msls' );
		if ( is_array( $temp ) && empty( $temp['exclude_current_blog'] ) ) {
			return $temp['description'] ?? '';
		}

		return false;
	}

	/**
	 * Gets the list of the blogs of the reference user
	 * The first available user of the blog will be used if there is no
	 * refrence user configured
	 *
	 * @param MslsOptions $options
	 *
	 * @return object[]|\stdClass[]
	 */
	public function get_blogs_of_reference_user( MslsOptions $options ) {
		$reference_user = $options->has_value( 'reference_user' ) ? $options->reference_user : current(
			$this->get_users(
				'ID',
				1
			)
		);

		$blogs = get_blogs_of_user( $reference_user );

		/**
		 * @todo Check if this is still useful
		 */
		if ( is_array( $blogs ) ) {
			foreach ( $blogs as $key => $blog ) {
				$blogs[ $key ]->blog_id = $blog->userblog_id;
			}
		}

		return $blogs;
	}

	/**
	 * Get blog by language
	 *
	 * @param string $language
	 *
	 * @return MslsBlog|null
	 */
	public function get_blog( $language ) {
		$blog = null;

		foreach ( $this->get_objects() as $item ) {
			if ( $language == $item->get_language() ) {
				$blog = $item;
				break;
			}
		}

		return apply_filters( 'msls_blog_collection_get_blog', $blog, $language );
	}

	/**
	 * Gets blog_id by language
	 *
	 * @param string $language
	 *
	 * @return ?int
	 */
	public function get_blog_id( string $language ): ?int {
		$blog    = $this->get_blog( $language );
		$blog_id = ! is_null( $blog ) ? $blog->userblog_id : null;

		return apply_filters( 'msls_blog_collection_get_blog_id', $blog_id, $language );
	}

	/**
	 * Get the id of the current blog
	 *
	 * @return int
	 */
	public function get_current_blog_id() {
		return $this->current_blog_id;
	}

	/**
	 * Checks if blog is the current blog
	 *
	 * @param MslsBlog $blog
	 *
	 * @return bool
	 */
	public function is_current_blog( MslsBlog $blog ) {
		return $blog->userblog_id === $this->get_current_blog_id();
	}

	/**
	 * Checks if current blog is in the collection
	 *
	 * @return bool
	 */
	public function has_current_blog() {
		return isset( $this->objects[ $this->get_current_blog_id() ] );
	}

	/**
	 * Gets current blog as object
	 *
	 * @return MslsBlog|null
	 */
	public function get_current_blog() {
		return $this->has_current_blog() ? $this->objects[ $this->get_current_blog_id() ] : null;
	}

	/**
	 * Gets an array with all blog-objects
	 *
	 * @return MslsBlog[]
	 */
	public function get_objects(): array {
		return apply_filters( 'msls_blog_collection_get_objects', $this->objects );
	}

	/**
	 * Gets a specific blog-object
	 *
	 * @param int $blog_id
	 *
	 * @return ?MslsBlog
	 */
	public function get_object( int $blog_id ): ?MslsBlog {
		return $this->get_objects()[ $blog_id ] ?? null;
	}

	/**
	 * Is plugin active in the blog with that blog_id
	 *
	 * @param int $blog_id
	 *
	 * @return bool
	 */
	public function is_plugin_active( $blog_id ) {
		if ( is_null( $this->active_plugins ) ) {
			$this->active_plugins = get_site_option( 'active_sitewide_plugins', array() );
		}

		$path = MslsPlugin::path();
		if ( isset( $this->active_plugins[ $path ] ) ) {
			return true;
		}

		$plugins = get_blog_option( $blog_id, 'active_plugins', array() );

		return in_array( $path, $plugins );
	}

	/**
	 * Gets only blogs where the plugin is active
	 *
	 * @return MslsBlog[]
	 */
	public function get_plugin_active_blogs() {
		$arr = array();

		foreach ( $this->get_objects() as $blog ) {
			if ( $this->is_plugin_active( $blog->userblog_id ) ) {
				$arr[] = $blog;
			}
		}

		return $arr;
	}

	/**
	 * Gets an array of all - but not the current - blog-objects
	 *
	 * @return MslsBlog[]
	 */
	public function get() {
		$objects = $this->get_objects();

		if ( $this->has_current_blog() ) {
			unset( $objects[ $this->current_blog_id ] );
		}

		return apply_filters( 'msls_blog_collection_get', $objects );
	}

	/**
	 * Gets an array with filtered blog-objects
	 *
	 * @param bool $filter
	 *
	 * @return MslsBlog[]
	 */
	public function get_filtered( bool $filter = false ): array {
		return ! $filter && $this->current_blog_output ? $this->get_objects() : $this->get();
	}

	/**
	 * Gets the registered users of the current blog
	 *
	 * @param string     $fields
	 * @param int|string $number
	 *
	 * @return array<string, int>
	 */
	public function get_users( $fields = 'all', $number = '' ) {
		$args = array(
			'blog_id'     => $this->current_blog_id,
			'orderby'     => 'registered',
			'fields'      => $fields,
			'number'      => $number,
			'count_total' => false,
		);

		$args = (array) apply_filters( 'msls_get_users', $args );

		return get_users( $args );
	}

	/**
	 * Returns a specific blog language.
	 *
	 * @param int    $blog_id
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_blog_language( $blog_id = null, $default = 'en_US' ) {
		if ( null === $blog_id ) {
			$blog_id = get_current_blog_id();
		}

		$language = (string) get_blog_option( $blog_id, 'WPLANG' );

		return '' !== $language ? $language : $default;
	}
}
