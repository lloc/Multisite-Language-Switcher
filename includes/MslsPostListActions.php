<?php declare( strict_types=1 );

namespace lloc\Msls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the "Add from Translation" entry point on post list screens.
 *
 * Bootstraps on load-edit.php, enqueues the picker script and prints the
 * Thickbox template in the admin footer so clicking the injected
 * page-title-action opens a modal backed by the Quick Create REST API.
 *
 * @package Msls
 */
class MslsPostListActions {

	const INLINE_ID = 'msls-translation-picker';

	const HANDLE = 'msls-translation-picker';

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$options = msls_options();
		if ( $options->is_excluded() ) {
			return;
		}

		$post_type = msls_post_type()->get_request();
		if ( empty( $post_type ) ) {
			return;
		}

		$obj = new self();

		add_action( 'admin_enqueue_scripts', array( $obj, 'enqueue' ) );
		add_action( 'admin_footer-edit.php', array( $obj, 'render_modal' ) );
	}

	/**
	 * Returns the list of source blogs the picker can offer for the
	 * current user, i.e., all other blogs where MSLS is active.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_source_blogs(): array {
		$collection = msls_blog_collection();
		$blogs      = array();

		foreach ( $collection->get() as $blog ) {
			if ( ! $collection->is_plugin_active( $blog->userblog_id ) ) {
				continue;
			}

			$blogs[] = array(
				'blog_id'     => (int) $blog->userblog_id,
				'description' => (string) $blog->get_description(),
				'language'    => $blog->get_language(),
			);
		}

		return $blogs;
	}

	/**
	 * Enqueues the picker script and localizes the bootstrap payload.
	 */
	public function enqueue(): void {
		$blogs = $this->get_source_blogs();
		if ( empty( $blogs ) ) {
			return;
		}

		add_thickbox();

		$ver    = defined( 'MSLS_PLUGIN_VERSION' ) ? constant( 'MSLS_PLUGIN_VERSION' ) : false;
		$folder = defined( 'SCRIPT_DEBUG' ) && constant( 'SCRIPT_DEBUG' ) ? 'src' : 'assets/js';

		wp_enqueue_script(
			self::HANDLE,
			MslsPlugin::plugins_url( "$folder/msls-translation-picker.js" ),
			array( 'jquery', 'wp-api-fetch' ),
			$ver,
			array( 'in_footer' => true )
		);

		wp_localize_script(
			self::HANDLE,
			'mslsTranslationPicker',
			array(
				'inlineId'     => self::INLINE_ID,
				'targetBlogId' => (int) get_current_blog_id(),
				'postType'     => msls_post_type()->get_request(),
				'sourceBlogs'  => $blogs,
				'i18n'         => array(
					'buttonLabel' => __( 'Add from Translation', 'multisite-language-switcher' ),
					'modalTitle'  => __( 'Add Post from Translation', 'multisite-language-switcher' ),
					'selectBlog'  => __( 'Source blog', 'multisite-language-switcher' ),
					'placeholder' => __( 'Select a source blog…', 'multisite-language-switcher' ),
					'loading'     => __( 'Loading posts…', 'multisite-language-switcher' ),
					'empty'       => __( 'All posts are already translated.', 'multisite-language-switcher' ),
					'error'       => __( 'Something went wrong. Please try again.', 'multisite-language-switcher' ),
					'creating'    => __( 'Creating draft…', 'multisite-language-switcher' ),
				),
			)
		);
	}

	/**
	 * Prints the hidden Thickbox modal template in the footer.
	 *
	 * The button itself is appended to the page-title-action area by JS
	 * because WordPress exposes no server-side hook at that location.
	 */
	public function render_modal(): void {
		if ( empty( $this->get_source_blogs() ) ) {
			return;
		}
		?>
		<div id="<?php echo esc_attr( self::INLINE_ID ); ?>" style="display:none;">
			<div class="msls-tp">
				<p class="msls-tp-blog-row">
					<label for="msls-tp-blog"><?php esc_html_e( 'Source blog', 'multisite-language-switcher' ); ?></label>
					<select id="msls-tp-blog">
						<option value=""><?php esc_html_e( 'Select a source blog…', 'multisite-language-switcher' ); ?></option>
						<?php foreach ( $this->get_source_blogs() as $blog ) : ?>
							<option value="<?php echo esc_attr( (string) $blog['blog_id'] ); ?>">
								<?php echo esc_html( $blog['description'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>
				<div class="msls-tp-status" aria-live="polite"></div>
				<ul class="msls-tp-list" role="list"></ul>
			</div>
		</div>
		<?php
	}
}
