<?php
/**
 * MslsMetaBox
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\ContentImport\MetaBox as ContentImportMetaBox;

/**
 * Meta box for the edit mode of the (custom) post types
 * @package Msls
 */
class MslsMetaBox extends MslsMain {

	/**
	 * Suggest
	 *
	 * Echo a JSON-ified array of posts of the given post-type and
	 * the requested search-term and then die silently
	 */
	public static function suggest() {
		$json = new MslsJson;

		if ( filter_has_var( INPUT_POST, 'blog_id' ) ) {
			switch_to_blog(
				filter_input( INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT )
			);

			$args = [
				'post_status'    => get_post_stati( [ 'internal' => '' ] ),
				'posts_per_page' => 10,
			];

			if ( filter_has_var( INPUT_POST, 'post_type' ) ) {
				$args['post_type'] = sanitize_text_field(
					filter_input( INPUT_POST, 'post_type' )
				);
			}

			if ( filter_has_var( INPUT_POST, 's' ) ) {
				$args['s'] = sanitize_text_field(
					filter_input( INPUT_POST, 's' )
				);
			}

			$json = self::get_suggested_fields( $json, $args );
		}

		wp_die( $json->encode() );
	}

	/**
	 * @param MslsJson $json
	 * @param array $args
	 *
	 * @return mixed
	 */
	public static function get_suggested_fields( $json, $args ) {
		/**
		 * Overrides the query-args for the suggest fields in the MetaBox
		 *
		 * @param array $args
		 *
		 * @since 0.9.9
		 *
		 */
		$args = (array) apply_filters( 'msls_meta_box_suggest_args', $args );

		$my_query = new \WP_Query( $args );
		while ( $my_query->have_posts() ) {
			$my_query->the_post();

			/**
			 * Manipulates the WP_Post object before using it
			 *
			 * @param \WP_Post $post
			 *
			 * @since 0.9.9
			 *
			 */
			$my_query->post = apply_filters( 'msls_meta_box_suggest_post', $my_query->post );

			if ( is_object( $my_query->post ) ) {
				$json->add( get_the_ID(), get_the_title() );
			}
		}

		wp_reset_postdata();
		restore_current_blog();

		return $json;
	}

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsMetaBox
	 */
	public static function init() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();
		$obj        = new static( $options, $collection );

		if ( ! $options->is_excluded() ) {
			add_action( 'add_meta_boxes', [ $obj, 'add' ] );
			add_action( 'save_post', [ $obj, 'set' ] );
			add_action( 'trashed_post', [ $obj, 'delete' ] );
		}

