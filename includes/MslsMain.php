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
	 * @return MslsMain
	 */
	static function init() {
		throw new Exception( 'Static method init is not defined' );
	}

	/**
	 * Save
	 * @param int $object_id
	 * @param string $class
	 */
	protected function save( $object_id, $class ) {
		if ( has_action( 'msls_main_save' ) ) {
			/**
			 * Calls completly customized save-routine
			 * @since 0.9.9
			 * @param int $object_id
			 * @param string Classname
			 */
			do_action( 'msls_main_save', $object_id, $class );
		}
		else {
			$blogs = MslsBlogCollection::instance();

			$input = array(
				$blogs->get_current_blog()->get_language() => (int) $object_id,
			);
			foreach ( filter_input_array( INPUT_POST ) as $key => $value ) {
				if ( false !== strpos( $key, 'msls_input_' ) && ! empty( $value ) ) {
					$input[ substr( $key, 11 ) ] = (int) $value;
				}
			}

			$msla      = new MslsLanguageArray( $input );
			$options   = new $class( $object_id );
			$language  = $blogs->get_current_blog()->get_language();
			$temp      = $options->get_arr();
			$object_id = $msla->get_val( $language );

			if ( 0 != $object_id ) {
				$options->save( $msla->get_arr( $language ) );
			}
			else {
				$options->delete();
			}

			foreach ( $blogs->get() as $blog ) {
				switch_to_blog( $blog->userblog_id );
				$language  = $blog->get_language();
				$object_id = $msla->get_val( $language );
				if ( 0 != $object_id ) {
					$options = new $class( $object_id );
					$options->save( $msla->get_arr( $language ) );
				}
				else {
					if ( isset( $temp[ $language ] ) ) {
						$options = new $class( $temp[ $language ] );
						$options->delete();
					}
				}
				restore_current_blog();
			}
		}
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
		return( filter_has_var( INPUT_POST, 'msls_noncename'] ) && wp_verify_nonce( filter_input( INPUT_POST, 'msls_noncename', FILTER_UNSAFE_RAW ), MSLS_PLUGIN_PATH ) );
	}

	/**
	 * Delete
	 * @param int $object_id
	 */
	public function delete( $object_id ) {
		$this->save( $object_id, 'MslsOptionsPost' );
	}

}
