<?php

namespace lloc\Msls;

class PostQuery {

	/**
	 * @var ?\WP_Query
	 */
	protected $query;

	/**
	 * Input is expected to be \WP_Query compatible
	 *
	 * @param array<string, mixed> $args
	 */
	public function __construct( array $args ) {
		$this->query = new \WP_Query( $args );
	}

	/**
	 * @return int
	 */
	public function has_posts(): int {
		return $this->query->post_count ?? 0;
	}

	/**
	 * @return array<int, string>
	 */
	public function get_posts(): array {
		$posts = [];
		if ( ! $this->has_posts() ) {
			return $posts;
		}

		while ( $this->query->have_posts() ) {
			$this->query->the_post();

			if ( is_object( $this->query->post ) ) {
				$posts[ get_the_ID() ] = get_the_title();
			}
		}

		wp_reset_postdata();

		return $posts;
	}
}