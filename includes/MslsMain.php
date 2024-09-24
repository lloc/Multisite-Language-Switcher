<?php

namespace lloc\Msls;

use lloc\Msls\Component\Component;

/**
 * Abstraction for the hook classes
 *
 * @package Msls
 */
class MslsMain {

	/**
	 * Instance of options
	 *
	 * @var MslsOptions
	 */
	protected $options;

	/**
	 * Collection of blog objects
	 *
	 * @var MslsBlogCollection
	 */
	protected $collection;

	/**
	 * Constructor
	 *
	 * @param MslsOptions        $options
	 * @param MslsBlogCollection $collection
	 */
	final public function __construct( MslsOptions $options, MslsBlogCollection $collection ) {
		$this->options    = $options;
		$this->collection = $collection;
	}

	public static function create(): object {
		return new static( msls_options(), msls_blog_collection() );
	}

	/**
	 * Prints a message in the error log if WP_DEBUG is true
	 *
	 * @param mixed $message
	 */
	public function debugger( $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				$message = print_r( $message, true );
			}

			error_log( 'MSLS Debug: ' . $message );
		}
	}

	/**
	 * Get the input array
	 *
	 * @param int $object_id
	 *
	 * @return array<string, int>
	 */
	public function get_input_array( $object_id ): array {
		$arr = array();

		$current_blog = $this->collection->get_current_blog();
		if ( ! is_null( $current_blog ) ) {
			$arr[ $current_blog->get_language() ] = (int) $object_id;
		}

		$input_post = filter_input_array( INPUT_POST );
		if ( ! is_array( $input_post ) ) {
			return $arr;
		}

		$offset = strlen( Component::INPUT_PREFIX );
		foreach ( $input_post as $key => $value ) {
			if ( false === strpos( $key, Component::INPUT_PREFIX ) || empty( $value ) ) {
				continue;
			}

			$arr[ substr( $key, $offset ) ] = intval( $value );
		}

		return $arr;
	}

	/**
	 * Checks if the current input comes from the autosave-functionality
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_autosave( $post_id ): bool {
		return ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id );
	}

	/**
	 * Checks for the nonce in the INPUT_POST
	 *
	 * @return boolean
	 */
	public function verify_nonce(): bool {
		return MslsRequest::has_var( MslsFields::FIELD_MSLS_NONCENAME ) && wp_verify_nonce( MslsRequest::get_var( MslsFields::FIELD_MSLS_NONCENAME ), MslsPlugin::path() );
	}

	/**
	 * Delete
	 *
	 * @param int $object_id
	 *
	 * @codeCoverageIgnore
	 */
	public function delete( $object_id ): void {
		$this->save( $object_id, MslsOptionsPost::class );
	}

	/**
	 * Save
	 *
	 * @param int    $object_id
	 * @param string $class_name
	 *
	 * @codeCoverageIgnore
	 */
	protected function save( $object_id, $class_name ): void {
		if ( has_action( 'msls_main_save' ) ) {
			/**
			 * Calls completely customized save-routine
			 *
			 * @param int $object_id
			 * @param string $class_name
			 *
			 * @since 0.9.9
			 */
			do_action( 'msls_main_save', $object_id, $class_name );

			return;
		}

		if ( ! $this->collection->has_current_blog() ) {
			$this->debugger( 'BlogCollection returns false when calling has_current_blog.' );

			return;
		}

		$language = $this->collection->get_current_blog()->get_language();
		$msla     = new MslsLanguageArray( $this->get_input_array( $object_id ) );
		$options  = new $class_name( $object_id );
		$temp     = $options->get_arr();

		if ( 0 != $msla->get_val( $language ) ) {
			$options->save( $msla->get_arr( $language ) );
		} else {
			$options->delete();
		}

		foreach ( $this->collection->get() as $blog ) {
			switch_to_blog( $blog->userblog_id );

			$language = $blog->get_language();
			$larr_id  = $msla->get_val( $language );

			if ( 0 != $larr_id ) {
				$options = new $class_name( $larr_id );
				$options->save( $msla->get_arr( $language ) );
			} elseif ( isset( $temp[ $language ] ) ) {
				$options = new $class_name( $temp[ $language ] );
				$options->delete();
			}

			restore_current_blog();
		}
	}
}
