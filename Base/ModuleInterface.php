<?php
namespace DtCr\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Interface for all modules in our plugin.
 */
interface ModuleInterface {
	/**
	 * Module iodentifier to be used internally by system.
	 */
	public static function get_identifier();

	/**
	 * Core modules provide core functionality used by other modules
	 */
	public static function is_core_module();

	/**
	 * Set up hooks for the module.
	 */
	public function setup_hooks();

	/**
	 * Actions preformed when module is initialized.
	 */
	public function init();
}
