<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\LogWriters\AdminNoticeLogger;
use lloc\Msls\ContentImport\LogWriters\LogWriter;

class ImportLogger {

	protected string $levels_delimiter = '/';

	/**
	 * @var array<string, array>
	 */
	protected array $data = array(
		'info'    => array(),
		'error'   => array(),
		'success' => array(),
	);

	/**
	 * @var ImportCoordinates
	 */
	protected $import_coordinates;

	public function __construct( ImportCoordinates $import_coordinates ) {
		$this->import_coordinates = $import_coordinates;
	}

	/**
	 * Merges the specified log data into this log.
	 *
	 * @param ImportLogger|null $logger
	 */
	public function merge( ImportLogger $logger = null ): void {
		if ( null === $logger ) {
			return;
		}

		$this->data = array_merge_recursive( $this->data, $logger->get_data() );
	}

	/**
	 * @return array<string, array>
	 */
	public function get_data(): array {
		return $this->data;
	}

	/**
	 * Saves the log or prints it some place.
	 */
	public function save(): void {
		$default_log_writer = AdminNoticeLogger::instance();
		$default_log_writer->set_import_coordinates( $this->import_coordinates );

		/**
		 * Filters the log class or object that should be used to write the log to the destination.
		 *
		 * @param mixed $default_log_writer
		 * @param ImportCoordinates $import_coordinates
		 */
		$log_writer = apply_filters( 'msls_content_import_log_writer', $default_log_writer, $this->import_coordinates );
		if ( empty( $log_writer ) ) {
			// we assume that was done on purpose to prevent logging
			return;
		}

		if ( is_string( $log_writer ) ) {
			$log_writer = new $log_writer();
		}

		if ( ! $log_writer instanceof LogWriter ) {
			// something is fishy, let's use the default one
			$log_writer = $default_log_writer;
		}

		$log_writer->write( $this->get_data() );
	}

	/**
	 * Logs an error.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what What should be stored in the log.
	 */
	public function log_error( $where, $what ): void {
		$this->log( $where, $what, 'error' );
	}

	/**
	 * Logs something.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what What should be stored in the log.
	 * @param string $root Where to log the information.
	 */
	protected function log( $where, $what, $root = 'info' ): void {
		if ( ! isset( $this->data[ $root ] ) ) {
			$this->data[ $root ] = array();
		}

		$data = $this->build_nested_array( $this->build_path( $where ), $what );

		$this->data[ $root ] = array_merge_recursive( $this->data[ $root ], $data );
	}

	/**
	 * @param array $path
	 * @param mixed $what
	 *
	 * @return array
	 */
	protected function build_nested_array( $path, $what = '' ): array {
		$json = '{"'
				. implode( '":{"', $path )
				. '":' . wp_json_encode( $what )
				. implode(
					'',
					array_fill(
						0,
						count( $path ),
						'}'
					)
				);
		$data = json_decode( $json, true );

		return $data;
	}

	/**
	 * @param string $where
	 *
	 * @return array
	 */
	protected function build_path( string $where ): array {
		$where_path = explode( $this->levels_delimiter, $where );

		return $where_path;
	}

	/**
	 * Returns the string that will be used to split paths into levels.
	 *
	 * @return string
	 */
	public function get_levels_delimiter(): string {
		return $this->levels_delimiter;
	}

	/**
	 * Sets the string that will be used to split paths into levels.
	 *
	 * @param string $levels_delimiter
	 */
	public function set_levels_delimiter( $levels_delimiter ): void {
		$this->levels_delimiter = $levels_delimiter;
	}

	/**
	 * Logs a success.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what What should be stored in the log.
	 */
	public function log_success( $where, $what ): void {
		$this->log( $where, $what, 'success' );
	}

	/**
	 * Logs some generic information.
	 *
	 * @param string $key
	 * @param string $message
	 */
	public function log_information( $key, $message ): void {
		$this->data['info'][ $key ] = $message;
	}

	/**
	 * @param string $where
	 *
	 * @return mixed
	 */
	public function get_error( $where ) {
		return $this->get_nested_value( 'error' . $this->levels_delimiter . $where );
	}

	/**
	 * @param string $where
	 *
	 * @return mixed
	 */
	protected function get_nested_value( $where ) {
		$path = $this->build_path( $where );

		$data = $this->data[ array_shift( $path ) ];

		foreach ( $path as $frag ) {
			$data = $data[ $frag ];
		}

		return $data;
	}

	/**
	 * @param string $where
	 *
	 * @return mixed
	 */
	public function get_success( $where ) {
		return $this->get_nested_value( 'success' . $this->levels_delimiter . $where );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get_information( $key ) {
		return isset( $this->data['info'][ $key ] ) ? $this->data['info'][ $key ] : '';
	}
}
