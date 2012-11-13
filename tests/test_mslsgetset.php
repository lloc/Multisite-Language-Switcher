<?php

/**
 * Tests MslsGetSet
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package WP_Document_Revisions
 */
class WP_Test_MslsGetSet extends WP_UnitTestCase {

	//var $my_obj;

    /**
     * SetUp initial settings
     */
    function setUp() {
        parent::setUp();
        wp_cache_flush();
		//$this->my_obj = new MslsGetSet();
    }

    /**
     * Break down for next test
     */
    function tearDown() {
        parent::tearDown();
    }

    /**
     * Verify the get_arr-method
     */
    function test_get_arr_method() {
		$this->assertFalse( is_plugin_active( $GLOBALS['wp_tests_options']['active_plugins'] ) );
        //$this->my_obj->reset();
        //$this->assertEmpty( $this->my_obj->get_arr(), 'An empty array was expected' );
        //$his->my_obj->temp = 'test';
        //$this->assertEquals( array( 'temp' => 'test' ), $this->my_obj->get_arr() , 'Unexpected result' );
    }

}
