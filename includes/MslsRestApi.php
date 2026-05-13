<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Options\OptionsPost;
use lloc\Msls\Options\OptionsTax;
use lloc\Msls\Query\TranslatedPostIdQuery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API endpoint for quick-creating translations.
 *
 * @package Msls
 */
class MslsRestApi {

	const NAMESPACE = 'msls/v1';

	const ROUTE = '/create-translation';

	const ROUTE_UNTRANSLATED = '/untranslated-posts';

	const UNTRANSLATED_POSTS_LIMIT = 100;

	const UNTRANSLATED_POST_STATUSES = array( 'publish', 'draft', 'pending', 'future' );

	const LAST_SOURCE_USER_META = 'msls_translation_picker_last_source';

	/**
	 * Registers the REST API route.
	 */
	public static function init(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( new self(), 'create_translation' ),
				'permission_callback' => array( new self(), 'check_permission' ),
				'args'                => self::get_route_args(),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			self::ROUTE_UNTRANSLATED,
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( new self(), 'list_untranslated_posts' ),
				'permission_callback' => array( new self(), 'check_list_permission' ),
				'args'                => self::get_list_route_args(),
			)
		);

		add_filter( 'msls_quick_create_post_data', array( self::class, 'prefix_source_language' ), 10, 4 );
		add_action( 'msls_quick_create_after_insert', array( self::class, 'remember_source_blog' ), 10, 3 );
	}

	/**
	 * Remembers the source blog a user last used when creating a
	 * translation, so the picker can pre-select it next time.
	 *
	 * Hooked to msls_quick_create_after_insert so it only records on
	 * successful creates, not on modal-open/cancel.
	 *
	 * @param int      $new_post_id
	 * @param \WP_Post $source_post
	 * @param int      $source_blog_id
	 */
	public static function remember_source_blog( int $new_post_id, \WP_Post $source_post, int $source_blog_id ): void {
		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			return;
		}

		update_user_meta( $user_id, self::LAST_SOURCE_USER_META, $source_blog_id );
	}

	/**
	 * Returns the source blog id the current user last picked, or 0.
	 *
	 * @return int
	 */
	public static function get_last_source_blog_id(): int {
		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			return 0;
		}

		return (int) get_user_meta( $user_id, self::LAST_SOURCE_USER_META, true );
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_route_args(): array {
		return array(
			'source_post_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'source_blog_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'target_blog_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
		);
	}

	/**
	 * Argument schema for the GET /untranslated-posts endpoint.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_list_route_args(): array {
		return array(
			'source_blog_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'target_blog_id' => array(
				'required'          => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'post_type'      => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
			),
			'search'         => array(
				'required'          => false,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			),
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function check_permission( \WP_REST_Request $request ): bool {
		$source_blog_id = (int) $request->get_param( 'source_blog_id' );
		$source_post_id = (int) $request->get_param( 'source_post_id' );
		$target_blog_id = (int) $request->get_param( 'target_blog_id' );

		if ( ! self::user_can_read_source( $source_post_id, $source_blog_id, $target_blog_id ) ) {
			return false;
		}

		return self::user_can_create_target( $source_post_id, $source_blog_id, $target_blog_id );
	}

	/**
	 * Evaluates the read capability on the source blog, with filter override.
	 *
	 * @param int $source_post_id
	 * @param int $source_blog_id
	 * @param int $target_blog_id
	 *
	 * @return bool
	 */
	public static function user_can_read_source( int $source_post_id, int $source_blog_id, int $target_blog_id ): bool {
		switch_to_blog( $source_blog_id );
		$default = current_user_can( 'read_post', $source_post_id );
		restore_current_blog();

		return self::apply_capability_filter( $default, $source_post_id, $source_blog_id, $target_blog_id, 'read' );
	}

	/**
	 * Evaluates the create capability on the target blog, with filter override.
	 *
	 * @param int $source_post_id
	 * @param int $source_blog_id
	 * @param int $target_blog_id
	 *
	 * @return bool
	 */
	public static function user_can_create_target( int $source_post_id, int $source_blog_id, int $target_blog_id ): bool {
		switch_to_blog( $target_blog_id );
		$default = current_user_can( 'edit_posts' );
		restore_current_blog();

		return self::apply_capability_filter( $default, $source_post_id, $source_blog_id, $target_blog_id, 'create' );
	}

	/**
	 * Permission callback for the untranslated-posts listing endpoint.
	 *
	 * Runs the same capability filter as the create endpoint but with no
	 * specific source post id (0) and with a generic 'read' capability
	 * default on the source blog, since no single post is being targeted.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function check_list_permission( \WP_REST_Request $request ): bool {
		$source_blog_id = (int) $request->get_param( 'source_blog_id' );
		$target_blog_id = (int) $request->get_param( 'target_blog_id' );

		switch_to_blog( $source_blog_id );
		$default_read = current_user_can( 'read' );
		restore_current_blog();

		if ( ! self::apply_capability_filter( $default_read, 0, $source_blog_id, $target_blog_id, 'read' ) ) {
			return false;
		}

		return self::user_can_create_target( 0, $source_blog_id, $target_blog_id );
	}

	/**
	 * Routes the default capability decision through the
	 * msls_quick_create_capability filter so integrations can override it.
	 *
	 * @param bool   $default        Result of the default capability check.
	 * @param int    $source_post_id Source post id, or 0 for list-style checks.
	 * @param int    $source_blog_id
	 * @param int    $target_blog_id
	 * @param string $context        'read' for source-side checks, 'create' for target-side.
	 *
	 * @return bool
	 */
	private static function apply_capability_filter( bool $default, int $source_post_id, int $source_blog_id, int $target_blog_id, string $context ): bool {
		/**
		 * Filters the result of the Quick Create capability check.
		 *
		 * Lets integrations override the default read/edit checks, for
		 * example to permit a translator without an account on the source
		 * blog to mirror a post into the target blog.
		 *
		 * @param bool   $default        Result of the default capability check.
		 * @param int    $source_post_id Source post ID (0 for list-style checks).
		 * @param int    $source_blog_id Source blog ID.
		 * @param int    $target_blog_id Target blog ID.
		 * @param string $context        'read' when checking the source, 'create' when checking the target.
		 *
		 * @since TBD
		 */
		return (bool) apply_filters(
			'msls_quick_create_capability',
			$default,
			$source_post_id,
			$source_blog_id,
			$target_blog_id,
			$context
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_translation( \WP_REST_Request $request ) {
		$source_post_id = (int) $request->get_param( 'source_post_id' );
		$source_blog_id = (int) $request->get_param( 'source_blog_id' );
		$target_blog_id = (int) $request->get_param( 'target_blog_id' );

		switch_to_blog( $source_blog_id );
		$source_post = get_post( $source_post_id );
		restore_current_blog();

		if ( ! $source_post instanceof \WP_Post ) {
			return new \WP_Error(
				'msls_source_not_found',
				__( 'Source post not found.', 'multisite-language-switcher' ),
				array( 'status' => 404 )
			);
		}

		$target_lang = MslsBlogCollection::get_blog_language( $target_blog_id );

		$post_data = $this->prepare_post_data( $source_post );
		$post_data = $this->prepare_taxonomies( $source_post, $source_blog_id, $target_blog_id, $target_lang, $post_data );

		/**
		 * Filters the post data before creating the translation.
		 *
		 * @param array    $post_data      The post data for wp_insert_post.
		 * @param \WP_Post $source_post    The source post object.
		 * @param int      $source_blog_id The source blog ID.
		 * @param int      $target_blog_id The target blog ID.
		 *
		 * @since TBD
		 */
		$post_data = apply_filters( 'msls_quick_create_post_data', $post_data, $source_post, $source_blog_id, $target_blog_id );

		switch_to_blog( $target_blog_id );

		if ( ! post_type_exists( $post_data['post_type'] ) ) {
			restore_current_blog();

			return new \WP_Error(
				'msls_target_post_type_not_found',
				__( 'Post type does not exist on the target blog.', 'multisite-language-switcher' ),
				array( 'status' => 400 )
			);
		}

		$new_post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $new_post_id ) ) {
			restore_current_blog();

			return $new_post_id;
		}

		$this->assign_taxonomies( $post_data, $new_post_id );

		/**
		 * Fires after the translation post is created on the target blog.
		 *
		 * @param int      $new_post_id    The new post ID.
		 * @param \WP_Post $source_post    The source post object.
		 * @param int      $source_blog_id The source blog ID.
		 * @param int      $target_blog_id The target blog ID.
		 *
		 * @since TBD
		 */
		do_action( 'msls_quick_create_after_insert', $new_post_id, $source_post, $source_blog_id, $target_blog_id );

		$edit_url   = get_edit_post_link( $new_post_id, 'raw' );
		$post_title = get_the_title( $new_post_id );
		restore_current_blog();

		$this->establish_link( $source_post_id, $source_blog_id, $new_post_id, $target_blog_id );

		$response_data = array(
			'post_id'    => $new_post_id,
			'edit_url'   => $edit_url,
			'post_title' => $post_title,
		);

		/**
		 * Filters the REST response data after creating a translation.
		 *
		 * @param array    $response_data  The response data.
		 * @param int      $new_post_id    The new post ID.
		 * @param \WP_Post $source_post    The source post object.
		 * @param int      $source_blog_id The source blog ID.
		 * @param int      $target_blog_id The target blog ID.
		 *
		 * @since TBD
		 */
		$response_data = apply_filters(
			'msls_quick_create_response',
			$response_data,
			$new_post_id,
			$source_post,
			$source_blog_id,
			$target_blog_id
		);

		return new \WP_REST_Response( $response_data, 201 );
	}

	/**
	 * Lists source-blog posts of a given type that have no translation
	 * in the target blog yet.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function list_untranslated_posts( \WP_REST_Request $request ) {
		$source_blog_id = (int) $request->get_param( 'source_blog_id' );
		$target_blog_id = (int) $request->get_param( 'target_blog_id' );
		$post_type      = (string) $request->get_param( 'post_type' );
		$search         = (string) $request->get_param( 'search' );

		$target_lang = MslsBlogCollection::get_blog_language( $target_blog_id );

		switch_to_blog( $source_blog_id );

		if ( ! post_type_exists( $post_type ) ) {
			restore_current_blog();

			return new \WP_Error(
				'msls_source_post_type_not_found',
				__( 'Post type does not exist on the source blog.', 'multisite-language-switcher' ),
				array( 'status' => 400 )
			);
		}

		$translated_ids = ( new TranslatedPostIdQuery( MslsSqlCacher::create( __CLASS__, __METHOD__ ) ) )( $target_lang );

		$query_args = array(
			'post_type'        => $post_type,
			'post_status'      => self::UNTRANSLATED_POST_STATUSES,
			'numberposts'      => self::UNTRANSLATED_POSTS_LIMIT,
			'post__not_in'     => $translated_ids,
			'suppress_filters' => false,
			'orderby'          => 'date',
			'order'            => 'DESC',
		);

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		$posts = get_posts( $query_args );

		$items = array();
		foreach ( $posts as $post ) {
			$items[] = array(
				'id'          => (int) $post->ID,
				'title'       => get_the_title( $post ),
				'post_status' => $post->post_status,
				'date_gmt'    => mysql2date( 'Y-m-d\TH:i:s', $post->post_date_gmt, false ),
				'view_url'    => (string) get_permalink( $post ),
			);
		}

		restore_current_blog();

		/**
		 * Filters the untranslated-posts listing response.
		 *
		 * @param array<int, array<string, mixed>> $items          Listing items.
		 * @param int                              $source_blog_id Source blog ID.
		 * @param int                              $target_blog_id Target blog ID.
		 * @param string                           $post_type      Post type queried.
		 *
		 * @since TBD
		 */
		$items = apply_filters( 'msls_untranslated_posts', $items, $source_blog_id, $target_blog_id, $post_type );

		return new \WP_REST_Response(
			array(
				'items' => $items,
				'total' => count( $items ),
			),
			200
		);
	}

	/**
	 * @param \WP_Post $source_post
	 *
	 * @return array<string, mixed>
	 */
	protected function prepare_post_data( \WP_Post $source_post ): array {
		return array(
			'post_type'    => $source_post->post_type,
			'post_status'  => 'draft',
			'post_title'   => $source_post->post_title,
			'post_content' => $source_post->post_content,
		);
	}

	/**
	 * Prefixes post title and content with the source language code.
	 *
	 * Registered as a filter callback on msls_quick_create_post_data.
	 * Can be removed via remove_filter() to disable the prefix.
	 *
	 * @param array<string, mixed> $post_data
	 * @param \WP_Post             $source_post
	 * @param int                  $source_blog_id
	 * @param int                  $target_blog_id
	 *
	 * @return array<string, mixed>
	 */
	public static function prefix_source_language( array $post_data, \WP_Post $source_post, int $source_blog_id, int $target_blog_id ): array {
		$lang_code = substr( MslsBlogCollection::get_blog_language( $source_blog_id ), 0, 2 );

		$post_data['post_title'] = sprintf(
			/* translators: 1: language code, 2: original post title */
			__( 'From %1$s: %2$s', 'multisite-language-switcher' ),
			$lang_code,
			$post_data['post_title']
		);

		return $post_data;
	}

	/**
	 * @param \WP_Post             $source_post
	 * @param int                  $source_blog_id
	 * @param int                  $target_blog_id
	 * @param string               $target_lang
	 * @param array<string, mixed> $post_data
	 *
	 * @return array<string, mixed>
	 */
	protected function prepare_taxonomies(
		\WP_Post $source_post,
		int $source_blog_id,
		int $target_blog_id,
		string $target_lang,
		array $post_data
	): array {
		switch_to_blog( $source_blog_id );

		$taxonomies = get_object_taxonomies( $source_post->post_type );
		$tax_input  = array();

		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $source_post->ID, $taxonomy, array( 'fields' => 'ids' ) );

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			$mapped_terms = array();
			foreach ( $terms as $term_id ) {
				/** @var OptionsTax $term_options */
				$term_options = OptionsTax::create( $term_id );

				if ( $term_options->has_value( $target_lang ) ) {
					$mapped_terms[] = (int) $term_options->$target_lang;
				}
			}

			if ( ! empty( $mapped_terms ) ) {
				$tax_input[ $taxonomy ] = $mapped_terms;
			}
		}

		restore_current_blog();

		$post_data['_msls_tax_input'] = $tax_input;

		/**
		 * Filters the mapped taxonomy terms before assigning them.
		 *
		 * @param array<string, int[]> $tax_input       Taxonomy => term IDs mapping.
		 * @param \WP_Post             $source_post     The source post object.
		 * @param int                  $source_blog_id  The source blog ID.
		 * @param int                  $target_blog_id  The target blog ID.
		 *
		 * @since TBD
		 */
		$post_data['_msls_tax_input'] = apply_filters(
			'msls_quick_create_tax_input',
			$post_data['_msls_tax_input'],
			$source_post,
			$source_blog_id,
			$target_blog_id
		);

		return $post_data;
	}

	/**
	 * @param array<string, mixed> $post_data
	 * @param int                  $new_post_id
	 */
	protected function assign_taxonomies( array $post_data, int $new_post_id ): void {
		if ( empty( $post_data['_msls_tax_input'] ) ) {
			return;
		}

		foreach ( $post_data['_msls_tax_input'] as $taxonomy => $term_ids ) {
			wp_set_object_terms( $new_post_id, $term_ids, $taxonomy );
		}
	}

	/**
	 * @param int $source_post_id
	 * @param int $source_blog_id
	 * @param int $new_post_id
	 * @param int $target_blog_id
	 */
	protected function establish_link(
		int $source_post_id,
		int $source_blog_id,
		int $new_post_id,
		int $target_blog_id
	): void {
		$collection  = msls_blog_collection();
		$source_lang = MslsBlogCollection::get_blog_language( $source_blog_id );
		$target_lang = MslsBlogCollection::get_blog_language( $target_blog_id );

		// Read existing links from the source post
		switch_to_blog( $source_blog_id );
		$source_options = new OptionsPost( $source_post_id );
		$existing_links = $source_options->get_arr();
		restore_current_blog();

		// Build a complete link map: all existing links + source + target
		$link_map                 = $existing_links;
		$link_map[ $source_lang ] = $source_post_id;
		$link_map[ $target_lang ] = $new_post_id;

		// Update every blog in the link map
		foreach ( $link_map as $lang => $post_id ) {
			if ( empty( $post_id ) ) {
				continue;
			}

			$blog_id = $collection->get_blog_id( $lang );

			if ( null === $blog_id ) {
				continue;
			}

			switch_to_blog( $blog_id );

			$options   = new OptionsPost( $post_id );
			$save_data = $link_map;
			unset( $save_data[ $lang ] );

			$options->save( $save_data );

			restore_current_blog();
		}
	}
}
