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
        $this->assertEquals( array(), $obj->get_arr(), 'An empty array was expected' );
    }

}
