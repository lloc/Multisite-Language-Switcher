<?php

namespace lloc\Msls\Settings;

class BlogDescription extends BlogOption {

	/**
	 * @return ?string
	 */
	public function get(): ?string {
		if ( ! empty( $this->option['exclude_current_blog'] ) ) {
			return null;
		}

		return $this->option['description'] ?? null;
	}

}