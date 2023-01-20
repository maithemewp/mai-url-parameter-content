<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Mai UPC class.
 */
class Mai_UPC {
	protected $args;
	protected $content;
	protected $valid; // If URL params are met/validated.

	/**
	 * Class constructor.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function __construct( $args = [], $content = '' ) {
		// Get args.
		$args = shortcode_atts(
			[
				'params'  => [],
				'hide'    => true,
				'preview' => false,
			],
			$args,
			'mai_upc'
		);

		// Sanitize args.
		$args['hide']    = rest_sanitize_boolean( $args['hide'] );
		$args['preview'] = rest_sanitize_boolean( $args['preview'] );

		// Build and sanitize params.
		$params = [];

		if ( $args['params'] ) {
			foreach ( $args['params'] as $index => $values ) {
				// $args['params'][ $index ]['key'] = isset( $values['key'] ) ? sanitize_key( $values['key'] ) :
				$key   = isset( $values['key'] ) ? esc_attr( $values['key'] ) : '';
				$value = isset( $values['value'] ) ? esc_attr( $values['value'] ) : '';

				// Skip if no key. We allow empty value.
				if ( ! $key ) {
					continue;
				}

				// Add to array.
				$params[ $key ] = $value;
			}
		}

		// Set params again.
		$args['params'] = $params;

		// Set props.
		$this->args    = $args;
		$this->content = $content;
		$this->valid   = false;
	}

	/**
	 * Display the content.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function render() {
		echo $this->get();
	}

	/**
	 * Gets upc.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get() {
		// Show full content in editor.
		if ( $this->args['preview'] ) {
			return $this->content;
		}

		// Validate.
		$this->validate();

		// Showing. URL is valid and not hiding content, so we show the content.
		if ( $this->valid && ! $this->args['hide'] ) {
			return $this->get_content();
		}

		// Hiding. URL is not valid, and we hide if valid, so we show the content.
		if ( ! $this->valid && $this->args['hide'] ) {
			return $this->get_content();
		}
	}

	/**
	 * Validates the current URL.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	function validate() {
		// Bail if no params.
		if ( empty( $this->args['params'] ) || empty( $_GET ) ) {
			return;
		}

		// Get matching params.
		$matches = array_intersect( array_keys( $_GET ), array_keys( $this->args['params'] ) );

		// Bail if on front end an all params don't exist.
		if ( count( $matches) !== count( $this->args['params'] ) ) {
			return;
		}

		// Handles value checks and string replacements.
		foreach ( $this->args['params'] as $key => $value ) {
			// Bail if we have a value that doesn't match.
			if ( $value && strtolower( (string) $value ) !== strtolower( (string) $_GET[ $key ] ) ) {
				return;
			}
		}

		$this->valid = true;
	}

	/**
	 * Gets content with placeholders replaced.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	function get_content() {
		// Handles value checks and string replacements.
		foreach ( $this->args['params'] as $key => $value ) {
			$replace = '';

			// Replace all param content.
			// We can't check if valid because some content
			// will show if not valid when set to hide if valid.
			// The latter is definitely an edge-case, but it's possible.
			if ( isset( $_GET[ $key ] ) ) {
				$replace = wp_kses_post( $_GET[ $key ] );
			}

			// Replace placeholder content.
			$this->content = str_replace( sprintf( '{%s}', $key ), $replace, $this->content );
		}

		return $this->content;
	}
}
