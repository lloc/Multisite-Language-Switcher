<?php declare( strict_types=1 );

namespace lloc\Msls\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use lloc\Msls\Options\Options;
use lloc\Msls\Options\OptionsInterface;

/**
 * Keeps the page of a paginated request in the links of the switcher
 *
 * @package Msls
 */
class Pagination {

	const MSLS_PRESERVE_HOOK = 'msls_preserve_pagination';

	const MSLS_MAX_PAGES_HOOK = 'msls_pagination_max_pages';

	const MSLS_GET_HOOK = 'msls_pagination_get';

	/**
	 * Page of a paginated archive
	 *
	 * @var int
	 */
	protected int $paged;

	/**
	 * Page of a post which is split by <!--nextpage-->
	 *
	 * @var int
	 */
	protected int $page;

	/**
	 * Reads the pagination of the current request
	 */
	public static function create(): Pagination {
		return new self( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	}

	/**
	 * @param int $paged
	 * @param int $page
	 */
	public function __construct( int $paged = 0, int $page = 0 ) {
		$this->paged = max( 0, $paged );
		$this->page  = max( 0, $page );
	}

	/**
	 * Checks if the request is paginated and the user did not opt out
	 */
	public function is_active(): bool {
		if ( $this->paged < 2 && $this->page < 2 ) {
			return false;
		}

		/**
		 * Returns false to link to the first page as it was before 3.1
		 *
		 * @param bool       $preserve
		 * @param Pagination $pagination
		 *
		 * @since 3.1.0
		 */
		return (bool) apply_filters( self::MSLS_PRESERVE_HOOK, true, $this ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- constant value is already prefixed with "msls_".
	}

	/**
	 * Gets the kind of pagination of the current request
	 */
	public function get_context(): string {
		return $this->paged > 1 ? Options::PAGINATION_ARCHIVE : Options::PAGINATION_SINGLE;
	}

	/**
	 * Gets the page of the current request
	 */
	public function get_page(): int {
		return $this->paged > 1 ? $this->paged : $this->page;
	}

	/**
	 * Adds the page of the current request to a link of the switcher
	 *
	 * @param string           $url
	 * @param OptionsInterface $options
	 * @param string           $language
	 *
	 * @return string
	 */
	public function get( string $url, OptionsInterface $options, string $language ): string {
		if ( '' === $url || ! $this->is_active() ) {
			return $url;
		}

		$page    = $this->get_page();
		$context = $this->get_context();

		$max = $options instanceof Options ? $options->get_max_pages( $language, $context ) : 0;

		/**
		 * Returns the number of pages the blog has for the current request
		 *
		 * @param int              $max
		 * @param OptionsInterface $options
		 * @param string           $language
		 * @param string           $context
		 *
		 * @since 3.1.0
		 */
		$max = (int) apply_filters( self::MSLS_MAX_PAGES_HOOK, $max, $options, $language, $context ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- constant value is already prefixed with "msls_".

		if ( $page <= $max ) {
			$url = Options::PAGINATION_ARCHIVE === $context
				? $this->add_paged( $url, $page )
				: $this->add_page( $url, $page );
		}

		/**
		 * Returns the link of the switcher with the page of the current request
		 *
		 * @param string           $url
		 * @param int              $page
		 * @param OptionsInterface $options
		 * @param string           $language
		 *
		 * @since 3.1.0
		 */
		return (string) apply_filters( self::MSLS_GET_HOOK, $url, $page, $options, $language ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- constant value is already prefixed with "msls_".
	}

	/**
	 * Adds the page of an archive
	 *
	 * @param string $url
	 * @param int    $page
	 *
	 * @return string
	 */
	protected function add_paged( string $url, int $page ): string {
		if ( ! $this->using_permalinks() ) {
			return (string) add_query_arg( 'paged', $page, $url );
		}

		return $this->add_path( $url, $this->get_pagination_base() . '/' . $page, Options::PAGINATION_ARCHIVE );
	}

	/**
	 * Adds the page of a post which is split by <!--nextpage-->
	 *
	 * @param string $url
	 * @param int    $page
	 *
	 * @return string
	 */
	protected function add_page( string $url, int $page ): string {
		if ( ! $this->using_permalinks() ) {
			return (string) add_query_arg( 'page', $page, $url );
		}

		// A static front page is the one exception where WordPress uses the pagination base here, see _wp_link_page().
		$path = untrailingslashit( $url ) === untrailingslashit( home_url( '/' ) )
			? $this->get_pagination_base() . '/' . $page
			: (string) $page;

		return $this->add_path( $url, $path, Options::PAGINATION_SINGLE );
	}

	/**
	 * Appends a path to the link and keeps a query string in place
	 *
	 * @param string $url
	 * @param string $path
	 * @param string $context
	 *
	 * @return string
	 */
	protected function add_path( string $url, string $path, string $context ): string {
		$query = '';
		$pos   = strpos( $url, '?' );

		if ( false !== $pos ) {
			$query = substr( $url, $pos );
			$url   = substr( $url, 0, $pos );
		}

		return user_trailingslashit( trailingslashit( $url ) . $path, $context ) . $query;
	}

	/**
	 * Checks the permalink structure of the blog the link belongs to
	 */
	protected function using_permalinks(): bool {
		return '' !== (string) get_option( 'permalink_structure', '' );
	}

	/**
	 * Gets the rewrite base of a paginated archive
	 */
	protected function get_pagination_base(): string {
		global $wp_rewrite;

		return isset( $wp_rewrite->pagination_base ) ? (string) $wp_rewrite->pagination_base : 'page';
	}
}
