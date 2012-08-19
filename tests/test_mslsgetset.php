<?php

/**
 * Tests MslsGetSet
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package WP_Document_Revisions
 */
class WP_Test_MslsGetSet extends WP_UnitTestCase {

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
     * Verify the get_arr-method
     */
    function test_get_arr_method() {
        $obj = new MslsGetSet();
        $obj->temp = 'test';
        $this->assertEquals( array( 'temp' => 'test' ), $obj->get_arr() , 'Another content of the result was expected' );
        $obj->reset();
        $this->assertEmpty( $obj->get_arr() , 'An empty array was expected' );
    }

}
