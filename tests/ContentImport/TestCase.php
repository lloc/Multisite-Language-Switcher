<?php

namespace lloc\Msls\ContentImport;


use Prophecy\Argument;

class TestCase extends \Msls_UnitTestCase {

	protected function setup_source_and_dest() {
		$source_lang    = 'it_IT';
		$dest_lang      = 'de_DE';
		$source_blog_id = $this->factory()->blog->create();
		$dest_blog_id   = $this->factory()->blog->create();

		switch_to_blog( $source_blog_id );
		$source_post = $this->factory()->post->create_and_get();
		update_option( 'WPLANG', $source_lang );

		switch_to_blog( $dest_blog_id );
		$dest_post_id = $this->factory()->post->create();
		update_option( 'WPLANG', $dest_lang );

		$import_coordinates                 = new ImportCoordinates();
		$import_coordinates->source_blog_id = $source_blog_id;
		$import_coordinates->dest_blog_id   = $dest_blog_id;
		$import_coordinates->source_post_id = $source_post->ID;
		$import_coordinates->source_post    = $source_post;
		$import_coordinates->dest_post_id   = $dest_post_id;
		$import_coordinates->source_lang    = $source_lang;
		$import_coordinates->dest_lang      = $dest_lang;

		$logger = $this->prophesize( ImportLogger::class );
		$relations = $this->prophesize( Relations::class );
		$logger->log_information( Argument::type( 'string' ), Argument::any() )->willReturn( true );
		$logger->log_success( Argument::type( 'string' ), Argument::any() )->willReturn( true );
		$logger->log_error( Argument::type( 'string' ), Argument::any() )->willReturn( true );
		$relations->should_create( Argument::any(), Argument::type( 'string' ), Argument::type( 'string' ) )->willReturn( true );

		$data      = $this->factory()->post->create_and_get();

		restore_current_blog();

		return array( $import_coordinates, $logger, $relations, (array) $data );
	}
}