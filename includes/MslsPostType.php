<?php
/**
 * MslsPostType
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Content types: Post types (Pages, Posts, ...)
 * @package Msls
 */
class MslsPostType extends MslsContentTypes implements IMslsRegistryInstance {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->types = array_merge(
			array( 'post', 'page' ), // we don't need attachment, revision or nav_menu_item here
			get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				),
				'names',
				'and'
			)
		);
		if ( !empty( $_REQUEST['post_type'] ) ) {
			$this->request = esc_attr( $_REQUEST['post_type'] );
		}
		else {
			$this->request = get_post_type();
			if ( !$this->request )
				$this->request = 'post'; 
		}
	}

	/**
	 * Check for post_type
	 * @return bool
	 */
	function is_post_type() {
		return true;
	}

	/**
	 * Get or create a instance of MslsPostType
	 * @return MslsPostType
	 */
	static function instance() {
		$registry = MslsRegistry::singleton();
		$cls      = __CLASS__;
		$obj      = $registry->get_object( $cls );
		if ( is_null( $obj ) ) {
			$obj = new $cls;
			$registry->set_object( $cls, $obj );
		}
		return $obj;
	}

}
