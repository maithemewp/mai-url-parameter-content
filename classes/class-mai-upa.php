<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Mai UPA class.
 */
class Mai_URL_Parameter_Adder {
	protected $links; // Assocative array of 'customclass' => [ 'param1 => 'value1, 'param2 => 'value2' ].
	protected $link_classes;

	/**
	 * Class constructor.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function __construct( array $links ) {
		$this->links        = $this->sanitize( $links );
		$this->link_classes = array_flip( array_keys( $this->links ) );
	}

	/**
	 * Maybe add links.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function run() {
		if ( ! $this->links ) {
			return;
		}

		add_filter( 'render_block', [ $this, 'render_block' ], 30, 2 );
	}

	/**
	 * Adds links to each block.
	 *
	 * @since 0.3.0
	 *
	 * @param string $block_content The existing block content.
	 * @param object $block         The button block object.
	 *
	 * @return string The modified block HTML.
	 */
	function render_block( $block_content, $block ) {
		if ( ! $block_content ) {
			return $block_content;
		}

		if ( ! isset( $block['attrs']['className'] ) || empty( $block['attrs']['className'] ) ) {
			return $block_content;
		}

		// Build classes array.
		$classes = explode( ' ', $block['attrs']['className'] );
		$classes = array_map( 'trim', $classes );
		$classes = array_filter( $classes );
		$classes = array_flip( $classes );
		$matches = array_intersect_key( $classes, $this->links );

		if ( ! $matches ) {
			return $block_content;
		}

		// Create the new document.
		$dom = new DOMDocument();

		// Modify state.
		$libxml_previous_state = libxml_use_internal_errors( true );

		// Load the content in the document HTML.
		$dom->loadHTML( "<div>$block_content</div>" );

		// Handle wraps.
		$container = $dom->getElementsByTagName('div')->item(0);
		$container = $container->parentNode->removeChild( $container );

		while ( $dom->firstChild ) {
			$dom->removeChild( $dom->firstChild );
		}

		while ( $container->firstChild ) {
			$dom->appendChild( $container->firstChild );
		}

		// Handle errors.
		libxml_clear_errors();

		// Restore.
		libxml_use_internal_errors( $libxml_previous_state );

		$elements = $dom->getElementsByTagName( 'a' );

		if ( ! $elements->length ) {
			return $block_content;
		}

		foreach ( $elements as $element ) {
			$href = $element->getAttribute( 'href' );

			// Skip if not an absolute url.
			if ( false === strpos( $href, 'http' ) ) {
				continue;
			}

			foreach ( $matches as $class => $index ) {
				$href = add_query_arg( $this->links[ $class ], $href );
			}

			$element->setAttribute( 'href', $href );
		}

		// Save new HTML.
		$block_content = $dom->saveHTML();

		return $block_content;
	}

	/**
	 * Gets sanitized links.
	 *
	 * @since 0.3.0
	 *
	 * @return array
	 */
	function sanitize( $links ) {
		$sanitized = [];

		foreach ( $links as $class => $params ) {
			foreach ( $params as $key => $value ) {
				$sanitized[ $class ][ sanitize_html_class( $key ) ] = esc_attr( $value );
			}
		}

		return array_filter( $sanitized );
	}
}
