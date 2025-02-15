<?php declare( strict_types=1 );

namespace lloc\Msls;

interface OptionsInterface {

	public function has_value( string $key ): bool;

	public function get_current_link(): string;

	public function get_permalink( string $language ): string;
}
