<?php declare( strict_types=1 );

namespace lloc\Msls\Options\Tax;

use lloc\Msls\Options\OptionsInterface;

interface OptionsTaxInterface extends OptionsInterface {

	public static function get_base_option(): string;

	public function handle_rewrite(): OptionsTaxInterface;
}
