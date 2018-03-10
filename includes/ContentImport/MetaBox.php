<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsRegistryInstance;

class MetaBox extends MslsRegistryInstance {

	/**
	 * Renders the content import metabox.
	 */
	public function render() {
		$post            = get_post();
		$mydata          = new MslsOptionsPost( $post->ID );
		$languages       = MslsOptionsPost::instance()->get_available_languages();
		$current         = MslsBlogCollection::get_blog_language( get_current_blog_id() );
		$languages       = array_diff_key( $languages, array( $current => $current ) );
		$input_lang      = isset( $_GET['msls_lang'] ) ? $_GET['msls_lang'] : null;
		$input_id        = isset( $_GET['msls_id'] ) ? $_GET['msls_id'] : null;
		$has_input       = null !== $input_lang && null !== $input_id;
		$blogs           = MslsBlogCollection::instance();
		$available       = array_filter( array_map( function ( $lang ) use ( $mydata ) {
			return $mydata->{$lang};
		}, array_keys( $languages ) ) );
		$has_translation = count( $available ) >= 1;

		if ( $has_input || $has_translation ) {
			add_thickbox();
			$label_template = __( 'Import content from %s', 'multisite-language-switcher' );
			$output         = '<fieldset>';
			$output         .= '<legend>'
			                   . esc_html__( 'Warning! This will override and replace all the post content with the content from the source post!',
					'multisite-language-switcher' )
			                   . '</legend>';
			foreach ( $languages as $language => $label ) {
				$id    = $mydata->language;
				$blog  = $blogs->get_blog_id( $language );
				$label = sprintf( $label_template, $label );
				if ( null === $id && $has_input && $input_lang === $language ) {
					$id   = $input_id;
					$blog = $blogs->get_blog_id( $language );
				}
				if ( null !== $id ) {
					$output .= sprintf( '<a class="button-primary thickbox" href="%s">%s</a>',
						$this->inline_thickbox_url( $blog, $id, $language ),
						$label
					);
				}
			}
			$output .= '</fieldset>';
			$output .= $this->inline_thickbox_html();
		} else {
			$output = '<p>' .
			          esc_html__( 'No translated versions linked to this post: import content functionality is disabled.',
				          'multisite-language-switcher' )
			          . '</p>';
		}

		echo $output;
	}

	protected function inline_thickbox_url( $blog, $id, $language ) {
		return esc_url(
			'#TB_inline' . add_query_arg( [
				'msls_blog' => $blog,
				'msls_id'   => $id,
				'msls_lang' => $language,
				'width'     => 600,
				'height'    => 550,
				'inlineId'  => 'msls-import-dialog',
			], '' )
		);
	}

	protected function inline_thickbox_html() {
		$out = '<div style="display: none;" id="msls-import-dialog"><p>Import stuff</p></div>';

		return $out;
	}
}