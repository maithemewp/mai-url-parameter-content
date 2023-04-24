<?php

/**
 * Plugin Name:     Mai URL Parameter Content
 * Plugin URI:      https://bizbudding.com/
 * Description:     Show or hide dynamic content based on URL parameters.
 * Version:         0.3.0
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Must be at the top of the file.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Main Mai_URL_Parameter_Content_Plugin Class.
 *
 * @since 0.1.0
 */
final class Mai_URL_Parameter_Content_Plugin {

	/**
	 * @var   Mai_URL_Parameter_Content_Plugin The one true Mai_URL_Parameter_Content_Plugin
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_URL_Parameter_Content_Plugin Instance.
	 *
	 * Insures that only one instance of Mai_URL_Parameter_Content_Plugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_URL_Parameter_Content_Plugin::setup_constants() Setup the constants needed.
	 * @uses    Mai_URL_Parameter_Content_Plugin::includes() Include the required files.
	 * @uses    Mai_URL_Parameter_Content_Plugin::hooks() Activate, deactivate, etc.
	 * @see     Mai_URL_Parameter_Content_Plugin()
	 * @return  object | Mai_URL_Parameter_Content_Plugin The one true Mai_URL_Parameter_Content_Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_URL_Parameter_Content_Plugin;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-url-parameter-content' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-url-parameter-content' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'MAI_UPC_PLUGIN_VERSION' ) ) {
			define( 'MAI_UPC_PLUGIN_VERSION', '0.3.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_UPC_PLUGIN_DIR' ) ) {
			define( 'MAI_UPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Classes.
		foreach ( glob( MAI_UPC_PLUGIN_DIR . 'classes/*.php' ) as $file ) { include $file; }
		// Blocks.
		include MAI_UPC_PLUGIN_DIR . 'blocks/mai-upc/block.php';
	}

	/**
	 * Run the hooks.
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'plugins_loaded', [ $this, 'updater' ] );
		add_action( 'wp_head',        [ $this, 'add_params' ] );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since 0.1.0
	 *
	 * @uses https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return void
	 */
	public function updater() {
		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = PucFactory::buildUpdateChecker( 'https://github.com/maithemewp/mai-url-parameter-content/', __FILE__, 'mai-url-parameter-content' );

		// Set branch.
		$updater->setBranch( 'main' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Check for URL parameter classes and links.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function add_params() {
		$links = apply_filters( 'mai_url_parameter_links', [] );

		if ( ! $links ) {
			return;
		}

		$class = new Mai_URL_Parameter_Adder( $links );
		$class->run();
	}
}

/**
 * The main function for that returns Mai_URL_Parameter_Content_Plugin
 *
 * The main function responsible for returning the one true Mai_URL_Parameter_Content_Plugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_URL_Parameter_Content_Plugin(); ?>
 *
 * @since 0.1.0
 *
 * @return object|Mai_URL_Parameter_Content_Plugin The one true Mai_URL_Parameter_Content_Plugin Instance.
 */
function mai_url_parameter_content_plugin() {
	return Mai_URL_Parameter_Content_Plugin::instance();
}

// Get Mai_URL_Parameter_Content_Plugin Running.
mai_url_parameter_content_plugin();
