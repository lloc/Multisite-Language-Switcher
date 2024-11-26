<?php declare( strict_types=1 );

/**
 * MslsPostTag
 *
 * @author Dennis Ploetner <re@lloc.de>
 */

namespace lloc\Msls;

use lloc\Msls\Component\Component;

/**
 * Post Tag
 *
 * @package Msls
 */
class MslsPostTag extends MslsMain {

	const EDIT_ACTION = 'msls_post_tag_edit_input';
	const ADD_ACTION  = 'msls_post_tag_add_input';

	/**
	 * Suggest
	 *
	 * Echo a JSON-ified array of posts of the given post-type and
	 * the requested search-term and then die silently
	 */
	public static function suggest(): void {
		$json = new MslsJson();

		if ( MslsRequest::has_var( MslsFields::FIELD_BLOG_ID ) ) {
			switch_to_blog(
				MslsRequest::get_var( MslsFields::FIELD_BLOG_ID )
			);

			$post_type = MslsRequest::get( MslsFields::FIELD_POST_TYPE, '' );
			$args      = array(
				'taxonomy'   => sanitize_text_field( $post_type ),
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 10,
				'hide_empty' => 0,
			);

			if ( MslsRequest::has_var( MslsFields::FIELD_S ) ) {
				$args['search'] = sanitize_text_field(
					MslsRequest::get_var( MslsFields::FIELD_S )
				);
			}

			/**
			 * Overrides the query-args for the suggest fields
			 *
			 * @param array $args
			 *
			 * @since 0.9.9
			 */
			$args = (array) apply_filters( 'msls_post_tag_suggest_args', $args );
			foreach ( get_terms( $args ) as $term ) {
				/**
				 * Manipulates the term object before using it
				 *
				 * @param int|string|\WP_Term $term
				 *
				 * @since 0.9.9
				 */
				$term = apply_filters( 'msls_post_tag_suggest_term', $term );

				if ( $term instanceof \WP_Term ) {
					$json->add( $term->term_id, $term->name );
				}
			}
			restore_current_blog();
		}

		wp_die( $json->encode() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function init(): void {
		$options    = msls_options();
		$collection = msls_blog_collection();
		$class      = $options->activate_autocomplete ? self::class : MslsPostTagClassic::class;
		$obj        = new $class( $options, $collection );

		$taxonomy = msls_content_types()->acl_request();
		if ( '' != $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add_input' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'edit_input' ), 10, 2 );
			add_action( "edited_{$taxonomy}", array( $obj, 'set' ) );
			add_action( "create_{$taxonomy}", array( $obj, 'set' ) );
		}
	}

	/**
	 * Add the input fields to the add-screen of the taxonomies
	 *
	 * @param string $taxonomy
	 */
	public function add_input( string $taxonomy ): void {
		if ( did_action( self::ADD_ACTION ) ) {
			return;
		}

		$title_format = '<h3>%s</h3>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>';

		$item_format = '<label for="msls_title_%1$d">%2$s</label>
			<input type="hidden" id="msls_id_%1$d" name="msls_input_%3$s" value="%4$s"/>
			<input class="msls_title" id="msls_title_%1$d" name="msls_title_%1$d" type="text" value="%5$s"/>';

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
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>
			</th>
			</tr>';

		$item_format = '<tr class="form-field">
			<th scope="row">
			<label for="msls_title_%1$d">%2$s</label>
			</th>
			<td>
			<input type="hidden" id="msls_id_%1$d" name="msls_input_%3$s" value="%4$s"/>
			<input class="msls_title" id="msls_title_%1$d" name="msls_title_%1$d" type="text" value="%5$s"/>
			</td>
			</tr>';

		$this->the_input( $tag, $title_format, $item_format );

		do_action( self::EDIT_ACTION, $tag, $taxonomy );
	}

	/**
	 * Print the input fields
	 *
	 * Returns true if the blog collection is not empty
	 *
	 * @param ?\WP_Term $tag
	 * @param string    $title_format
	 * @param string    $item_format
	 *
	 * @return boolean
	 */
	public function the_input( ?\WP_Term $tag, string $title_format, string $item_format ): bool {
		$blogs = $this->collection->get();
		if ( $blogs ) {
			$term_id = $tag->term_id ?? 0;
			$mydata  = MslsOptionsTax::create( $term_id );
			$type    = msls_content_types()->get_request();

			$this->maybe_set_linked_term( $mydata );

			$allowed_html = Component::get_allowed_html();

			echo wp_kses(
				sprintf( $title_format, esc_html( $this->get_select_title() ), esc_attr( $type ) ),
				$allowed_html
			);

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();
				$icon      = MslsAdminIcon::create()->set_language( $language )->set_icon_type( $icon_type );

				$value = $title = '';
				if ( $mydata->has_value( $language ) ) {
					$term = get_term( $mydata->$language, $type );
					if ( is_object( $term ) ) {
						$icon->set_href( (int) $mydata->$language );
						$value = $mydata->$language;
						$title = $term->name;
					}
				}

				$content = sprintf(
					$item_format,
					$blog->userblog_id,
					$icon,
					esc_attr( $language ),
					esc_attr( $value ),
					esc_attr( $title )
				);

				echo wp_kses( $content, $allowed_html );

				restore_current_blog();
			}

			return true;
		}

		return false;
	}

	/**
	 * Set calls the save method if taxonomy is set
	 *
	 * @param int $term_id
	 */
	public function set( $term_id ): void {
		if ( msls_content_types()->acl_request() ) {
			$this->save( $term_id, MslsOptionsTax::class );
		}
	}

	/**
	 * Sets the selected element in the data from the `$_GET` superglobal, if any.
	 *
	 * @param MslsOptionsTax $mydata
	 *
	 * @return MslsOptionsTax
	 */
	public function maybe_set_linked_term( MslsOptionsTax $mydata ) {
		if ( ! MslsRequest::isset( array( MslsFields::FIELD_MSLS_ID, MslsFields::FIELD_MSLS_LANG ) ) ) {
			return $mydata;
		}

		$origin_lang = MslsRequest::get_var( MslsFields::FIELD_MSLS_LANG );

		if ( isset( $mydata->{$origin_lang} ) ) {
			return $mydata;
		}

		$origin_term_id = MslsRequest::get_var( MslsFields::FIELD_MSLS_ID );

		$origin_blog_id = $this->collection->get_blog_id( $origin_lang );

		if ( null === $origin_blog_id ) {
			return $mydata;
		}

		switch_to_blog( $origin_blog_id );
		$origin_term = get_term( $origin_term_id, $mydata->base );
		restore_current_blog();

		if ( ! $origin_term instanceof \WP_Term ) {
			return $mydata;
		}

		$mydata->{$origin_lang} = $origin_term_id;

		return $mydata;
	}

	/**
	 * Get the title for the select field
	 *
	 * @return string
	 */
	protected function get_select_title(): string {
		return apply_filters(
			'msls_term_select_title',
			__( 'Multisite Language Switcher', 'multisite-language-switcher' )
		);
	}
}
