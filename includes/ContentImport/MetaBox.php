<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\Importers\ImportersFactory;
use lloc\Msls\ContentImport\Importers\Map;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptionsPost;
use lloc\Msls\MslsPlugin;
use lloc\Msls\MslsRegistryInstance;

class MetaBox extends MslsRegistryInstance {
	protected $data = [];

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
		$blogs           = msls_blog_collection();
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
				$id    = $mydata->{$language};
				$blog  = $blogs->get_blog_id( $language );
				$label = sprintf( $label_template, $label );
				if ( null === $id && $has_input && $input_lang === $language ) {
					$id   = $input_id;
					$blog = $blogs->get_blog_id( $language );
				}
				if ( null !== $id ) {
					$this->data = [
						'msls_import'  => "{$blog}|{$id}",
					];
					$output .= sprintf( '<a class="button button-primary thickbox" href="%s" title="%s">%s</a>',
						$this->inline_thickbox_url( $this->data ),
						$label,
						$label
					);
				}
			}
			$output .= '</fieldset>';
		} else {
			$output = '<p>' .
			          esc_html__( 'No translated versions linked to this post: import content functionality is disabled.',
				          'multisite-language-switcher' )
			          . '</p>';
		}

		echo $output;
	}

	protected function inline_thickbox_url( array $data = [] ) {
		$args = array_merge( [
			'modal'    => true,
			'width'    => 770, // meh, just a guess on *most* devices
			'height'   => 770,
			'inlineId' => 'msls-import-dialog-' . str_replace( '|', '-', $data['msls_import'] ),
		], $data );

		return esc_url(
			'#TB_inline' . add_query_arg( $args, '' )
		);
	}

	public function print_modal_html() {
		echo $this->inline_thickbox_html( true, $this->data );
	}

	protected function inline_thickbox_html( $echo = true, array $data = [] ) {
		if ( ! isset( $data['msls_import'] ) ) {
			return '';
		}

		$slug = str_replace( '|', '-', $data['msls_import'] );

		ob_start();
		?>
        <div style="display: none;" id="msls-import-dialog-<?php echo esc_attr( $slug ) ?>">
            <h3><?php esc_html_e( 'Select what should be imported and how', 'multisite-language-switcher' ) ?></h3>

            <form action="<?php echo add_query_arg( [] ) ?>" method="post">

				<?php wp_nonce_field( MslsPlugin::path(), 'msls_noncename' ); ?>

				<?php foreach ( $data as $key => $value ) : ?>
                    <input type="hidden" name="<?php echo esc_attr( $key ) ?>" value="<?php echo esc_attr( $value ) ?>">
				<?php endforeach; ?>

				<?php /** @var ImportersFactory $factory */
				foreach ( Map::instance()->factories() as $slug => $factory ) : ?>
					<?php $details = $factory->details() ?>
                    <h4><?php echo esc_html( $details->name ) ?></h4>
					<?php if ( empty( $details->importers ) ) : ?>
                        <p><?php esc_html_e( 'No importers available for this type of content.', 'multisite-language-switcher' ) ?></p>
					<?php else: ?>
                        <ul>
                            <li>
                                <label>
                                    <input type="radio" name="msls_importers[<?php echo esc_attr( $details->slug ) ?>]">
									<?php esc_html_e( 'Off - Do not import this type of content in the destination post.', 'multisite-language-switcher' ) ?>
                                </label>
                            </li>
							<?php foreach ( $details->importers as $importer_slug => $importer_info ) : ?>
                                <li>
                                    <label>
                                        <input type="radio" name="msls_importers[<?php echo esc_attr( $details->slug ) ?>]"
                                               value="<?php echo esc_attr( $importer_slug ) ?>"
											<?php checked( $details->selected, $importer_slug ) ?>
                                        >
										<?php echo( esc_html( sprintf( '%s -  %s', $importer_info->name, $importer_info->description ) ) ) ?>
                                    </label>
                                </li>
							<?php endforeach; ?>
                        </ul>
					<?php endif; ?>
				<?php endforeach; ?>

                <div>
                    <input
                            type="submit"
                            class="button button-primary"
                            value="<?php esc_html_e( 'Import Content', 'multisite-language-switcher' ) ?>"
                    >
                </div>
            </form>
        </div>

		<?php
		$html = ob_get_clean();

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
}
