<?php
/**
 * MslsPostTag
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Post Tag
 * @package Msls
 */
class MslsPostTag extends MslsMain {

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
				'orderby' => 'name',
				'order' => 'ASC',
				'number' => 10,
				'hide_empty' => 0,
			);
			if ( isset( $_REQUEST['s'] ) ) {
				$args['name__like'] = $_REQUEST['s'];
			}
			$json_obj = new MslsJson;
			foreach ( get_terms( $_REQUEST['post_type'], $args ) as $term ) {
				$json_obj->add( $term->term_id, $term->name );
			}
			restore_current_blog();
		}
		echo $json_obj;
		die();
	}

	/**
	 * Init
	 * @return MslsPostTag
	 */
	public static function init() {
		$taxonomy = MslsPostTag::check();
		if ( $taxonomy ) {
			$obj = new self();
			add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'add' ) );
			add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add' ) );
			add_action( "edited_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
			add_action( "create_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
		}
	}

	/**
	 * Check the taxonomy
	 * @return string
	 */
	public static function check() {
		if ( MslsOptions::instance()->is_excluded() || !isset( $_REQUEST['taxonomy'] ) ) {
			$type = MslsContentTypes::create()->get_request();
			if ( !empty( $type ) ) {
				$tax = get_taxonomy( $type );
				if ( $tax && current_user_can( $tax->cap->manage_terms ) )
					return $type;
			}
		}
		return '';
	}

	/**
	 * Add
	 * @param StdClass
	 * @return MslsPostTag
	 */
	public function add( $tag ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$blogs   = $this->blogs->get();
		if ( $blogs ) {
			$my_data = MslsOptionsTax::create( $term_id );
			$type    = MslsContentTypes::create()->get_request();
			printf(
				'<tr>
				<th colspan="2">
				<strong>%s</strong>
				<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
				<input type="hidden" name="msls_action" id="msls_action" type="text" value="suggest_terms"/>
				</th>
				</tr>',
				__( 'Multisite Language Switcher', 'msls' ),
				$type
			);
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );
				$lang  = $blog->get_language();
				$icon  = MslsAdminIcon::create()
					->set_language( $lang )
					->set_src( $this->options->get_flag_url( $lang ) );
				$value = $title = '';
				if ( $my_data->has_value( $lang ) ) {
					$term = get_term( $my_data->$lang, $type ); 
					if ( is_object( $term ) ) { 
						$icon->set_href( $my_data->$lang );
						$value = $my_data->$lang;
						$title = $term->name;
					}
				}
				printf(
					'<tr class="form-field">
					<th scope="row" valign="top">
					<label for="msls_title_%1$s">%2$s</label>
					</th>
					<td>
					<input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/>
					<input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>
					</td>',
					$blog->userblog_id,
					$icon,
					$lang,
					$value,
					$title
				);
				restore_current_blog();
			}
		}
	}

	/**
	 * Set
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public function set( $term_id, $tt_id ) {
		if ( MslsPostTag::check() )
			$this->save( $term_id, 'MslsOptionsTax' );
	}

}
