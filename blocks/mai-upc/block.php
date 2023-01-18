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
	$args['class']   = isset( $attributes['className'] ) ? $attributes['className']: '';
	$args['params']  = get_field( 'maiupc' );
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

	// acf_add_local_field_group(
	// 	[
	// 		'key'    => 'mai_upc_field_group',
	// 		'title'  => __( 'Mai URL Parameter Content', 'mai-url-parameter-content' ),
	// 		'fields' => [
	// 			[
	// 				'key'     => 'mai_upc_trigger',
	// 				'label'   => __( 'Trigger', 'mai-url-parameter-content' ),
	// 				'name'    => 'trigger',
	// 				'type'    => 'select',
	// 				'choices' => [
	// 					'manual' => __( 'Manual (Custom Link)', 'mai-url-parameter-content' ),
	// 					'load'   => __( 'On Load', 'mai-url-parameter-content' ),
	// 					'scroll' => __( 'Scroll Distance', 'mai-url-parameter-content' ),
	// 					'time'   => __( 'Time Delay', 'mai-url-parameter-content' ),
	// 				],
	// 			],
	// 			[
	// 				'key'     => 'mai_upc_animate',
	// 				'label'   => __( 'Animation', 'mai-url-parameter-content' ),
	// 				'name'    => 'animate',
	// 				'type'    => 'select',
	// 				'choices' => [
	// 					'fade' => __( 'Fade In', 'mai-url-parameter-content' ),
	// 					'up'   => __( 'Slide up', 'mai-url-parameter-content' ),
	// 					'down' => __( 'Slide down', 'mai-url-parameter-content' ),
	// 				],
	// 			],
	// 			[
	// 				'key'               => 'mai_upc_distance',
	// 				'label'             => __( 'Scroll distance', 'mai-url-parameter-content' ),
	// 				'name'              => 'distance',
	// 				'type'              => 'number',
	// 				'default_value'     => 50,
	// 				'min'               => 0,
	// 				'max'               => '',
	// 				'step'              => 1,
	// 				'append'            => '%',
	// 				'conditional_logic' => [
	// 					[
	// 						[
	// 							'field'    => 'mai_upc_trigger',
	// 							'operator' => '==',
	// 							'value'    => 'scroll',
	// 						],
	// 					],
	// 				],
	// 			],
	// 			[
	// 				'key'               => 'mai_upc_delay',
	// 				'label'             => __( 'Delay', 'mai-url-parameter-content' ),
	// 				'name'              => 'delay',
	// 				'type'              => 'number',
	// 				'min'               => 0,
	// 				'max'               => '',
	// 				'step'              => '.5',
	// 				'append'            => __( 'seconds', 'mai-url-parameter-content' ),
	// 				'conditional_logic' => [
	// 					[
	// 						[
	// 							'field'    => 'mai_upc_trigger',
	// 							'operator' => '==',
	// 							'value'    => 'time',
	// 						],
	// 					],
	// 				],
	// 			],
	// 			[
	// 				'key'          => 'mai_upc_width',
	// 				'label'        => __( 'Width', 'mai-url-parameter-content' ),
	// 				'instructions' => __( 'Accepts any CSS value (px, em, rem, vw, ch, etc.). Using 100% removes margin around content.', 'mai-url-parameter-content' ),
	// 				'name'         => 'width',
	// 				'type'         => 'text',
	// 				'placeholder'  => '600px',
	// 			],
	// 			[
	// 				'key'               => 'mai_upc_repeat',
	// 				'label'             => __( 'Repeat', 'mai-url-parameter-content' ),
	// 				'instructions'      => __( 'The time it takes before this popup will be displayed again for the same user. Use 0 to always show, but beware that this may frustrate your website users.', 'mai-url-parameter-content' ),
	// 				'name'              => 'repeat',
	// 				'type'              => 'text',
	// 				'default_value'     => '7 days', // Can't translate. English for `strtotime()`.
	// 				'conditional_logic' => [
	// 					[
	// 						[
	// 							'field'    => 'mai_upc_trigger',
	// 							'operator' => '!=',
	// 							'value'    => 'manual',
	// 						],
	// 					],
	// 				],
	// 			],
	// 			[
	// 				'key'               => 'mai_upc_repeat_roles',
	// 				'label'             => __( 'Always repeat for user roles', 'mai-url-parameter-content' ),
	// 				'name'              => 'repeat_roles',
	// 				'instructions'      => __( 'Select user roles that will always see this popup, regardless of the setting above.', 'mai-url-parameter-content' ),
	// 				'type'              => 'select',
	// 				'choices'           => [], // Added later.
	// 				'return_format'     => 'value',
	// 				'multiple'          => 1,
	// 				'ui'                => 1,
	// 				'ajax'              => 1,
	// 				'conditional_logic' => [
	// 					[
	// 						[
	// 							'field'    => 'mai_upc_trigger',
	// 							'operator' => '!=',
	// 							'value'    => 'manual',
	// 						],
	// 					],
	// 				],
	// 			],
	// 			[
	// 				'key'          => 'mai_upc_link',
	// 				'label'        => __( 'Link', 'mai-url-parameter-content' ),
	// 				'instructions' => __( 'Launch this popup by linking any text or button link to the anchor below. The popup must be on the page for the link to work.', 'mai-url-parameter-content' ),
	// 				'name'         => 'id',
	// 				'type'         => 'text',
	// 			],
	// 		],
	// 		'location' => [
	// 			[
	// 				[
	// 					'param'    => 'block',
	// 					'operator' => '==',
	// 					'value'    => 'acf/mai-popup',
	// 				],
	// 			],
	// 		],
	// 	]
	// );
}