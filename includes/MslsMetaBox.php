<?php
/**
 * MslsMetaBox
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

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
	static function suggest() {
		$json = new MslsJson;
		if ( isset( $_REQUEST['blog_id'] ) ) {
			switch_to_blog( (int) $_REQUEST['blog_id'] );

			$args = array(
				'post_status'    => 'any',
				'posts_per_page' => 10,
			);
				
			if ( isset( $_REQUEST['post_type'] ) ) {
				$args['post_type'] = sanitize_text_field( $_REQUEST['post_type'] );
			}

			if ( isset( $_REQUEST['s'] ) ) {
				$args['s'] = sanitize_text_field( $_REQUEST['s'] );
			}

			/**
			 * Overrides the query-args for the suggest fields in the MetaBox
			 * @since 0.9.9
			 * @param array $args
			 */
			$args = (array) apply_filters( 'msls_meta_box_suggest_args', $args );

			$my_query = new WP_Query( $args );
			while ( $my_query->have_posts() ) {
				$my_query->the_post();

				/**
				 * Manipulates the WP_Post object before using it
				 * @since 0.9.9
				 * @param WP_Post $post
				 */
				$my_query->post = apply_filters( 'msls_meta_box_suggest_post', $my_query->post );

				if ( is_object( $my_query->post ) ) {
					$json->add( get_the_ID(), get_the_title() );
				}
			}
			wp_reset_postdata();
			restore_current_blog();
		}
		echo $json; // xss ok
		die();
	}

	/**
	 * Init
	 * @return MslsMetaBox
	 */
	static function init() {
		$obj = new self();
		if ( ! MslsOptions::instance()->is_excluded() ) {
			add_action( 'add_meta_boxes', array( $obj, 'add' ) );
			add_action( 'save_post', array( $obj, 'set' ) );
			add_action( 'trashed_post', array( $obj, 'delete' ) );
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
				__( 'Multisite Language Switcher', 'msls' ),
				array(
					$this,
					( 
						MslsOptions::instance()->activate_autocomplete ?
						'render_input' :
						'render_select'
					),
				),
				$post_type,
				'side',
				'high'
			);
		}
	}

	/**
	 * Render the classic select-box
	 */
	public function render_select() {
		$blogs = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			global $post;
			$type   = get_post_type( $post->ID );
			$mydata = new MslsOptionsPost( $post->ID );
			$temp   = $post;
			$lis    = '';
			wp_nonce_field( MSLS_PLUGIN_PATH, 'msls_noncename' );
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$selects  = '';
				$pto      = get_post_type_object( $type );

				$icon = MslsAdminIcon::create();
				$icon->set_language( $language );
				$icon->set_src( $flag_url );

				if ( $mydata->has_value( $language ) )
					$icon->set_href( $mydata->$language );
				if ( $pto->hierarchical ) {
					$selects .= wp_dropdown_pages(
						array(
							'post_type' => $type,
							'selected' => $mydata->$language,
							'name' => 'msls_input_' . $language,
							'show_option_none' => ' ',
							'sort_column' => 'menu_order, post_title',
							'echo' => 0,
						)
					);
				}
				else {
					$options  = '';
					$my_query = new WP_Query(
						array(
							'post_type' => $type,
							'post_status' => 'any',
							'orderby' => 'title',
							'order' => 'ASC',
							'posts_per_page' => (-1),
						)
					);
					while ( $my_query->have_posts() ) {
						$my_query->the_post();
						$my_id    = get_the_ID();
						$options .= sprintf(
							'<option value="%s" %s>%s</option>',
							$my_id,
							selected( $my_id, $mydata->$language, false ),
							get_the_title()
						);
					}
					$selects .= sprintf(
						'<select name="msls_input_%s"><option value=""></option>%s</select>',
						$language,
						$options
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
			printf(
				'<ul>%s</ul><input type="submit" class="button-secondary" value="%s"/>',
				$lis,
				__( 'Update', 'msls' )
			);
			$post = $temp;
		}
		else {
			printf(
				'<p>%s</p>',
				__( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'msls' )
			);
		}
	}
	
	/**
	 * Render the suggest input-field
	 */
	public function render_input() {
		$blogs = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			global $post;
			$post_type = get_post_type( $post->ID );
			$my_data   = new MslsOptionsPost( $post->ID );
			$temp      = $post;
			$items     = '';
			wp_nonce_field( MSLS_PLUGIN_PATH, 'msls_noncename' );
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$icon     = MslsAdminIcon::create()->set_language( $language )->set_src( $flag_url );

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
				<input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/>
				<input type="submit" class="button-secondary" value="%s"/>',
				$items,
				$post_type,
				__( 'Update', 'msls' )
			);
			$post = $temp;
		}
		else {
			printf(
				'<p>%s</p>',
				__( 'You should define at least another blog in a different language in order to have some benefit from this plugin!', 'msls' )
			);
		}
	}

	/**
	 * Set
	 * @param int $post_id
	 */
	public function set( $post_id ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['msls_noncename'] ) || ! wp_verify_nonce( $_POST['msls_noncename'], MSLS_PLUGIN_PATH ) ) {
			return;
		}
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		}
		else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}
		$this->save( $post_id, 'MslsOptionsPost' );
	}

}
