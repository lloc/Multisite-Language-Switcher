<?php
/**
 * MslsPostTag
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Post Tag
 * @package Msls
 */
class MslsPostTagClassic extends MslsMain {

	/**
	 * Init
	 * @return MslsPostTagClassic
	 */
	static function init() {
		$obj      = new self();
		$taxonomy = self::check();
		if ( $taxonomy ) {
			add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'create_input' ) );
			add_action( "{$taxonomy}_add_form_fields", array( $obj, 'create_input' ) );
		}
		add_action( "edited_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
		add_action( "create_{$taxonomy}", array( $obj, 'set' ), 10, 2 );
		return $obj;
	}

	/**
	 * Edit select
	 * @param StdClass $tag
	 */
	public function create_input( $tag ) {
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
					'<tr class="form-field">
					<th scope="row" valign="top">
					<label for="msls_input_%1$s">%2$s</label></th>
					<td>
					<select class="msls-translations" name="msls_input_%1$s">
					<option value=""></option>
					%3$s
					</select></td>
					</tr>',
					$language,
					$icon,
					$options
				);

				restore_current_blog();
			}
		}
	}

}
