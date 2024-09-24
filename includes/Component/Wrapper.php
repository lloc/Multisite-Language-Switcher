<?php

namespace lloc\Msls\Component;

class Wrapper {

	protected string $element;

	protected string $content;

	public function __construct( string $element, string $content ) {
		$this->element = $element;
		$this->content = $content;
	}

	public function render(): string {
		return sprintf( '<%1$s>%2$s</%1$s>', esc_html( $this->element ), wp_kses_post( $this->content ) );
	}
}
