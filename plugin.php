<?php

namespace DtCr;

use DtCr\Core\ModulesManager;
use DtCr\Core\Settings;


defined( 'ABSPATH' ) || exit;

/**
 * Main class plugin
 */
class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var ModulesManager
	 */
	public $modules_manager;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		$this->modules_manager = new ModulesManager();

		$this->setup_hooks();
		$this->modules_manager->setup_hooks();

		do_action( 'dt-cr/init' );
	}

	private function setup_hooks() {
		add_action( 'init', array( $this, 'on_init' ) );

		// settings menu item and page
		add_action( 'admin_init', array( Settings::class, 'settings_init' ) );
		add_action( 'admin_menu', array( Settings::class, 'settings_page' ) );

		// add link to settings page in plugins list
		add_filter(
			'plugin_action_links_' . DT_CR_BASE,
			function ( $links ) {
				$url = admin_url( 'options-general.php?page=responsiveness-settings' );
				array_push( $links, '<a href="' . $url . '">' . __( 'Settings', 'dt-cr' ) . '</a>' );

				return $links;
			}
		);
	}

	/**
	 * Singleton implementation
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Uninstall hook.
	 *
	 * @return void
	 */
	public static function on_uninstall() {
		Settings::on_uninstall();
	}

	public function on_init() {
		$this->modules_manager->init();
	}

	/**
	 * Clone.
	 * Disable class cloning and throw an error on object clone.
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @since  1.7.0
	 * @access public
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Cloning instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}

	/**
	 * Wakeup.
	 * Disable unserializing of the class.
	 *
	 * @since  1.7.0
	 * @access public
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf( 'Unserializing instances of the singleton "%s" class is forbidden.', get_class( $this ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'1.0.0'
		);
	}
}
