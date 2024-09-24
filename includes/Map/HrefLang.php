<?php

namespace lloc\Msls\Map;

use lloc\Msls\MslsBlogCollection;

/**
 * Class HrefLang
 *
 * @package lloc\Msls\Map
 */
class HrefLang {

	/**
	 * @var array<string, string>
	 */
	protected $map = array();

	/**
	 * @param MslsBlogCollection $blogs
	 */
	public function __construct( MslsBlogCollection $blogs ) {
		$map = array();
		foreach ( $blogs->get_objects() as $blog ) {
			$map[ $blog->get_alpha2() ][] = $blog->get_language();
		}

		foreach ( $map as $alpha2 => $languages ) {
			if ( 1 == count( $languages ) ) {
				$this->map[ $languages[0] ] = $alpha2;
			} else {
				foreach ( $languages as $language ) {
					$this->map[ $language ] = $this->get_hreflang( $language );
				}
			}
		}
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
	public function get( string $language ): string {
		if ( ! has_filter( 'msls_head_hreflang' ) ) {
			return $this->map[ $language ] ?? $language;
		}

		/**
		 * Overrides the hreflang value
		 *
		 * @param string $language
		 *
		 * @since 0.9.9
		 */
		return (string) apply_filters( 'msls_head_hreflang', $language );
	}
}
