<?php

namespace lloc\Msls\ContentImport\LogWriters;


use lloc\Msls\ContentImport\ImportCoordinates;

class AdminNoticeLoggerTest extends \Msls_UnitTestCase {

	public function testWrite() {
		$data                               = [
			'info' => [ 'something' ],
		];
		$import_coordinates                 = new ImportCoordinates();
		$import_coordinates->source_blog_id = $this->factory()->blog->create();
		$import_coordinates->dest_blog_id   = $this->factory()->blog->create();
		switch_to_blog( $import_coordinates->source_blog_id );
		$import_coordinates->source_post_id = $this->factory()->post->create();
		switch_to_blog( $import_coordinates->dest_blog_id );
		$import_coordinates->dest_post_id = $this->factory()->post->create();

		$logger = new AdminNoticeLogger();

		get_transient( $logger->get_transient() );

		$logger->set_import_coordinates( $import_coordinates );
		$logger->write( $data );

		$html = get_transient( $logger->get_transient() );
		$this->assertNotEmpty( $html );
		$this->assertInternalType( 'string', $html );
	}

	public function testShow_last_log() {
		$logger = new AdminNoticeLogger();
		$output = $logger->show_last_log( false );

		$this->assertEmpty( $output );

		set_transient( $logger->get_transient(), 'foo-bar' );

		$output = $logger->show_last_log( false );
		$this->assertEquals( 'foo-bar', $output );

		$this->assertEmpty( get_transient( $logger->get_transient() ) );
	}
}
