<?php

namespace lloc\Msls\ContentImport\LogWriters;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\MslsRegistryInstance;

class AdminNoticeLogger extends MslsRegistryInstance implements LogWriter {
	protected $transient = 'msls_last_import_log';

	/**
	 * @var ImportCoordinates
	 */
	protected $import_coordinates;

	public function write( array $data ) {
		/* translators: %1$d: source post ID, %2$d: source blog ID, %3$d: destination post ID, %4$d: destination blog ID */
		$format = esc_html__( 'From post %1$d on site %2$d to post %3$d on site %4$d', 'multisite-language-switcher' );

		$message  = '<h3>' . esc_html__( 'Multisite Language Switcher last import report', 'multisite-language-switcher' ) . '</h3>';
		$message .= '<b>' . sprintf(
			$format,
			$this->import_coordinates->source_post_id,
			$this->import_coordinates->source_blog_id,
			$this->import_coordinates->dest_post_id,
			$this->import_coordinates->dest_blog_id
		) . '</b>';
		if ( ! empty( $data['info'] ) ) {
			$section_title = esc_html__( 'General information', 'multisite-language-switcher' );
			$entries       = $data['info'];
			$message      .= $this->get_section_html( $section_title, $entries );
		}

		if ( ! empty( $data['success'] ) ) {
			$section_title   = esc_html__( 'Details', 'multisite-language-switcher' );
			$success_data    = $data['success'];
			$success_entries = array();

			if ( isset( $success_data['post-field']['added'] ) ) {
				$success_entries[] = esc_html__(
					'The following post fields have been set: ',
					'multisite-language-switcher'
				) .
									'<code>' . implode(
										'</code>, <code>',
										array_keys( $success_data['post-field']['added'] )
									) . '</code>.';
			}
			if ( isset( $success_data['meta']['added'] ) ) {
				$success_entries[] = esc_html__(
					'The following post meta have been set: ',
					'multisite-language-switcher'
				) .
									'<code>' . implode(
										'</code>, <code>',
										array_keys( $success_data['meta']['added'] )
									) . '</code>.';
			}
			if ( isset( $success_data['term']['added'] ) ) {
				$success_entries[] = esc_html__(
					'Terms have been assigned to the post for the following taxonomies: ',
					'multisite-language-switcher'
				) .
									'<code>' . implode(
										'</code>, <code>',
										array_keys( $success_data['term']['added'] )
									) . '</code>.';
			}
			if ( isset( $success_data['post-thumbnail']['set'] ) ) {
				$success_entries[] = esc_html__( 'The post thumbnail has been set.', 'multisite-language-switcher' );
			}

			$message .= $this->get_section_html( $section_title, $success_entries, false );
		}

		if ( ! empty( $data['error'] ) ) {
			$section_title = esc_html__( 'Errors:', 'multisite-language-switcher' );
			$error_data    = $data['error'];
			$error_entries = array();
			if ( isset( $error_data['term']['added'] ) || isset( $error_data['term']['created'] ) ) {
				$taxonomies      = isset( $error_data['term']['added'] ) ? array_keys( $error_data['term']['added'] ) : array();
				$taxonomies      = isset( $error_data['term']['created'] ) ? array_merge(
					$taxonomies,
					array_keys( $error_data['term']['created'] )
				) : $taxonomies;
				$error_entries[] = esc_html__(
					'There were issues creating or assigning terms for the following taxonomies: ',
					'multisite-language-switcher'
				) .
									'<code>' . implode( '</code>, <code>', $taxonomies ) . '</code>.';
			}
			if ( isset( $error_data['post-thumbnail']['set'] ) || isset( $error_data['post-thumbnail']['created'] ) ) {
				$error_entries[] = esc_html__(
					'The post thumbnail could not be created or set.',
					'multisite-language-switcher'
				);
			}
			$message .= $this->get_section_html( $section_title, $error_entries, false );
		}

		$html = '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';

		switch_to_blog( $this->import_coordinates->dest_blog_id );

		set_transient( $this->transient, $html, HOUR_IN_SECONDS );
	}

	protected function get_section_html( $section_title, $entries, $escape_entries = true ) {
		$html  = '<h3>' . $section_title . '</h3>';
		$html .= '<ul>';
		foreach ( $entries as $entry ) {
			if ( $escape_entries ) {
				$html .= '<li>' . esc_html( $entry ) . '</li>';
			} else {
				$html .= '<li>' . $entry . '</li>';
			}
		}
		$html .= '</ul>';

		return $html;
	}

	public function show_last_log( $echo = true ) {
		if ( ! ( $html = get_transient( $this->transient ) ) ) {
			return;
		}

		if ( $echo ) {
			echo $html;
		}

		// we've shown it, no reason to keep it
		delete_transient( $this->transient );

		return $html;
	}

	public function set_import_coordinates( $import_coordinates ) {
		$this->import_coordinates = $import_coordinates;
	}

	/**
	 * Returns the name of the transient where the logger will store the output HTML.
	 *
	 * @return string
	 */
	public function get_transient() {
		return $this->transient;
	}
}
