<?php

namespace lloc\Msls\Component;

/**
 * Interface Input
 *
 * @package lloc\Msls\Component
 */
interface InputInterface {

	const INPUT_PREFIX = 'msls_input_';

	/**
	 * @return string
	 */
	public function render(): string;
}
