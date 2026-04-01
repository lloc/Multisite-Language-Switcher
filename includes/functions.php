<?php

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the output for using the links to the translations in your code
 *
 * @package Msls
 * @param mixed $attr
 * @return string
 */
function msls_get_switcher( $attr ): string {
	$arr = is_array( $attr ) ? $attr : array();
	$obj = apply_filters( 'msls_get_output', null );

	return ! is_null( $obj ) ? strval( $obj->set_tags( $arr ) ) : '';
}

/**
 * Output the links to the translations in your template
 *
 * You can call this function directly like that
 *
 *     if ( function_exists ( 'msls_the_switcher' ) )
 *         msls_the_switcher();
 *
 * or just use it as shortcode [sc_msls]
 *
 * @package Msls
 * @uses get_the_msls
 *
 * @param string[] $arr
 */
function msls_the_switcher( array $arr = array() ): void {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo msls_get_switcher( $arr );
}

/**
 * Gets the URL of the country flag-icon for a specific locale
 *
 * @param string $locale
 *
 * @return string
 */
function msls_get_flag_url( string $locale ): string {
	return ( new \lloc\Msls\MslsOptions() )->get_flag_url( $locale );
}

/**
 * Gets the description for a blog for a specific locale
 *
 * @param string $locale
 * @param string $preset
 *
 * @return string
 */
function msls_get_blog_description( string $locale, string $preset = '' ): string {
	$blog = msls_blog( $locale );

	return $blog ? $blog->get_description() : $preset;
}

/**
 * Gets the permalink for a translation of the current post in a given language
 *
 * @param string $locale
 * @param string $preset
 *
 * @return string
 */
function msls_get_permalink( string $locale, string $preset = '' ): string {
	$url  = null;
	$blog = msls_blog( $locale );

	if ( $blog ) {
		$options = \lloc\Msls\MslsOptions::create();
		$url     = $blog->get_url( $options );
	}

	return $url ?? $preset;
}

/**
 * Looks for the MslsBlog instance for a specific locale
 *
 * @param string $locale
 *
 * @return \lloc\Msls\MslsBlog|null
 */
function msls_blog( string $locale ): ?\lloc\Msls\MslsBlog {
	return msls_blog_collection()->get_blog( $locale );
}

/**
 * Gets the MslsBlogCollection instance
 *
 * @return \lloc\Msls\MslsBlogCollection
 */
function msls_blog_collection(): \lloc\Msls\MslsBlogCollection {
	return \lloc\Msls\MslsBlogCollection::instance();
}

/**
 * Gets the MslsOptions instance
 *
 * @return \lloc\Msls\MslsOptions
 */
function msls_options(): \lloc\Msls\MslsOptions {
	return \lloc\Msls\MslsOptions::instance();
}

/**
 * Gets the MslsContentTypes instance
 *
 * @return \lloc\Msls\MslsContentTypes
 */
function msls_content_types(): \lloc\Msls\MslsContentTypes {
	return \lloc\Msls\MslsContentTypes::create();
}

/**
 * Gets the MslsPostType instance
 *
 * @return \lloc\Msls\MslsPostType
 */
function msls_post_type(): \lloc\Msls\MslsPostType {
	return \lloc\Msls\MslsPostType::instance();
}

/**
 * Gets the MslsTaxonomy instance
 *
 * @return \lloc\Msls\MslsTaxonomy
 */
function msls_taxonomy(): \lloc\Msls\MslsTaxonomy {
	return \lloc\Msls\MslsTaxonomy::instance();
}

/**
 * Gets the MslsOutput instance
 *
 * @return \lloc\Msls\MslsOutput
 */
function msls_output(): \lloc\Msls\MslsOutput {
	return \lloc\Msls\MslsOutput::create();
}

/**
 * Retrieves the MslsOptionsPost instance.
 *
 * @param int $id
 * @return \lloc\Msls\MslsOptionsPost
 */
function msls_get_post( int $id ): \lloc\Msls\MslsOptionsPost {
	return new \lloc\Msls\MslsOptionsPost( $id );
}

/**
 * Retrieves the MslsOptionsTax instance.
 *
 * Determines the current query based on conditional tags:
 * - is_category
 * - is_tag
 * - is_tax
 *
 * @param int $id
 * @return \lloc\Msls\OptionsTaxInterface
 */
function msls_get_tax( int $id ): \lloc\Msls\OptionsTaxInterface {
	return \lloc\Msls\MslsOptionsTax::create( $id );
}

/**
 * Retrieves the MslsOptionsQuery instance.
 *
 * Determines the current query based on conditional tags:
 * - is_day
 * - is_month
 * - is_year
 * - is_author
 * - is_post_type_archive
 *
 * @return ?\lloc\Msls\MslsOptionsQuery
 */
function msls_get_query(): ?\lloc\Msls\MslsOptionsQuery {
	return \lloc\Msls\MslsOptionsQuery::create();
}

/**
 * Gets structured language data for all available translations
 *
 * Returns an array of language entries with locale, alpha2 code, URL,
 * label, flag URL, and whether it's the current language. This provides
 * programmatic access to the same data used by the switcher output,
 * without any HTML rendering.
 *
 * @param bool $filter When true, only returns languages with existing translations
 *
 * @return array<int, array{locale: string, alpha2: string, url: string, label: string, flag_url: string, current: bool}>
 */
function msls_get_languages( bool $filter = false ): array {
	return msls_output()->get_languages( $filter );
}

/**
 * Trivial void function for actions that do not return anything.
 *
 * @return void
 */
function msls_return_void(): void {
}
