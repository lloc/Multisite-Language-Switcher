<?php
/**
 * MslsMain
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

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
	 * @param MslsOptions $options
	 * @param MslsBlogCollection $collection
	 */
	public function __construct( MslsOptions $options, MslsBlogCollection $collection ) {
		$this->options    = $options;
		$this->collection = $collection;
	}

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return static
	 */
	public static function init() {
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
	 * @return array
	 */
	public function get_input_array( $object_id ) {
		$arr = [];

		$current_blog = $this->collection->get_current_blog();
		if ( ! is_null( $current_blog ) ) {
			$arr[ $current_blog->get_language() ] = (int) $object_id;
		}

		$input_post = filter_input_array( INPUT_POST );
		if ( ! is_array( $input_post ) ) {
			return $arr;
		}

		foreach ( $input_post as $k => $v ) {
			list ( $key, $value ) = $this->get_input_value( $k, $v );
			if ( $value ) {
				$arr[ $key ] = $value;
			}
		}

		return $arr;
	}

	/**
	 * Prepare input key/value-pair
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return array
	 */
	protected function get_input_value( $key, $value ) {
		if ( false === strpos( $key, 'msls_input_' ) || empty( $value ) ) {
			return [ '', 0 ];
		}

		return [ substr( $key, 11 ), intval( $value ) ];
	}

	/**
	 * Checks if the current input comes from the autosave-functionality
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_autosave( $post_id ) {
		return ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id );
	}

	/**
	 * Checks for the nonce in the INPUT_POST
	 *
	 * @return boolean
	 */
	public function verify_nonce() {
		return (
			filter_has_var( INPUT_POST, 'msls_noncename' ) &&
			wp_verify_nonce( filter_input( INPUT_POST, 'msls_noncename' ), MslsPlugin::path() )
		);
	}

	/**
	 * Delete
	 *
	 * @param int $object_id
	 *
	 * @codeCoverageIgnore
	 */
	public function delete( $object_id ) {
		$this->save( $object_id, MslsOptionsPost::class );
	}

	/**
	 * Save
	 *
	 * @param int $object_id
	 * @param string $class
	 *
	 * @codeCoverageIgnore
	 */
	protected function save( $object_id, $class ) {
		if ( has_action( 'msls_main_save' ) ) {
			/**
			 * Calls completely customized save-routine
			 *
			 * @param int $object_id
			 * @param string Classname
			 *
			 * @since 0.9.9
			 *
			 */
			do_action( 'msls_main_save', $object_id, $class );

			return;
		}

		if ( ! $this->collection->has_current_blog() ) {
			$this->debugger( 'BlogCollection returns false when calling has_current_blog.' );

			return;
		}

		$language = $this->collection->get_current_blog()->get_language();
		$msla     = new MslsLanguageArray( $this->get_input_array( $object_id ) );
		$options  = new $class( $object_id );
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
				$options = new $class( $larr_id );
				$options->save( $msla->get_arr( $language ) );
			} elseif ( isset( $temp[ $language ] ) ) {
				$options = new $class( $temp[ $language ] );
				$options->delete();
			}

			restore_current_blog();
		}
	}

}