		return $obj;
	}

	/**
	 * Add
	 */
	public function add() {
		foreach ( MslsPostType::instance()->get() as $post_type ) {
			add_meta_box(
				'msls',
				apply_filters(
					'msls_metabox_post_select_title',
					__( 'Multisite Language Switcher', 'multisite-language-switcher' )
				),
				[
					$this,
					(
					MslsOptions::instance()->activate_autocomplete ?
						'render_input' :
						'render_select'
					),
				],
				$post_type,
				'side',
				'high'
			);

			if ( MslsOptions::instance()->activate_content_import ) {
				add_meta_box(
					'msls-content-import',
					apply_filters(
						'msls_metabox_post_import_title',					
						__( 'Multisite Language Switcher - Import content', 'multisite-language-switcher' )
					),
					[
						ContentImportMetaBox::instance(),
						'render',
					],
					$post_type,
					'side',
					'high'
				);
				add_action( 'admin_footer', [ ContentImportMetaBox::instance(), 'print_modal_html' ] );
			}
		}
	}

	/**
	 * Render the classic select-box
	 * @uses selected
	 */
	public function render_select() {
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

				$language = $blog->get_language();
				$icon     = MslsAdminIcon::create( $type )
				                         ->set_language( $language )
				                         ->set_icon_type( 'flag' );

				if ( $mydata->has_value( $language ) ) {
					$icon->set_href( $mydata->$language );
				}

				$selects  = '';
				$p_object = get_post_type_object( $type );

				if ( $p_object->hierarchical ) {
					$args = [
						'post_type'         => $type,
						'selected'          => $mydata->$language,
						'name'              => 'msls_input_' . $language,
						'show_option_none'  => ' ',
						'option_none_value' => 0,
						'sort_column'       => 'menu_order, post_title',
						'echo'              => 0,
					];
					/**
					 * Overrides the args for wp_dropdown_pages when using the HTML select in the MetaBox
					 *
					 * @param array $args
					 *
					 * @since 1.0.5
					 *
					 */
					$args = (array) apply_filters( 'msls_meta_box_render_select_hierarchical', $args );

					$selects .= wp_dropdown_pages( $args );
				} else {
					$selects .= sprintf(
						'<select name="msls_input_%s"><option value="0"></option>%s</select>',
						$language,
						$this->render_options( $type, $mydata->$language )
					);
				}

				$lis .= sprintf(
					'<li><label for="msls_input_%s">%s</label>%s</li>',
					$language,
					$icon,
					$selects
				);

				restore_current_blog();
			}

			printf( '<ul>%s</ul>', $lis );

			$post = $temp;
		} else {
			printf(
				'<p>%s</p>',
				__( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'multisite-language-switcher' )
			);
		}
	}

	/**
	 * @param string $type
	 * @param string $msls_id
	 *
	 * @return string
	 */
	public function render_options( $type, $msls_id ) {
		$options = [];

		$my_query = new \WP_Query( [
			'post_type'      => $type,
			'post_status'    => get_post_stati( [ 'internal' => '' ] ),
			'orderby'        => 'title',
			'order'          => 'ASC',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
		] );

		if ( $my_query->have_posts() ) {
			foreach ( $my_query->posts as $post_id ) {
				$options[] = $this->render_option( $post_id, $msls_id );
			}
		}

		return implode( PHP_EOL, $options );
	}

	/**
	 * @param string $post_id
	 * @param string $msls_id
	 *
	 * @return string
	 */
	public function render_option( $post_id, $msls_id ) {
		return sprintf( '<option value="%s" %s>%s</option>', $post_id, selected( $post_id, $msls_id, false ), get_the_title( $post_id ) );
	}

	/**
	 * Render the suggest input-field
	 *
	 * @param bool $echo Whether the metabox markup should be echoed to the page or not.
	 */
	public function render_input( $echo = true ) {
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

				$language = $blog->get_language();
				$icon     = MslsAdminIcon::create()
					->set_language( $language )
					->set_icon_type( 'flag' );

				$value = $title = '';

				if ( $my_data->has_value( $language ) ) {
					$icon->set_href( $my_data->$language );
					$value = $my_data->$language;
					$title = get_the_title( $value );
				}

				$items .= sprintf(
					'<li>
					<label for="msls_title_%1$s">%2$s</label>
					<input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/>
					<input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>
					</li>',
					$blog->userblog_id,
					$icon,
					$language,
					$value,
					$title
				);

				restore_current_blog();
			}

			printf(
				'<ul>%s</ul>
				<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
				<input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/>',
				$items,
				$post_type
			);

			$post = $temp;
		} else {
			printf(
				'<p>%s</p>',
				__( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'multisite-language-switcher' )
			);
		}
	}

	/**
	 * Set
	 *
	 * @param int $post_id
	 */
	public function set( $post_id ) {
		if ( $this->is_autosave( $post_id ) || ! $this->verify_nonce() ) {
			return;
		}

		$post_type  = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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
		if ( ! isset( $_GET['msls_id'], $_GET['msls_lang'] ) ) {
			return $mydata;
		}

		$origin_lang = trim( $_GET['msls_lang'] );

		if ( isset( $mydata->{$origin_lang} ) ) {
			return $mydata;
		}

		$origin_post_id = (int) $_GET['msls_id'];

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
