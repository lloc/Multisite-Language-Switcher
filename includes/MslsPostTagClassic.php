<?php

namespace lloc\Msls;

use lloc\Msls\Component\Component;

/**
 * Post Tag Classic
 *
 * @package Msls
 */
class MslsPostTagClassic extends MslsPostTag {

	const EDIT_ACTION = 'msls_post_tag_classic_edit_input';
	const ADD_ACTION  = 'msls_post_tag_classic_add_input';

	/**
	 * Add the input fields to the add-screen of the taxonomies
	 *
	 * @param string $taxonomy
	 */
	public function add_input( string $taxonomy ): void {
		if ( did_action( self::ADD_ACTION ) ) {
			return;
		}

		$title_format = '<h3>%s</h3>';

		$item_format = '<label for="msls_input_%1$s">%2$s</label>
			<select class="msls-translations" name="msls_input_%1$s">
			<option value=""></option>
			%3$s
			</select>';

		echo '<div class="form-field">';
		$this->the_input( null, $title_format, $item_format );
		echo '</div>';

		do_action( self::ADD_ACTION, $taxonomy );
	}

	/**
	 * Add the input fields to the edit-screen of the taxonomies
	 *
	 * @param \WP_Term $tag
	 * @param string   $taxonomy
	 */
	public function edit_input( \WP_Term $tag, string $taxonomy ): void {
		if ( did_action( self::EDIT_ACTION ) ) {
			return;
		}

		$title_format = '<tr>
			<th colspan="2">
			<strong>%s</strong>
			</th>
			</tr>';

		$item_format = '<tr class="form-field">
			<th scope="row">
			<label for="msls_input_%1$s">%2$s</label></th>
			<td>
			<select class="msls-translations" name="msls_input_%1$s">
			<option value=""></option>
			%3$s
			</select></td>
			</tr>';

		$this->the_input( $tag, $title_format, $item_format );

		do_action( self::EDIT_ACTION, $tag, $taxonomy );
	}

	/**
	 * Print the input fields
	 * Returns true if the blog-collection is not empty
	 *
	 * @param ?\WP_Term $tag
	 * @param string    $title_format
	 * @param string    $item_format
	 *
	 * @return boolean
	 */
	public function the_input( ?\WP_Term $tag, string $title_format, string $item_format ): bool {
		$blogs = $this->collection->get();
		if ( ! empty( $blogs ) ) {
			$term_id = $tag->term_id ?? 0;
			$mydata  = MslsOptionsTax::create( $term_id );
			$type    = msls_content_types()->get_request();

			$this->maybe_set_linked_term( $mydata );

			echo wp_kses(
				sprintf( $title_format, esc_html( $this->get_select_title() ), esc_attr( $type ) ),
				Component::get_allowed_html()
			);

			foreach ( $blogs as $blog ) {
				$this->print_option( $blog, $type, $mydata, $item_format );
			}

			return true;
		}

		return false;
	}

	/**
	 * Prints options inputs
	 *
	 * @param MslsBlog       $blog
	 * @param string         $type
	 * @param MslsOptionsTax $mydata
	 * @param string         $item_format
	 */
	public function print_option( MslsBlog $blog, string $type, MslsOptionsTax $mydata, string $item_format ): void {
		switch_to_blog( $blog->userblog_id );

		$language  = $blog->get_language();
		$icon_type = $this->options->get_icon_type();
		$icon      = MslsAdminIcon::create()->set_language( $language )->set_icon_type( $icon_type );

		if ( $mydata->has_value( $language ) ) {
			$icon->set_href( (int) $mydata->$language );
		}

		$options = '';
		$terms   = get_terms(
			array(
				'taxonomy'   => $type,
				'hide_empty' => false,
			)
		);

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$options .= sprintf(
					'<option value="%d" %s>%s</option>',
					$term->term_id,
					selected( $term->term_id, $mydata->$language, false ),
					esc_html( $term->name )
				);
			}
		}

		echo wp_kses(
			sprintf( $item_format, esc_attr( $language ), $icon, $options ),
			Component::get_allowed_html()
		);

		restore_current_blog();
	}
}
