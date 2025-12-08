<?php

declare(strict_types=1);

/**
 * Deprecated: Get the output for using the links to the translations in your code.
 *
 * @deprecated 2.10.1 Use msls_get_switcher()
 *
 * @param mixed $attr
 *
 * @return string
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function get_the_msls( $attr ): string {
	_deprecated_function( __FUNCTION__, '2.10.1', 'msls_get_switcher' );

	return msls_get_switcher( $attr );
}

/**
 * Deprecated: Output the links to the translations in your template.
 *
 * @deprecated 2.10.1 Use msls_the_switcher()
 *
 * @param string[] $arr
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function the_msls( array $arr = array() ): void {
	_deprecated_function( __FUNCTION__, '2.10.1', 'msls_the_switcher' );
	msls_the_switcher( $arr );
}

/**
 * Deprecated: Gets the URL of the country flag-icon for a specific locale.
 *
 * @deprecated 2.10.1 Use msls_get_flag_url()
 *
 * @param string $locale
 *
 * @return string
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function get_msls_flag_url( string $locale ): string {
	_deprecated_function( __FUNCTION__, '2.10.1', 'msls_get_flag_url' );

	return msls_get_flag_url( $locale );
}

/**
 * Deprecated: Gets the description for a blog for a specific locale.
 *
 * @deprecated 2.10.1 Use msls_get_blog_description()
 *
 * @param string $locale
 * @param string $preset
 *
 * @return string
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function get_msls_blog_description( string $locale, string $preset = '' ): string {
	_deprecated_function( __FUNCTION__, '2.10.1', 'msls_get_blog_description' );

	return msls_get_blog_description( $locale, $preset );
}

/**
 * Deprecated: Gets the permalink for a translation of the current post in a given language.
 *
 * @deprecated 2.10.1 Use msls_get_permalink()
 *
 * @param string $locale
 * @param string $preset
 *
 * @return string
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
function get_msls_permalink( string $locale, string $preset = '' ): string {
	_deprecated_function( __FUNCTION__, '2.10.1', 'msls_get_permalink' );

	return msls_get_permalink( $locale, $preset );
}
