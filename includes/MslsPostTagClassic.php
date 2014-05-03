<?php
/**
 * MslsPostTagClassic
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Post Tag Clasic
 * @package Msls
 */
class MslsPostTagClassic extends MslsPostTag {

	/**
	 * Init
	 * @return MslsPostTagClassic
	 */
	static function init() {
		return new self();
	}

	/**
	 * Add the input fields to the add-screen of the taxonomies
	 * @param StdClass $tag
	 */
	public function add_input( $tag ) {
		$title_format = '<h3>%s</h3>';
		$item_format  = '<label for="msls_input_%1$s">%2$s</label>
			<select class="msls-translations" name="msls_input_%1$s">
			<option value=""></option>
			%3$s
			</select>';
		echo '<div class="form-field">';
		$this->the_input( $tag, $title_format, $item_format );
		echo '</div>';
	}
	
	/**
	 * Add the input fields to the edit-screen of the taxonomies
	 * @param StdClass $tag
	 */
	public function edit_input( $tag ) {
		$title_format = '<tr>
			<th colspan="2">
			<strong>%s</strong>
			</th>
			</tr>';
		$item_format  = '<tr class="form-field">
			<th scope="row" valign="top">
			<label for="msls_input_%1$s">%2$s</label></th>
			<td>
			<select class="msls-translations" name="msls_input_%1$s">
			<option value=""></option>
			%3$s
			</select></td>
			</tr>';
		$this->the_input( $tag, $title_format, $item_format );
	}

	/**
	 * Print the input fields
	 * @param StdClass $tag
	 * @param string $title_format
	 * @param string $item_format
	 */
	public function the_input( $tag, $title_format, $item_format ) {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );
		$blogs   = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			$mydata = MslsOptionsTax::create( $term_id );
			$type   = MslsContentTypes::create()->get_request();
			printf(
				$title_format,
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
					$item_format,
					$language,
					$icon,
					$options
				);

				restore_current_blog();
			}
		}
	}

}
