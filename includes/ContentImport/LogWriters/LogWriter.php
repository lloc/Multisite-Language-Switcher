<?php

namespace lloc\Msls\ContentImport\LogWriters;


interface LogWriter {
	/**
	 * Writes the log to the destination.
	 *
	 * @param array $data An array of data to log.
	 *
	 * @return mixed
	 */
	public function write(array $data);
}