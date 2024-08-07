<?php

namespace DtCr\Core;

use DtCr\Base\ManagableModuleInterface;
use DtCr\Base\ModuleInterface;
use DtCr\Core\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Manages all available modules.
 * Set up hooks and enable modules based on settings.
 */
final class ModulesManager {

	/** @var string[] */
	private $module_classnames = array();

	/** @var ManagableModuleInterface[] */
	private $enabled_modules = array();

	/** @var ModuleInterface[] */
	private $core_modules = array();

	/**
	 * Relative path to the modules directory.
	 *
	 * @var string
	 */
	const MODULE_NAMESPACE_BASE = 'DtCr\Modules\\';

	/**
	 * Retrieve the class names of all available modules.
	 * File pat, naming convention and interface implementation are checked.
	 *
	 * @return string[] The array of module class names.
	 */
	private static function get_module_classnames() {

		$module_classnames = array();

		foreach ( glob( DT_CR_DIR . 'Modules/*/Module.php' ) as $file ) {
			$dirname     = pathinfo( $file, PATHINFO_DIRNAME );
			$module_name = substr( $dirname, strrpos( $dirname, '/' ) + 1 );

			$module_classname = self::MODULE_NAMESPACE_BASE . $module_name . '\\Module';

			if ( is_a( $module_classname, ModuleInterface::class, true ) ) {
				$module_classnames[] = $module_classname;
			}
		}

		return $module_classnames;
	}

	/**
	 * Initializes the module classnames, core modules, and enabled modules.
	 * Module instances are created only for core and enabled modules.
	 *
	 * @return void
	 */
	public function __construct() {
		foreach ( $this->get_module_classnames() as $module_classname ) {
			$this->module_classnames[] = $module_classname;

			if ( $module_classname::is_core_module() ) {
				$this->core_modules[] = $module_classname::instance();
			}

			if ( is_a( $module_classname, ManagableModuleInterface::class, true ) ) {
				if ( Settings::is_module_enabled( $module_classname::get_identifier() ) ) {
					$this->enabled_modules[] = $module_classname::instance();
				}
			}
		}
	}

	/**
	 * Initializes the modules by calling the `init` method on each module.
	 *
	 * @return void
	 */
	public function init() {
		// init core modules first
		foreach ( $this->core_modules as $module ) {
			$module->init();
		}

		foreach ( $this->enabled_modules as $module ) {
			$module->init();
		}
	}

	/**
	 * Sets up the hooks for the modules.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		// for core modules first
		foreach ( $this->core_modules as $module ) {
			$module->setup_hooks();
		}

		foreach ( $this->enabled_modules as $module ) {
			$module->setup_hooks();
		}
	}

	/**
	 * Retrieves data of manageable modules to be used later in the plugin settings (admin panel).
	 *
	 * @return array{'identifier': string, 'title': string, 'description': string}[]
	 */
	public static function get_managable_modules_data() {
		$data = array();

		foreach ( self::get_module_classnames() as $classname ) {
			if ( is_a( $classname, ManagableModuleInterface::class, true ) ) {
				$data[] = array(
					'identifier'     => $classname::get_identifier(),
					'title'          => $classname::get_title(),
					'label'          => $classname::get_label(),
					'description'    => $classname::get_description(),
					'settings_order' => $classname::get_settings_order(),
				);
			}
		}

		return $data;
	}
}
