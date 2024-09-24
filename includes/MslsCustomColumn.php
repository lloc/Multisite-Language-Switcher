<?php

namespace lloc\Msls;

use lloc\Msls\Component\Component;

/**
 * Handling of existing/not existing translations in the backend listings of
 * various post types
 *
 * @package Msls
 */
class MslsCustomColumn extends MslsMain {

	public static function init(): void {
		$options    = msls_options();
		$collection = msls_blog_collection();

		( new static( $options, $collection ) )->add_hooks();
	}

	protected function add_hooks(): void {
		if ( $this->options->is_excluded() ) {
			return;
		}

		$post_type = msls_post_type()->get_request();

		if ( ! empty( $post_type ) ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'th' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'td' ), 10, 2 );
			add_action( 'trashed_post', array( $this, 'delete' ) );
		}
	}

	/**
	 * Table header
	 *
	 * @param string[] $columns
	 *
	 * @return string[]
	 */
	public function th( array $columns ) {
		$blogs = $this->collection->get();
		if ( $blogs ) {
			$html = '';
			foreach ( $blogs as $blog ) {
				$language  = $blog->get_language();
				$icon_type = $this->options->get_icon_type();

				$icon = ( new MslsAdminIcon() )->set_language( $language )->set_icon_type( $icon_type );

				if ( $post_id = get_the_ID() ) {
					$icon->set_id( $post_id );
					$icon->set_origin_language( 'it_IT' );
				}

				$html .= '<span class="msls-icon-wrapper ' . esc_attr( $icon_type ) . '">';
				$html .= $icon->get_icon();
				$html .= '</span>';
			}
			$columns['mslscol'] = $html;
		}

		return $columns;
	}

	/**
	 * Table body
	 *
	 * @param string $column_name
	 * @param int    $item_id
	 */
	public function td( $column_name, $item_id ): void {
		if ( 'mslscol' == $column_name ) {
			$blogs           = $this->collection->get();
			$origin_language = MslsBlogCollection::get_blog_language();
			if ( $blogs ) {
				$mydata = MslsOptions::create( $item_id );
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->userblog_id );

					$language = $blog->get_language();

					$icon = MslsAdminIcon::create();
					$icon->set_language( $language );
					$icon->set_id( $item_id );
					$icon->set_origin_language( $origin_language );

					$icon->set_icon_type( 'action' );

					if ( $mydata->has_value( $language ) ) {
						$icon->set_href( (int) $mydata->$language );
					}

					echo wp_kses(
						sprintf(
							'<span class="msls-icon-wrapper %1$s">%2$s</span>',
							esc_attr( $this->options->get_icon_type() ),
							$icon->get_a()
						),
						Component::get_allowed_html()
					);

					restore_current_blog();
				}
			}
		}
	}
}
