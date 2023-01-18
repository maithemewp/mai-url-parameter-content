<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Mai UPC class.
 */
class Mai_UPC {
	protected $args;
	protected $content;

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
				'preview' => false,
			],
			$args,
			'mai_upc'
		);

		// Sanitize args.
		$args['class']   = esc_attr( $args['class'] );
		$args['preview'] = rest_sanitize_boolean( $args['preview'] );

		// Build params.
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

		// Bail if no params.
		if ( empty( $this->args['params'] || empty( $_GET ) ) ) {
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

			// Replace placeholder content.
			$this->content = str_replace( sprintf( '{%s}', $key ), wp_kses_post( $value) , $this->content );
		}

		return $this->content;
	}
}
