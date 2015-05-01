<?php
/**
 * MslsMain
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Abstraction for the hook classes
 * @package Msls
 */
class MslsMain {

	/**
	 * Every child of MslsMain has to define a init-method
	 * @throws Exception If a child class does not define an init method
	 * @return MslsMain
	 */
	public static function init() {
		throw new Exception( 'Static method init is not defined' );
	}

	/**
	 * Prints a message in the error log if WP_DEBUG is true
	 *
	 * @param mixed $message
	 */
	public function debugger( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				$message = print_r( $message, true );
			}
			error_log( 'MSLS Debug: ' . $message );
		}
	}

	/**
	 * Get the input array
	 * @param int $object_id
	 * @return array
	 */
	public function get_input_array( $object_id ) {
		$arr = array();

		$current_blog = MslsBlogCollection::instance()->get_current_blog();
		if ( ! is_null( $current_blog ) ) {
			$arr[ $current_blog->get_language() ] = (int) $object_id;
		}

		$input_post = filter_input_array( INPUT_POST );
		if ( is_array( $input_post ) ) {
			foreach ( $input_post as $key => $value ) {
				if ( false !== strpos( $key, 'msls_input_' ) && ! empty( $value ) ) {
					$arr[ substr( $key, 11 ) ] = (int) $value;
				}
			}
		}

		return $arr;
	}

	/**
	 * Checks if the current input comes from the autosave-functionality
	 * @param int $post_id
	 * @return bool
	 */
	public function is_autosave( $post_id ) {
		return( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id );
	}

	/**
	 * Checks for the nonce in the INPUT_POST
	 * @return boolean
	 */
	public function verify_nonce() {
		return(
			filter_has_var( INPUT_POST, 'msls_noncename' ) &&
			wp_verify_nonce( filter_input( INPUT_POST, 'msls_noncename' ), MSLS_PLUGIN_PATH )
		);
	}

	/**
	 * Delete
	 * @param int $object_id
	 * @codeCoverageIgnore
	 */
	public function delete( $object_id ) {
		$this->save( $object_id, 'MslsOptionsPost' );
	}

	/**
	 * Save
	 * @param int $object_id
	 * @param string $class
	 * @codeCoverageIgnore
	 */
	protected function save( $object_id, $class ) {
		if ( has_action( 'msls_main_save' ) ) {
			/**
			 * Calls completely customized save-routine
			 * @since 0.9.9
			 * @param int $object_id
			 * @param string Classname
			 */
			do_action( 'msls_main_save', $object_id, $class );
			return;
		}

		$blogs = MslsBlogCollection::instance();
		if ( ! $blogs->has_current_blog() ) {
			$this->debugger( 'MslsBlogCollection::instance()->has_current_blog returns false.' );
			return;
		}

		$language = $blogs->get_current_blog()->get_language();
		$msla     = new MslsLanguageArray( $this->get_input_array( $object_id ) );
		$options  = new $class( $object_id );
		$temp     = $options->get_arr();

		if ( 0 != $msla->get_val( $language ) ) {
			$options->save( $msla->get_arr( $language ) );
		}
		else {
			$options->delete();
		}

		foreach ( $blogs->get() as $blog ) {
			switch_to_blog( $blog->userblog_id );

			$language = $blog->get_language();
			$larr_id  = $msla->get_val( $language );

			if ( 0 != $larr_id ) {
				$options = new $class( $larr_id );
				$options->save( $msla->get_arr( $language ) );
			}
			elseif ( isset( $temp[ $language ] ) ) {
				$options = new $class( $temp[ $language ] );
				$options->delete();
			}

			restore_current_blog();
		}
	}

}
