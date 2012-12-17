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
		$result = array();
		if ( isset( $_REQUEST['blog_id'] ) ) {
			switch_to_blog( (int) $_REQUEST['blog_id'] );
			$args = array(
				'post_status' => 'any',
				'posts_per_page' => 10,
			);
			if ( isset( $_REQUEST['post_type'] ) ) {
				$args['post_type'] = $_REQUEST['post_type'];
			}
			if ( isset( $_REQUEST['s'] ) ) {
				$args['s'] = $_REQUEST['s'];
			}
			$my_query = new WP_Query( $args );
			$json_obj = new MslsJson;
			while ( $my_query->have_posts() ) {
				$my_query->the_post();
				$json_obj->add( get_the_ID(), get_the_title() );
			}
			restore_current_blog();
		}
		echo $json_obj;
		die();
	}

	/**
	 * Init
	 */
	static function init() {
		$options = MslsOptions::instance();
		if ( !$options->is_excluded() ) {
			$obj = new self();
			add_action( 'add_meta_boxes', array( $obj, 'add' ) );
			add_action( 'save_post', array( $obj, 'set' ) );
			add_action( 'trashed_post', array( $obj, 'delete' ) );
		}
	}

	/**
	 * Add
	 */
	public function add() {
		$pt_arr = MslsPostType::instance()->get();
		foreach ( $pt_arr as $post_type ) {
			add_meta_box(
				'msls',
				__( 'Multisite Language Switcher', 'msls' ),
				array( $this, 'render' ),
				$post_type,
				'side',
				'high'
			);
		}
	}

	/**
	 * Render
	*/
	public function render() {
		$blogs = $this->blogs->get();
		if ( $blogs ) {
			global $post;
			$post_type = get_post_type( $post->ID );
			$mydata    = new MslsOptionsPost( $post->ID );
			$temp      = $post;
			$items     = '';
			wp_nonce_field( MSLS_PLUGIN_PATH, 'msls_noncename' );
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );
				$lang = $blog->get_language();
				$icon = MslsAdminIcon::create()
					->set_language( $lang )
					->set_src( $this->options->get_flag_url( $lang ) );
				$value = $title = '';
				if ( $mydata->has_value( $lang ) ) {
					$icon->set_href( $mydata->$lang );
					$value = $mydata->$lang;
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
					$lang,
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
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			wp_is_post_revision( $post_id ) ||
			!isset( $_POST['msls_noncename'] ) || 
			!wp_verify_nonce( $_POST['msls_noncename'], MSLS_PLUGIN_PATH ) )
			return;
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page' ) )
				return;
		}
		else {
			if ( !current_user_can( 'edit_post' ) )
				return;
		}
		$this->save( $post_id, 'MslsOptionsPost' );
	}

}
