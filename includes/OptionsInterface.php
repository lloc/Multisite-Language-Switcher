<?php

namespace lloc\Msls;

interface OptionsInterface {

	public function has_value( string $value ): bool;

	public function get_permalink( string $language ): string;

	public function get_current_link(): string;

}