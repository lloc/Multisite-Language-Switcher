<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Component\Component;
use lloc\Msls\Component\Wrapper;
use lloc\Msls\ContentImport\MetaBox as ContentImportMetaBox;

/**
 * Meta box for the edit mode of the (custom) post types
 *
 * @package Msls
 */
final class MslsMetaBox extends MslsMain {

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
		$json = new MslsJson();

		if ( MslsRequest::has_var( MslsFields::FIELD_BLOG_ID, INPUT_POST ) ) {
			switch_to_blog( MslsRequest::get_var( MslsFields::FIELD_BLOG_ID, INPUT_POST ) );

			$args = array(
				'post_status'    => get_post_stati( array( 'internal' => '' ) ),
				'posts_per_page' => 10,
			);

			if ( MslsRequest::has_var( MslsFields::FIELD_POST_TYPE, INPUT_POST ) ) {
				$args['post_type'] = sanitize_text_field(
					MslsRequest::get_var( MslsFields::FIELD_POST_TYPE, INPUT_POST )
				);
			}

			if ( MslsRequest::has_var( MslsFields::FIELD_S, INPUT_POST ) ) {
				$value_s = sanitize_text_field(
					MslsRequest::get_var( MslsFields::FIELD_S, INPUT_POST )
				);

				/**
				 * If the value is numeric, we assume it is a post ID
				 */
				is_numeric( $value_s ) ? $args['include'] = array( $value_s ) : $args['s'] = $value_s;
			}

			$json = self::get_suggested_fields( $json, $args );

			restore_current_blog();
		}

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die( $json->encode() );
	}

	/**
	 * @param MslsJson             $json
	 * @param array<string, mixed> $args
	 *
	 * @return MslsJson
	 */
	public static function get_suggested_fields( MslsJson $json, array $args ): MslsJson {
		/**
		 * Overrides the query-args for the suggest fields in the MetaBox
		 *
		 * @param array $args<string, mixed>
		 *
		 * @since 0.9.9
		 */
		$args = (array) apply_filters( 'msls_meta_box_suggest_args', $args );

		foreach ( get_posts( $args ) as $post ) {
			/**
			 * Manipulates the WP_Post object before using it
			 *
			 * @param \WP_Post $post
			 *
			 * @since 0.9.9
			 */
			$post = apply_filters( 'msls_meta_box_suggest_post', $post );
			if ( is_object( $post ) ) {
				$json->add( $post->ID, get_the_title( $post ) );
			}
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

			$type   = get_post_type( $post->ID );
			$mydata = new MslsOptionsPost( $post->ID );

			$this->maybe_set_linked_post( $mydata );

			$temp = $post;

			wp_nonce_field( MslsPlugin::path(), 'msls_noncename' );

			$lis = '';

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();
				$icon      = MslsAdminIcon::create( $type )->set_language( $language )->set_icon_type( $icon_type );

				if ( $mydata->has_value( $language ) ) {
					$icon->set_href( (int) $mydata->$language );
				}

				$selects  = '';
				$p_object = get_post_type_object( $type );

				if ( $p_object->hierarchical ) {
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

				$lis .= sprintf(
					'<li><label for="msls_input_%1$s msls-icon-wrapper %4$s">%2$s</label>%3$s</li>',
					esc_attr( $language ),
					$icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$selects, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					esc_attr( $icon_type )
				);

				restore_current_blog();
			}

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( new Wrapper( 'ul', $lis ) )->render();

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
			$options[] = $this->render_option( $post->ID, intval($msls_id) );
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
			$my_data   = new MslsOptionsPost( $post->ID );

			$this->maybe_set_linked_post( $my_data );

			$temp  = $post;
			$items = '';

			wp_nonce_field( MslsPlugin::path(), 'msls_noncename' );

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();
				$icon      = MslsAdminIcon::create()->set_language( $language )->set_icon_type( $icon_type );

				$value = $title = '';
				if ( $my_data->has_value( $language ) ) {
					$icon->set_href( (int) $my_data->$language );
					$value = $my_data->$language;
					$title = get_the_title( $value );
				}

				$items .= sprintf(
					'<li class=""><label for="msls_title_%1$s msls-icon-wrapper %6$s">%2$s</label><input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/><input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/></li>',
					$blog->userblog_id,
					$icon,
					$language,
					$value,
					$title,
					esc_attr( $icon_type )
				);

				restore_current_blog();
			}

			echo wp_kses(
				sprintf(
					'<ul>%s</ul><input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/>',
					$items,
					$post_type
				),
				Component::get_allowed_html()
			);

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
	 * Set
	 *
	 * @param int $post_id
	 */
	public function set( $post_id ): void {
		if ( $this->is_autosave( $post_id ) || ! $this->verify_nonce() ) {
			return;
		}

		$post_type  = MslsRequest::get_var( MslsFields::FIELD_POST_TYPE );
		$capability = 'page' === $post_type ? 'edit_page' : 'edit_post';

		if ( ! current_user_can( $capability, $post_id ) ) {
			return;
		}

		$this->save( $post_id, MslsOptionsPost::class );
	}

	/**
	 * Sets the selected element in the data from the `$_GET` superglobal, if any.
	 *
	 * @param MslsOptionsPost $mydata
	 *
	 * @return MslsOptionsPost
	 */
	public function maybe_set_linked_post( MslsOptionsPost $mydata ) {
		if ( ! MslsRequest::isset( array( MslsFields::FIELD_MSLS_ID, MslsFields::FIELD_MSLS_LANG ) ) ) {
			return $mydata;
		}

		$origin_lang = MslsRequest::get_var( MslsFields::FIELD_MSLS_LANG );

		if ( isset( $mydata->{$origin_lang} ) ) {
			return $mydata;
		}

		$origin_post_id = MslsRequest::get_var( MslsFields::FIELD_MSLS_ID );
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
