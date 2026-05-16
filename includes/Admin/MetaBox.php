<?php declare( strict_types=1 );

namespace lloc\Msls\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use lloc\Msls\Blog\Collection;
use lloc\Msls\Component\Component;
use lloc\Msls\Component\Wrapper;
use lloc\Msls\ContentImport\MetaBox as ContentImportMetaBox;
use lloc\Msls\Data\Json;
use lloc\Msls\Options\Post\Post;
use lloc\Msls\Plugin;
use lloc\Msls\Request\Fields;
use lloc\Msls\RestApi\Request;
use WP_Post_Type;

/**
 * Meta box for the edit mode of the (custom) post types
 *
 * @package Msls
 */
final class MetaBox extends Main {

	public static function init(): void {
		$options = msls_options();
		$obj     = new self( $options, msls_blog_collection() );

		if ( ! $options->is_excluded() ) {
			add_action( 'add_meta_boxes', array( $obj, 'add' ) );
			add_action( 'save_post', array( $obj, 'set' ) );
			add_action( 'trashed_post', array( $obj, 'delete' ) );
		}
	}

	/**
	 * Suggest
	 *
	 * Echo a JSON-ified array of posts of the given post-type and
	 * the requested search-term and then die silently
	 */
	public static function suggest(): void {
		$json = new Json();

		if ( Request::has_var( Fields::FIELD_BLOG_ID, INPUT_POST ) ) {
			switch_to_blog( Request::get_var( Fields::FIELD_BLOG_ID, INPUT_POST ) );

			$args = array(
				'post_status'    => get_post_stati( array( 'internal' => '' ) ),
				'posts_per_page' => 10,
			);

			if ( Request::has_var( Fields::FIELD_POST_TYPE, INPUT_POST ) ) {
				$args['post_type'] = sanitize_text_field(
					Request::get_var( Fields::FIELD_POST_TYPE, INPUT_POST )
				);
			}

			if ( Request::has_var( Fields::FIELD_S, INPUT_POST ) ) {
				$value_s = sanitize_text_field(
					Request::get_var( Fields::FIELD_S, INPUT_POST )
				);

				/**
				 * If the value is numeric, we assume it is a post ID
				 */
				is_numeric( $value_s ) ? $args['include'] = array( $value_s ) : $args['s'] = $value_s;
			}

			$json = self::get_suggested_fields( $json, $args );

			restore_current_blog();
		}

		/**
		 * Filters the suggest results before encoding
		 *
		 * @param array<int, array{value: int, label: string}> $results
		 * @param array<string, mixed> $context
		 *
		 * @since 2.12.0
		 */
		$results = (array) apply_filters(
			'msls_meta_box_suggest_results',
			$json->get(),
			array(
				'blog_id'   => Request::get_var( Fields::FIELD_BLOG_ID, INPUT_POST ),
				'post_type' => Request::get_var( Fields::FIELD_POST_TYPE, INPUT_POST ),
				's'         => Request::get_var( Fields::FIELD_S, INPUT_POST ),
				'source_id' => Request::get_var( Fields::FIELD_SOURCE_ID, INPUT_POST ),
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die( wp_json_encode( $results ) ?: '' );
	}

	/**
	 * @param Json                 $json
	 * @param array<string, mixed> $args
	 *
	 * @return Json
	 */
	public static function get_suggested_fields( Json $json, array $args ): Json {
		/**
		 * Overrides the query-args for the 'suggest' fields in the MetaBox
		 *
		 * @param array $args<string, mixed>
		 *
		 * @since 0.9.9
		 */
		$args = (array) apply_filters( 'msls_meta_box_suggest_args', $args );

		foreach ( get_posts( $args ) as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			/**
			 * Manipulates the WP_Post object before using it
			 *
			 * @since 0.9.9
			 */
			$filtered = apply_filters( 'msls_meta_box_suggest_post', $post );
			if ( $filtered instanceof \WP_Post ) {
				$post = $filtered;
			}

			$json->add( $post->ID, get_the_title( $post ) );
		}

		wp_reset_postdata();

		return $json;
	}

	/**
	 * Adds the meta box to the post types
	 */
	public function add(): void {
		foreach ( msls_post_type()->get() as $post_type ) {

			add_meta_box(
				'msls',
				apply_filters(
					'msls_metabox_post_select_title',
					__( 'Multisite Language Switcher', 'multisite-language-switcher' )
				),
				array(
					$this,
					(
					msls_options()->activate_autocomplete ?
						'render_input' :
						'render_select'
					),
				),
				$post_type,
				'side',
				'high'
			);

			if ( msls_options()->activate_content_import ) {
				add_meta_box(
					'msls-content-import',
					apply_filters(
						'msls_metabox_post_import_title',
						__( 'Multisite Language Switcher - Import content', 'multisite-language-switcher' )
					),
					array(
						ContentImportMetaBox::instance(),
						'render',
					),
					$post_type,
					'side',
					'high'
				);
				add_action( 'admin_footer', array( ContentImportMetaBox::instance(), 'print_modal_html' ) );
			}
		}
	}

	/**
	 * Render the classic select-box
	 *
	 * @uses selected
	 */
	public function render_select(): void {
		$blogs = $this->collection->get();
		if ( $blogs ) {
			global $post;

			$type = get_post_type( $post->ID );
			if ( false === $type ) {
				return;
			}

			$mydata          = new Post( $post->ID );
			$origin_language = Collection::get_blog_language();
			$is_saved        = 'auto-draft' !== get_post_status( $post );

			$this->maybe_set_linked_post( $mydata );

			$temp = $post;

			wp_nonce_field( Plugin::path(), 'msls_noncename' );

			$lis = '';

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();
				$icon      = Icon::create( $type )->set_language( $language )->set_icon_type( $icon_type );

				$linked_post_id = null;
				if ( $mydata->has_value( $language ) ) {
					$linked_post_id = (int) $mydata->$language;
					$icon->set_href( $linked_post_id );
				}

				$selects   = '';
				$pt_object = get_post_type_object( $type );
				if ( $pt_object instanceof WP_Post_Type && $pt_object->hierarchical ) {
					$args = array(
						'post_type'         => $type,
						'selected'          => $mydata->$language,
						'name'              => Component::INPUT_PREFIX . $language,
						'show_option_none'  => ' ',
						'option_none_value' => 0,
						'sort_column'       => 'menu_order, post_title',
						'echo'              => 0,
					);

					/**
					 * Overrides the args for wp_dropdown_pages when using the HTML select in the MetaBox
					 *
					 * @param array $args
					 *
					 * @since 1.0.5
					 */
					$args = (array) apply_filters( 'msls_meta_box_render_select_hierarchical', $args );

					$selects .= wp_dropdown_pages( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					$selects .= sprintf(
						'<select name="msls_input_%1$s"><option value="0"></option>%2$s</select>',
						esc_attr( $language ),
						$this->render_options( $type, $mydata->$language ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				}

				$action = '';
				if ( $is_saved ) {
					$action = $this->get_create_new_link( $type, $language, $post->ID, $origin_language, $linked_post_id );
				}

				$lis .= sprintf(
					'<li><label for="msls_input_%1$s" class="msls-icon-wrapper %5$s">%2$s</label>%3$s%4$s</li>',
					esc_attr( $language ),
					$icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$selects, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$action, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					esc_attr( $icon_type )
				);

				restore_current_blog();
			}

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( new Wrapper( 'ul', $lis ) )->render();

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$post = $temp;
		} else {
			$message = esc_html__(
				'You should define at least another blog in a different language in order to have some benefit from this plugin!',
				'multisite-language-switcher'
			);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( new Wrapper( 'p', $message ) )->render();
		}
	}

	/**
	 * @param string $type
	 * @param ?int   $msls_id
	 *
	 * @return string
	 */
	public function render_options( $type, $msls_id ): string {
		$options = array();

		$posts = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => get_post_stati( array( 'internal' => '' ) ),
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => - 1,
			)
		);

		foreach ( $posts as $post ) {
			$options[] = $this->render_option( $post->ID, intval( $msls_id ) );
		}

		return implode( PHP_EOL, $options );
	}

	/**
	 * @param int $post_id
	 * @param int $msls_id
	 *
	 * @return string
	 */
	public function render_option( int $post_id, ?int $msls_id ): string {
		return wp_kses(
			sprintf(
				'<option value="%d" %s>%s</option>',
				esc_attr( strval( $post_id ) ),
				selected( $post_id, $msls_id, false ),
				get_the_title( $post_id )
			),
			Component::get_allowed_html()
		);
	}

	public function render_input(): void {
		$blogs = $this->collection->get();

		if ( $blogs ) {
			global $post;

			$post_type = get_post_type( $post->ID );
			if ( false === $post_type ) {
				return;
			}

			$my_data         = new Post( $post->ID );
			$origin_language = Collection::get_blog_language();
			$is_saved        = 'auto-draft' !== get_post_status( $post );

			$this->maybe_set_linked_post( $my_data );

			$temp  = $post;
			$items = '';

			wp_nonce_field( Plugin::path(), 'msls_noncename' );

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();
				$icon      = Icon::create()->set_language( $language )->set_icon_type( $icon_type );
				$value     = '';
				$title     = '';

				$linked_post_id = null;
				if ( $my_data->has_value( $language ) ) {
					$linked_post_id = (int) $my_data->$language;
					$icon->set_href( $linked_post_id );
					$value = $my_data->$language;
					$title = get_the_title( $value );
				}

				$action = '';
				if ( $is_saved ) {
					$action = $this->get_create_new_link( $post_type, $language, $post->ID, $origin_language, $linked_post_id );
				}

				$items .= sprintf(
					'<li class=""><label for="msls_title_%1$s" class="msls-icon-wrapper %7$s">%2$s</label><input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/><input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>%6$s</li>',
					$blog->userblog_id,
					$icon,
					$language,
					$value,
					$title,
					$action,
					esc_attr( $icon_type )
				);

				restore_current_blog();
			}

			echo wp_kses(
				sprintf(
					'<ul>%s</ul><input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/><input type="hidden" name="msls_source_id" id="msls_source_id" value="%d"/>',
					$items,
					$post_type,
					$post->ID
				),
				Component::get_allowed_html()
			);

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$post = $temp;
		} else {
			$message = esc_html__(
				'You should define at least another blog in a different language in order to have some benefit from this plugin!',
				'multisite-language-switcher'
			);

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( new Wrapper( 'p', $message ) )->render();
		}
	}

	/**
	 * Renders the action element for a language row in the metabox.
	 *
	 * Returns a "+" create button (Quick Create or classic link) when no
	 * translation is linked, or an external-link icon when one exists.
	 *
	 * @param string $type           Post type slug.
	 * @param string $language       Target language code.
	 * @param int    $post_id        Current (source) post ID.
	 * @param string $origin_language Source blog language code.
	 * @param ?int   $linked_post_id Linked translation post ID, or null.
	 *
	 * @return string
	 */
	private function get_create_new_link( string $type, string $language, int $post_id, string $origin_language, ?int $linked_post_id ): string {
		if ( null !== $linked_post_id ) {
			$href = (string) get_edit_post_link( $linked_post_id );

			if ( '' !== $href ) {
				$title = sprintf(
					/* translators: %s: language code */
					__( 'Edit the translation in the %s-blog', 'multisite-language-switcher' ),
					$language
				);

				return sprintf(
					'<a class="msls-edit-link" href="%1$s" target="_blank" title="%2$s"><span class="dashicons dashicons-external"></span></a>',
					esc_url( $href ),
					esc_attr( $title )
				);
			}
		}

		if ( msls_options()->activate_quick_create ) {
			$action_icon = ( new Icon( $type ) )
				->set_language( $language )
				->set_icon_type( 'action' )
				->set_id( $post_id )
				->set_origin_language( $origin_language );

			return $action_icon->get_a();
		}

		$action_icon = ( new Icon( $type ) )
			->set_language( $language )
			->set_icon_type( 'action' )
			->set_id( $post_id )
			->set_origin_language( $origin_language );

		$href = $action_icon->get_edit_new();

		$title = sprintf(
			/* translators: %s: language code */
			__( 'Create a new translation in the %s-blog', 'multisite-language-switcher' ),
			$language
		);

		return sprintf(
			'<a class="msls-create-new" href="%1$s" target="_blank" title="%2$s"><span class="dashicons dashicons-plus"></span></a>',
			esc_url( $href ),
			esc_attr( $title )
		);
	}

	/**
	 * Set
	 *
	 * @param int $post_id
	 */
	public function set( $post_id ): void {
		if ( $this->is_autosave( $post_id ) || ! $this->verify_nonce() ) {
			return;
		}

		$post_type  = Request::get_var( Fields::FIELD_POST_TYPE );
		$capability = 'page' === $post_type ? 'edit_page' : 'edit_post';

		if ( ! current_user_can( $capability, $post_id ) ) {
			return;
		}

		$this->save( $post_id, Post::class );
	}

	/**
	 * Sets the selected element in the data from the `$_GET` superglobal, if any.
	 *
	 * @param Post $mydata
	 *
	 * @return Post
	 */
	public function maybe_set_linked_post( Post $mydata ) {
		if ( ! Request::isset( array( Fields::FIELD_MSLS_ID, Fields::FIELD_MSLS_LANG ) ) ) {
			return $mydata;
		}

		$origin_lang = Request::get_var( Fields::FIELD_MSLS_LANG );

		if ( isset( $mydata->{$origin_lang} ) ) {
			return $mydata;
		}

		$origin_post_id = Request::get_var( Fields::FIELD_MSLS_ID );
		$origin_blog_id = $this->collection->get_blog_id( $origin_lang );

		if ( null === $origin_blog_id ) {
			return $mydata;
		}

		switch_to_blog( $origin_blog_id );
		$origin_post = get_post( $origin_post_id );
		restore_current_blog();

		if ( ! $origin_post instanceof \WP_Post ) {
			return $mydata;
		}

		$mydata->{$origin_lang} = $origin_post_id;

		return $mydata;
	}
}
