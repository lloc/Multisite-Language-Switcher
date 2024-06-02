<?php

namespace lloc\Msls;

class MslsAdminBar {

	protected string $icon_type;

	protected MslsBlogCollection $blog_collection;

	public function __construct( MslsOptions $options, MslsBlogCollection $blog_collection ) {
		$this->icon_type       = $options->get_icon_type();
		$this->blog_collection = $blog_collection;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$obj = new MslsAdminBar( msls_options(), msls_blog_collection() );

		if ( is_admin_bar_showing() ) {
			add_action( 'admin_bar_menu', array( $obj, 'update_admin_bar' ), 999 );
		}
	}

	/**
	 * Callback that updates the admin bar with the blog information
	 */
	public function update_admin_bar( \WP_Admin_Bar $wp_admin_bar ): void {
		foreach ( $this->blog_collection->get_plugin_active_blogs() as $blog ) {
			$title = $this->get_title( $blog, true );

			$title && $this->add_node( $wp_admin_bar, 'blog-' . $blog->userblog_id, $title );
		}

		$blog  = $this->blog_collection->get_current_blog();
		$title = $this->get_title( $blog );

		$title && $this->add_node( $wp_admin_bar, 'site-name', $title );
	}

	/**
	 * Adds node information to an existing node
	 */
	public function add_node( \WP_Admin_Bar $wp_admin_bar, string $node_id, string $title ): bool {
		$node = $wp_admin_bar->get_node( $node_id );
		if ( is_null( $node ) ) {
			return false;
		}

		$wp_admin_bar->add_node(
			array(
				'id'    => $node_id,
				'title' => $title,
			)
		);

		return true;
	}

	/**
	 * Gets a title with label orflag (depending on the settings) for the blog
	 *
	 * It uses a blavatar icon as prefix if $blavatar is set to true
	 */
	protected function get_title( ?MslsBlog $blog, bool $blavatar = false ): ?string {
		if ( is_null( $blog ) ) {
			return $blog;
		}

		$prefix = $blavatar ? $blog->get_blavatar() : '';

		return $prefix . $blog->get_title( $this->icon_type );
	}
}
