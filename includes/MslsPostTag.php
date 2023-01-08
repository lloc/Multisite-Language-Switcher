<?php
/**
 * MslsPostTag
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use StdClass;
use WP_Term;

/**
 * Post Tag
 * @package Msls
 */
class MslsPostTag extends MslsMain {

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsPostTag
	 */
	public static function init(): HookInterface {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();

		if ( $options->activate_autocomplete	) {
			$obj = new self( $options, $collection );
		}
		else {
			$obj = new MslsPostTagClassic( $options, $collection );
		}

		$taxonomy = MslsContentTypes::create()->acl_request();
		if ( '' != $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields",  [ $obj, 'add_input' ] );
			add_action( "{$taxonomy}_edit_form_fields", [ $obj, 'edit_input' ] );
			add_action( "edited_{$taxonomy}", [ $obj, 'set' ] );
			add_action( "create_{$taxonomy}", [ $obj, 'set' ] );
		}

		return $obj;
	}

	/**
	 * Suggest
	 *
	 * Echo a JSON-ified array of posts of the given post-type and
	 * the requested search-term and then die silently
	 */
	public static function suggest(): void {
		$json = new MslsJson();

		if ( filter_has_var( INPUT_POST, 'blog_id' ) ) {
			$blog_id = filter_input( INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT );

			switch_to_blog( $blog_id );

			$args = [
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 10,
				'hide_empty' => false,
			];

			if ( filter_has_var( INPUT_POST, 's' ) ) {
				$args['search'] = sanitize_text_field(
					filter_input( INPUT_POST, 's' )
				);
			}

			if ( filter_has_var( INPUT_POST, 'post_type' ) ) {
				$args['taxonomy'] = sanitize_text_field(
					filter_input( INPUT_POST, 'post_type' )
				);
			}

			/**
			 * Overrides the query-args for the suggestion fields
			 * @since 0.9.9
			 * @param array $args
			 */
			$args = (array) apply_filters( 'msls_post_tag_suggest_args', $args );

			foreach ( get_terms( $args ) as $term ) {
				/**
				 * Manipulates the term object before using it
				 * @since 0.9.9
				 * @param StdClass $term
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
	 * Add the input fields to the add-screen of the taxonomies
	 *
	 * @param StdClass $tag
	 */
	public function add_input( $tag ): void {
		$title_format = '<h3>%s</h3>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>';

		$item_format = '<label for="msls_title_%1$s">%2$s</label>
			<input type="hidden" id="msls_id_%1$s" name="msls_input_%3$s" value="%4$s"/>
			<input class="msls_title" id="msls_title_%1$s" name="msls_title_%1$s" type="text" value="%5$s"/>';

		echo '<div class="form-field">';
		$this->the_input( $tag, $title_format, $item_format );
		echo '</div>';
	}

	/**
	 * Add the input fields to the edit-screen of the taxonomies
	 *
	 * @param StdClass $tag
	 */
	public function edit_input( $tag ): void {
		$title_format = '<tr>
			<th colspan="2">
			<strong>%s</strong>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="%s"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>
			</th>
			</tr>';

		$item_format = '<tr class="form-field">
			<th scope="row" vertical-align="top">
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
	 * @param mixed $tag
	 * @param string $title_format
	 * @param string $item_format
	 *
	 * @return boolean
	 */
	public function the_input( $tag, string $title_format, string $item_format ): bool {
		$term_id = ( is_object( $tag ) ? $tag->term_id : 0 );

		$blogs   = $this->collection->get();
		if ( $blogs ) {
			$my_data = MslsOptionsTax::create( $term_id );

			$this->maybe_set_linked_term( $my_data );

			$type    = MslsContentTypes::create()->get_request();

			printf(
				$title_format,
				apply_filters( 
					'msls_term_select_title',
					__( 'Multisite Language Switcher', 'multisite-language-switcher' )
				),
				$type
			);
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->userblog_id );

				$language = $blog->get_language();
				$icon     = MslsAdminIcon::create()
					->set_language( $language )
					->set_icon_type( 'flag' );

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
					$item_format,
					$blog->userblog_id,
					$icon,
					$language,
					$value,
					$title
				);
				restore_current_blog();
			}
			return true;
		}
		return false;
	}

	/**
	 * Set calls the save method if taxonomy is set
	 * @param int $term_id
	 *
 	 * @return void
	 */
	public function set( int $term_id ): void {
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
	public function maybe_set_linked_term( MslsOptionsTax $mydata ): MslsOptionsTax {
		if ( ! isset( $_GET['msls_id'], $_GET['msls_lang'] ) ) {
			return $mydata;
		}

		$origin_term_id = (int) $_GET['msls_id'];

		$origin_lang = trim( $_GET['msls_lang'] );
		if ( isset( $mydata->{$origin_lang} ) ) {
			return $mydata;
		}

		$origin_blog_id = $this->collection->get_blog_id( $origin_lang );
		if ( null === $origin_blog_id ) {
			return $mydata;
		}

		switch_to_blog( (int) $origin_blog_id );
		$origin_term = get_term( $origin_term_id, $mydata->base );
		restore_current_blog();

		if ( ! $origin_term instanceof WP_Term ) {
			return $mydata;
		}

		$mydata->{$origin_lang} = $origin_term_id;

		return $mydata;
	}

}
