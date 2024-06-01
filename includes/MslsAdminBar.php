<?php

namespace lloc\Msls;

class MslsAdminBar {


	/**
	 * @codeCoverageIgnore
	 */
	public function init(): void {
		if ( is_admin_bar_showing() ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'update_admin_bar' ), 999 );
		}
	}

	/**
	 * @param $wp_admin_bar
	 *
	 * @return void
	 */
	public static function update_admin_bar( \WP_Admin_Bar $wp_admin_bar ): void {
		$icon_type = msls_options()->get_icon_type();

		$blog_collection = msls_blog_collection();
		foreach ( $blog_collection->get_plugin_active_blogs() as $blog ) {
			$title = $blog->get_blavatar() . $blog->get_title( $icon_type );

			$wp_admin_bar->add_node(
				array(
					'id'    => 'blog-' . $blog->userblog_id,
					'title' => $title,
				)
			);
		}

		$blog = $blog_collection->get_current_blog();
		if ( is_object( $blog ) && method_exists( $blog, 'get_title' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'site-name',
					'title' => $blog->get_title( $icon_type ),
				)
			);
		}
	}
}
