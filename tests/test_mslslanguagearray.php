<?php
/**
 * Tests for MslsGetSet
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package WP_Document_Revisions
 */

/**
 * WP_Test_MslsGetSet
 */
class WP_Test_MslsLanguageArray extends WP_UnitTestCase {

    /**
     * SetUp initial settings
     */
    function setUp() {
        parent::setUp();
        wp_cache_flush();
    }

    /**
     * Break down for next test
     */
    function tearDown() {
        parent::tearDown();
    }

    /**
     * Verify the set-method
     */
    function test_set_method() {
		$arr = array(
            'fr_FR' => 0, // not ok, value 0 is not ok as blog_id
            'it'    => 1,
            'de_DE' => 2,
            'x'     => 3, // not ok, minlength of string is 2
        );
        $obj = new MslsLanguageArray( $arr );
        $this->assertEquals( array( 'it' => 1, 'de_DE' => 2 ), $obj->get_arr() );
        $obj->set( 'fr_FR', 3 );
        $this->assertEquals( array( 'it' => 1, 'de_DE' => 2, 'fr_FR' => 3 ), $obj->get_arr() );
    }

    /**
     * Verify the get_val-method
     */
    function test_get_val_method() {
		$arr = array(
            'it'    => 1,
            'de_DE' => 2,
        );
        $obj = new MslsLanguageArray( $arr );
        $this->assertEquals( 1, $obj->get_val( 'it' ) );
        $this->assertEquals( 0, $obj->get_val( 'fr_FR' ) );
    }

    /**
     * Verify the get_arr-method
     */
    function test_get_arr_method() {
		$arr = array(
            'it'    => 1,
            'de_DE' => 2,
        );
        $obj = new MslsLanguageArray( $arr );
        $this->assertEquals( array( 'it' => 1, 'de_DE' => 2 ), $obj->get_arr() );
        $this->assertEquals( array( 'it' => 1 ), $obj->get_arr( 'de_DE' ) );
    }

}
