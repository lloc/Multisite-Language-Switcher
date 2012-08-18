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
     * Verify the reset method
     */
    function test_reset_method() {
        $obj      = new MslsGetSet();
        $obj->str = 'This is a string.';
        $obj->reset();
        $this->assertEquals( array(), $obj->get_arr(), 'An empty array was expected' );
    }

}
