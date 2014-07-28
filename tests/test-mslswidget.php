<?php
/**
 * Tests for MslsWidget
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsWidget
 */
class WP_Test_MslsWidget extends Msls_UnitTestCase {

	/**
	 * Verify the widget-method
	 */
	function test_widget_method() {
		$obj = new MslsWidget();
		$this->expectOutputString( 'No available translations found' );
		$obj->widget( array(), array() );
		return $obj;
	}

	/**
	 * Verify the static init-method
	 * @depends test_widget_method
	 */
	function test_update_method( $obj ) {
		$this->assertInternalType( 'array', $obj->update( array(), array() ) );
	}

}
