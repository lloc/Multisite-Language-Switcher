<?php declare( strict_types=1 );

/**
 * MslsPostTag
 *
 * @author Dennis Ploetner <re@lloc.de>
 */

namespace lloc\Msls;

/**
 * Post Tag
 *
 * @package Msls
 */
class MslsPostTag extends MslsMain {

	/**
	 * Suggest
	 *
	 * Echo a JSON-ified array of posts of the given post-type and
	 * the requested search-term and then die silently
	 */
	public static function suggest() {
		$json = new MslsJson();

		if ( MslsRequest::has_var( MslsFields::FIELD_BLOG_ID ) ) {
			switch_to_blog(
				MslsRequest::get_var( MslsFields::FIELD_BLOG_ID )
			);

			$args = array(
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
			$args      = (array) apply_filters( 'msls_post_tag_suggest_args', $args );
			$post_type = MslsRequest::get( MslsFields::FIELD_POST_TYPE, '' );
			foreach ( get_terms( sanitize_text_field( $post_type ), $args ) as $term ) {
				/**
				 * Manipulates the term object before using it
				 *
				 * @param \StdClass $term
				 *
				 * @since 0.9.9
				 */
				$term = apply_filters( 'msls_post_tag_suggest_term', $term );

				if ( is_object( $term ) ) {
					$json->add( $term->term_id, $term->name );
				}
			}
			restore_current_blog();
		}

		wp_die( $json->encode() );
	}

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsPostTag
	 */
	public static function init() {
		$options    = msls_options();
		$collection = msls_blog_collection();
		$class      = $options->activate_autocomplete ? self::class : MslsPostTagClassic::class;
		$obj        = new $class( $options, $collection );

		$taxonomy = MslsContentTypes::create()->acl_request();
		if ( '' != $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields", array( $obj, 'add_input' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( $obj, 'edit_input' ), 10, 2 );
			add_action( "edited_{$taxonomy}", array( $obj, 'set' ) );
			add_action( "create_{$taxonomy}", array( $obj, 'set' ) );
		}

		return $obj;
	}

	/**
	 * Add the input fields to the add-screen of the taxonomies
	 *
	 * @param string $taxonomy
	 */
	public function add_input( string $taxonomy ): void {
		$title_format = '<h3>%s</h3>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>';

		$item_format = '<label for="msls_title_%1$s">%2$s</label>
			<input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/>
			<input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>';

		echo '<div class="form-field">';
		$this->the_input( null, $title_format, $item_format );
		echo '</div>';
	}

	/**
	 * Add the input fields to the edit-screen of the taxonomies
	 *
	 * @param \WP_Term $tag
	 * @param string   $taxonomy
	 */
	public function edit_input( \WP_Term $tag, string $taxonomy ): void {
		$title_format = '<tr>
			<th colspan="2">
			<strong>%s</strong>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>
			</th>
			</tr>';

		$item_format = '<tr class="form-field">
			<th scope="row">
			<label for="msls_title_%1$s">%2$s</label>
			</th>
			<td>
			<input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/>
			<input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>
			</td>
			</tr>';

		$this->the_input( $tag, $title_format, $item_format );
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
		static $count = 0;

		if ( $count > 0 ) {
			return false;
		}

		++$count;

		$blogs = $this->collection->get();
		if ( $blogs ) {
			$term_id = $tag->term_id ?? 0;
			$mydata  = MslsOptionsTax::create( $term_id );
			$type    = MslsContentTypes::create()->get_request();

			$this->maybe_set_linked_term( $mydata );

			printf( $title_format, $this->get_select_title(), $type );

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

				printf( $item_format, $blog->userblog_id, $icon, $language, $value, $title );

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
	 *
	 * @codeCoverageIgnore
	 */
	public function set( $term_id ) {
		if ( MslsContentTypes::create()->acl_request() ) {
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
