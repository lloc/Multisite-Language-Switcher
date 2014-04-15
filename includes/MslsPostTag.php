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
		$json = new MslsJson;
		if ( isset( $_REQUEST['blog_id'] ) ) {
			switch_to_blog( (int) $_REQUEST['blog_id'] );
			$args = apply_filters(
				'msls_post_tag_suggest_args',
				array(
					'orderby'    => 'name',
					'order'      => 'ASC',
					'number'     => 10,
					'hide_empty' => 0,
				)
			);
			if ( isset( $_REQUEST['s'] ) ) {
				$args['name__like'] = sanitize_text_field( $_REQUEST['s'] );
			}
			foreach ( get_terms( sanitize_text_field( $_REQUEST['post_type'] ), $args ) as $term ) {
				$term = apply_filters( 'msls_post_tag_suggest_term', $term );
				$json->add( $term->term_id, $term->name );
			}
			restore_current_blog();
		}
		echo $json; // xss ok
		die();
	}

	/**
	 * Init
	 * @return MslsPostTag
	 */
	static function init() {
		$obj      = new self();
		$taxonomy = self::check();
		if ( $taxonomy ) {
			if ( MslsOptions::instance()->activate_autocomplete ) {
				add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'edit_input' ) );
				add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add_input' ) );
			}
			else {
				add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'edit_select' ) );
				add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add_select' ) );
			}
			add_action( "edited_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
			add_action( "create_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
		}
		return $obj;
	}

	/**
	 * Check the taxonomy
	 * @return string
	 */
	static function check() {
		if ( ! MslsOptions::instance()->is_excluded() && isset( $_REQUEST['taxonomy'] ) ) {
			$type = MslsContentTypes::create()->get_request();
			if ( ! empty( $type ) ) {
				$tax = get_taxonomy( $type );
				if ( $tax && current_user_can( $tax->cap->manage_terms ) ) {
					return $type;
				}
			}
		}
		return '';
	}

	/**
	 * Edit select
	 * @param StdClass $tag
	 */
	public function edit_select( $tag ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$blogs   = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			$mydata = MslsOptionsTax::create( $term_id );
			$type   = MslsContentTypes::create()->get_request();
			printf(
				'<tr><th colspan="2"><strong>%s</strong></th></tr>',
				__( 'Multisite Language Switcher', 'msls' )
			);
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$icon     = MslsAdminIcon::create()->set_language( $language )->set_src( $flag_url );

				$options  = '';
				$terms    = get_terms( $type, array( 'hide_empty' => 0 ) );

				if ( $mydata->has_value( $language ) ) {
					$icon->set_href( $mydata->$language );
				}
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$options .= sprintf(
							'<option value="%s"%s>%s</option>',
							$term->term_id,
							( $term->term_id == $mydata->$language ? ' selected="selected"' : '' ),
							$term->name
						);
					}
				}
				printf(
					'<tr class="form-field"><th scope="row" valign="top"><label for="msls_input_%1$s">%2$s </label></th><td><select class="msls-translations" name="msls_input_%1$s"><option value=""></option>%3$s</select></td>',
					$language,
					$icon,
					$options
				);
				restore_current_blog();
			}
		}
	}

	/**
	 * Add select
	 * @param StdClass $tag
	 */
	public function add_select( $tag ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$blogs   = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			$mydata = MslsOptionsTax::create( $term_id );
			$type   = MslsContentTypes::create()->get_request();
			printf(
				'<tr><th colspan="2"><strong>%s</strong></th></tr>',
				__( 'Multisite Language Switcher', 'msls' )
			);
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );
	
				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$icon     = MslsAdminIcon::create()->set_language( $language )->set_src( $flag_url );

				$options  = '';
				$terms    = get_terms( $type, array( 'hide_empty' => 0 ) );
	
				if ( $mydata->has_value( $language ) ) {
					$icon->set_href( $mydata->$language );
				}
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$options .= sprintf(
							'<option value="%s"%s>%s</option>',
							$term->term_id,
							( $term->term_id == $mydata->$language ? ' selected="selected"' : '' ),
							$term->name
						);
					}
				}
				printf(
					'<tr class="form-field"><th scope="row" valign="top"><label for="msls_input_%1$s">%2$s </label></th><td><select class="msls-translations" name="msls_input_%1$s"><option value=""></option>%3$s</select></td>',
					$language,
					$icon,
					$options
				);
				restore_current_blog();
			}
		}
	}

	/**
	 * Edit input
	 * @param StdClass $tag
	 */
	public function edit_input( $tag ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$blogs   = MslsBlogCollection::instance()->get();
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
	
				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$icon     = MslsAdminIcon::create()->set_language( $language )->set_src( $flag_url );

				$value = $title = '';
				if ( $my_data->has_value( $language ) ) {
					$term = get_term( $my_data->$language, $type );
					if ( is_object( $term ) ) {
						$icon->set_href( $my_data->$language );
						$value = $my_data->$language;
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
					$language,
					$value,
					$title
				);
				restore_current_blog();
			}
		}
	}

	/**
	 * Add input
	 * @param StdClass $tag
	 */
	public function add_input( $tag ) {
		$format_str = '<tr><th colspan="2"><strong>%s</strong>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" type="text" value="suggest_terms"/>
			</th></tr>';
	
		$blogs = $this->envelop( $tag, $format_str );
		if ( $blogs ) {
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language = $blog->get_language();
				$flag_url = MslsOptions::instance()->get_flag_url( $language );
				$icon     = MslsAdminIcon::create()->set_language( $language )->set_src( $flag_url );

				$value = $title = '';
				if ( $my_data->has_value( $language ) ) {
					$term = get_term( $my_data->$language, $type ); 
					if ( is_object( $term ) ) { 
						$icon->set_href( $my_data->$language );
						$value = $my_data->$language;
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
					$language,
					$value,
					$title
				);
				restore_current_blog();
			}
		}
	}

	protected function envelop( $tag, $format_str ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$retval  = array(); 
		$blogs   = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			$retval  = array(
				$blogs,
				MslsOptionsTax::create( $term_id ),
				sprintf(
					$format_str,
					__( 'Multisite Language Switcher', 'msls' ),
					MslsContentTypes::create()->get_request()
				),
			);
		}
		return $retval;
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
