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
 *     if ( function_exists ( 'the_msls' ) )
 *         the_msls();
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
	return ( new \lloc\Msls\Options\Options() )->get_flag_url( $locale );
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
		$options = \lloc\Msls\Options\Options::create();
		$url     = $blog->get_url( $options );
	}

	return $url ?? $preset;
}

/**
 * Looks for the Blog instance for a specific locale
 *
 * @param string $locale
 *
 * @return \lloc\Msls\Blog\Blog|null
 */
function msls_blog( string $locale ): ?\lloc\Msls\Blog\Blog {
	return msls_blog_collection()->get_blog( $locale );
}

/**
 * Gets the Blog Collection instance
 *
 * @return \lloc\Msls\Blog\Collection
 */
function msls_blog_collection(): \lloc\Msls\Blog\Collection {
	return \lloc\Msls\Blog\Collection::instance();
}

/**
 * Gets the Options instance
 *
 * @return \lloc\Msls\Options\Options
 */
function msls_options(): \lloc\Msls\Options\Options {
	return \lloc\Msls\Options\Options::instance();
}

/**
 * Gets the MslsContentTypes instance
 *
 * @return \lloc\Msls\ContentTypes\ContentTypes
 */
function msls_content_types(): \lloc\Msls\ContentTypes\ContentTypes {
	return \lloc\Msls\ContentTypes\ContentTypes::create();
}

/**
 * Gets the MslsPostType instance
 *
 * @return \lloc\Msls\ContentTypes\PostType
 */
function msls_post_type(): \lloc\Msls\ContentTypes\PostType {
	return \lloc\Msls\ContentTypes\PostType::instance();
}

/**
 * Gets the MslsTaxonomy instance
 *
 * @return \lloc\Msls\ContentTypes\Taxonomy
 */
function msls_taxonomy(): \lloc\Msls\ContentTypes\Taxonomy {
	return \lloc\Msls\ContentTypes\Taxonomy::instance();
}

/**
 * Gets the Output instance
 *
 * @return \lloc\Msls\Frontend\Output
 */
function msls_output(): \lloc\Msls\Frontend\Output {
	return \lloc\Msls\Frontend\Output::create();
}

/**
 * Retrieves the OptionsPost instance.
 *
 * @param int $id
 *
 * @return \lloc\Msls\Options\Post\Post
 */
function msls_get_post( int $id ): \lloc\Msls\Options\Post\Post {
	return new \lloc\Msls\Options\Post\Post( $id );
}

/**
 * Retrieves the OptionsTax instance.
 *
 * Determines the current query based on conditional tags:
 * - is_category
 * - is_tag
 * - is_tax
 *
 * @param int $id
 * @return \lloc\Msls\Options\Tax\OptionsTaxInterface
 */
function msls_get_tax( int $id ): \lloc\Msls\Options\Tax\OptionsTaxInterface {
	return \lloc\Msls\Options\Tax\Tax::create( $id );
}

/**
 * Retrieves the OptionsQuery instance.
 *
 * Determines the current query based on conditional tags:
 * - is_day
 * - is_month
 * - is_year
 * - is_author
 * - is_post_type_archive
 *
 * @return ?\lloc\Msls\Options\Query\Query
 */
function msls_get_query(): ?\lloc\Msls\Options\Query\Query {
	return \lloc\Msls\Options\Query\Query::create();
}

/**
 * Trivial void function for actions that do not return anything.
 *
 * @return void
 */
function msls_return_void(): void {
}
