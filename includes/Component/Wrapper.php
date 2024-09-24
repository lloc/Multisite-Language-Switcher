<?php

namespace lloc\Msls\Component;

final class Wrapper extends Component {

	protected string $element;

	protected string $content;

	public function __construct( string $element, string $content ) {
		$this->element = $element;
		$this->content = $content;
	}

	public function render(): string {
		return sprintf( '<%1$s>%2$s</%1$s>', esc_html( $this->element ), wp_kses( $this->content, self::get_allowed_html() ) );
	}
}
