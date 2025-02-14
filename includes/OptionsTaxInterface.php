<?php declare( strict_types=1 );

namespace lloc\Msls;

interface OptionsTaxInterface extends OptionsInterface {

	public static function get_base_option(): string;
}
