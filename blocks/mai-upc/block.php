<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

add_action( 'acf/init', 'mai_register_upc_block' );
/**
 * Register Mai UPC block.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_register_upc_block() {
	register_block_type( __DIR__ . '/block.json' );
}

/**
 * Callback function to render the block.
 *
 * @since 0.1.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content The block content.
 * @param bool     $is_preview Whether or not the block is being rendered for editing preview.
 * @param int      $post_id The current post being edited or viewed.
 * @param WP_Block $wp_block The block instance (since WP 5.5).
 * @param array    $context The block context array.
 *
 * @return void
 */
function mai_do_upc_block( $attributes, $content, $is_preview, $post_id, $wp_block, $context ) {
	$args            = [];
	$args['params']  = (array) get_field( 'params' );
	$args['hide']    = (bool) get_field( 'hide' );
	$args['preview'] = $is_preview;
	$template        = [ [ 'core/paragraph', [], [] ] ];
	$inner           = sprintf( '<InnerBlocks template="%s" />', esc_attr( wp_json_encode( $template ) ) );
	$content         = $is_preview ? $inner : $content;
	$content         = do_shortcode( $content );

	$block = new Mai_UPC( $args, $content );
	$block->render();
}

add_action( 'acf/init', 'mai_register_upc_field_group' );
/**
 * Register field group.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_register_upc_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		[
			'key'    => 'maiupc_field_group',
			'title'  => __( 'Mai URL Parameter Content', 'mai-url-parameter-content' ),
			'fields' => [
				[
					'key'          => 'maiupc_parameters',
					'label'        => __( 'URL Parameters', 'mai-url-parameter-content' ),
					'name'         => 'params',
					'type'         => 'repeater',
					'layout'       => 'block',
					'min'          => 1,
					'max'          => 0,
					'button_label' => __( 'Add Parameter', 'mai-url-parameter-content' ),
					'sub_fields'   => [
						[
							'key'             => 'maiupc_key',
							'label'           => __( 'Parameter', 'mai-url-parameter-content' ),
							'name'            => 'key',
							'type'            => 'text',
							'parent_repeater' => 'maiupc_parameters',
						],
						[
							'key'             => 'maiupc_value',
							'label'           => __( 'Value', 'mai-url-parameter-content' ),
							'name'            => 'value',
							'type'            => 'text',
							'parent_repeater' => 'maiupc_parameters',
						],
					],
				],
				[
					'key'           => 'maiupc_hide',
					'label'         => __( 'Show/Hide the inner content if the above conditions are met', 'mai-url-parameter-content' ),
					'name'          => 'hide',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui_on_text'    => __( 'Hide', 'mai-url-parameter-content' ),
					'ui_off_text'   => __( 'Show', 'mai-url-parameter-content' ),
					'ui'            => 1,
				],
			],
			'location' => [
				[
					[
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/mai-upc',
					],
				],
			],
		]
	);
}