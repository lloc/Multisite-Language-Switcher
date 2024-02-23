<?php

namespace lloc\Msls\Map;

use lloc\Msls\MslsBlogCollection;

/**
 * Class HrefLang
 * @package lloc\Msls\Map
 */
class HrefLang {

	/**
	 * @var string[]
	 */
	protected $map = [];

	/**
	 * @param MslsBlogCollection $blogs
	 */
	public function __construct( MslsBlogCollection $blogs ) {
		foreach ( self::map_languages_from( $blogs ) as $alpha2 => $languages ) {
			if ( 1 == count( $languages ) ) {
				$this->map[ current( $languages) ][ key( $languages ) ] = $alpha2;
			} else {
				foreach ( $languages as $blog_id => $language ) {
					$this->map[ $language ][ $blog_id ] = $this->get_hreflang( $language );
				}
			}
		}
	}

	/**
	 * @param MslsBlogCollection $blogs
	 *
	 * @return array<string,array<int,string>>
	 */
	public static function map_languages_from( MslsBlogCollection $blogs ): array {
		$map = [];

		foreach ( $blogs->get_objects() as $blog ) {
			$map[ $blog->get_alpha2() ][ $blog->userblog_id ] = $blog->get_language();
		}

		return $map;
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	protected function get_hreflang( string $language ): string {
		$parts = explode( '_', $this->map[ $language ] ?? $language );

		return 1 === count( $parts ) ? $parts[0] : $parts[0] . '-' . $parts[1];
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get( string $language, int $blog_id ): string {
		if ( ! has_filter( 'msls_head_hreflang' ) ) {
			return $this->map[ $language ][ $blog_id ] ?? $language;
		}

		/**
		 * Overrides the hreflang value
		 *
		 * @param string $language
		 * @param int $blog_id
		 *
		 * @since 0.9.9
		 */
		return (string) apply_filters( 'msls_head_hreflang', $language, $blog_id );
	}

}