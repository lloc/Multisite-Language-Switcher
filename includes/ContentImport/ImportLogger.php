<?php

namespace lloc\Msls\ContentImport;


class ImportLogger {

	protected $levels_delimiter = '/';

	protected $data = [
		'info'    => [],
		'error'   => [],
		'success' => [],
	];

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
	public function merge( ImportLogger $logger = null ) {
		if ( null === $logger ) {
			return;
		}

		$this->data = array_merge_recursive( $this->data, $logger->get_data() );
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Saves the log or prints it some place.
	 */
	public function save() {
		// @todo how and where to log?
	}

	/**
	 * Logs an error.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what  What should be stored in the log.
	 */
	public function log_error( $where, $what ) {
		$this->log( $where, $what, 'error' );
	}

	/**
	 * Logs something.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what  What should be stored in the log.
	 * @param string $root  Where to log the information.
	 */
	protected function log( $where, $what, $root = 'info' ) {
		if ( ! isset( $this->data[ $root ] ) ) {
			$this->data[ $root ] = [];
		}

		$data = $this->build_nested_array( $this->build_path( $where ), $what );


		$this->data[ $root ] = array_merge_recursive( $this->data[ $root ], $data );
	}

	protected function build_nested_array( $path, $what = '' ) {
		$json = '{"'
		        . implode( '":{"', $path )
		        . '":' . json_encode( $what )
		        . implode(
			        '',
			        array_fill( 0, count( $path ),
				        '}' )
		        );
		$data = json_decode( $json, true );

		return $data;
	}

	/**
	 * @param $where
	 *
	 * @return array
	 */
	protected function build_path( $where ) {
		$where_path = explode( $this->levels_delimiter, $where );

		return $where_path;
	}

	/**
	 * Returns the string that will be used to split paths into levels.
	 *
	 * @return string
	 */
	public function get_levels_delimiter() {
		return $this->levels_delimiter;
	}

	/**
	 * Sets the string that will be used to split paths into levels.
	 *
	 * @param string $levels_delimiter
	 */
	public function set_levels_delimiter( $levels_delimiter ) {
		$this->levels_delimiter = $levels_delimiter;
	}

	/**
	 * Logs a success.
	 *
	 * @param string $where A location string using `/` as level format.
	 * @param mixed  $what  What should be stored in the log.
	 */
	public function log_success( $where, $what ) {
		$this->log( $where, $what, 'success' );
	}

	/**
	 * Logs some generic information.
	 *
	 * @param string $message
	 */
	public function log_information( $key, $message ) {
		$this->data['info'][ $key ] = $message;
	}

	public function get_error( $where ) {
		return $this->get_nested_value( 'error' . $this->levels_delimiter . $where );
	}

	/**
	 * @param $where
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

	public function get_success( $where ) {
		return $this->get_nested_value( 'success' . $this->levels_delimiter . $where );
	}

	public function get_information( $key ) {
		return isset( $this->data['info'][ $key ] ) ? $this->data['info'][ $key ] : '';
	}
}