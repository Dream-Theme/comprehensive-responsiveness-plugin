<?php

namespace DtCr\Core;

use Exception;
use DtCr\Core\ModulesManager;

defined( 'ABSPATH' ) || exit;

class Settings {

	protected static $allowed_breakpoint_units = array( 'px', 'em', 'rem', 'vw', 'vh' );

	// WP permission to open the Settings Page
	const CAPABILITY = 'manage_options';

	// Settings page slug
	const MENU_PAGE_SLUG = 'responsiveness-settings';

	const TEMPLATES_FOLDER_NAME = 'settings/';

	/**
	 * Add the Settings Page in the WP admin menu.
	 *
	 * @return void
	 */
	public static function settings_page() {
		add_options_page(
			__( 'Responsiveness Settings', 'dt-cr' ),
			__( 'Responsiveness', 'dt-cr' ),
			self::CAPABILITY,
			self::MENU_PAGE_SLUG,
			function ( $args ) {
				self::parse_template( 'page', $args );
			}
		);
	}

	/**
	 * Print out Settings page.
	 *
	 * @return void
	 */
	public static function settings_init() {
		// register section
		add_settings_section(
			DT_CR_PLUGIN_ID . '_settings_section',
			'', // no title for section
			null, // no callback for section content at the top of the page
			self::MENU_PAGE_SLUG
		);

		$modules_data = ModulesManager::get_managable_modules_data();
		usort(
			$modules_data,
			function ( $a, $b ) {
				return $a['settings_order'] <=> $b['settings_order'];
			}
		);

		foreach ( $modules_data as $module_data ) {
			self::add_module_enable_checkbox(
				$module_data['identifier'],
				$module_data['title'],
				array(
					'label'       => $module_data['label'],
					'description' => $module_data['description'],
				)
			);
		}

		self::add_user_defined_breakpoint_options();
	}

	/**
	 * Checks if a module is enabled using WP options API.
	 * All modules are enabled by default.
	 *
	 * @return bool
	 */
	public static function is_module_enabled( $module_identifier ) {
		return '1' === get_option( self::build_module_enabled_option_name( $module_identifier ), '1' );
	}

	public static function get_active_user_defined_breakpoints() {
		$user_defined_breakpoints = self::get_user_defined_breakpoints();
		return array_filter(
			$user_defined_breakpoints,
			function ( $item ) {
				return $item['active'];
			}
		);
	}

	/**
	 * Retrieves the user-defined breakpoints from the options if set,
	 * otherwise the default user-defined breakpoints.
	 *
	 * @return array
	 */
	public static function get_user_defined_breakpoints() {
		return get_option(
			self::build_user_defined_breakpoints_option_name(),
			self::get_default_user_defined_breakpoints()
		);
	}

	private static function add_user_defined_breakpoint_options() {
		$name = self::build_user_defined_breakpoints_option_name();

		register_setting(
			DT_CR_PLUGIN_ID . '_settings',
			$name,
			array(
				'default'           => array(),
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_user_defined_breakpoints' ),
			)
		);

		$args = array(
			'name'                     => $name,
			'allowed_breakpoint_units' => self::$allowed_breakpoint_units,
			'options'                  => self::get_active_user_defined_breakpoints(),
		);

		add_settings_field(
			$name,
			__( 'Breakpoints', 'dt-cr' ),
			function ( $args ) {
				self::parse_template( 'breakpoints', $args );
			},
			self::MENU_PAGE_SLUG,
			DT_CR_PLUGIN_ID . '_settings_section',
			$args
		);
	}

	/**
	 * Sanitizes the user-defined breakpoints.
	 * In case of invalid input just remove invalid option.
	 *
	 * @param array $options
	 * @return array sanitized options.
	 */
	public static function sanitize_user_defined_breakpoints( $options ) {
		$current_breakpoints = self::get_user_defined_breakpoints();

		// if not valid
		// new breakpoints - just ignore
		// current breakpoints - use old values
		foreach ( $options as $key => $data ) {
			$sanitized           = $data;
			$sanitized['name']   = (string) $data['name'];
			$sanitized['value']  = floatval( $data['value'] );
			$sanitized['active'] = true;

			$options[ $key ] = $sanitized;

			if ( ! in_array( $sanitized['unit'], self::$allowed_breakpoint_units )
			|| empty( $sanitized['name'] ) || strlen( $sanitized['name'] ) > 20
			|| empty( $sanitized['value'] ) || $sanitized['value'] < 0 || $sanitized['value'] > 9999
			) {
				if ( array_key_exists( $key, $current_breakpoints ) ) {
					$options[ $key ] = $current_breakpoints[ $key ];
				} else {
					unset( $options[ $key ] );
				}
				continue;
			}
		}

		// removed breakpoints have to be marked as inactive
		foreach ( array_diff_key( $current_breakpoints, $options ) as $key => $data ) {
			$options[ $key ]           = $data;
			$options[ $key ]['active'] = false;
		}

		return $options;
	}

	/**
	 * Remove all options stored by the plugin.
	 *
	 * @return void
	 */
	public static function on_uninstall() {
		delete_option( self::build_user_defined_breakpoints_option_name() );

		$modules_data = ModulesManager::get_managable_modules_data();

		foreach ( $modules_data as $module_data ) {
			delete_option( self::build_module_enabled_option_name( $module_data['identifier'] ) );
		}
	}

	private static function add_module_enable_checkbox( $module_identifier, $title, $args = array() ) {
		$name = self::build_module_enabled_option_name( $module_identifier );
		register_setting(
			DT_CR_PLUGIN_ID . '_settings',
			$name,
			array(
				'default' => '1',
				'type'    => 'boolean',
			)
		);

		$args = array(
			'identifier'  => $name,
			'title'       => $title,
			'label'       => $args['label'] ?? null,
			'description' => $args['description'] ?? null,
			'enabled'     => self::is_module_enabled( $module_identifier ),
		);

		add_settings_field(
			$name,
			$title,
			function ( $args ) {
				self::parse_template( '_checkbox', $args );
			},
			self::MENU_PAGE_SLUG,
			DT_CR_PLUGIN_ID . '_settings_section',
			$args
		);
	}

	/**
	 * Parse a template file based on the provided template name and arguments.
	 * Prints parsed template content.
	 *
	 * @param string $template_name filename of the template (without extension)
	 * @param array  $args variables to be set in template
	 * @throws Exception in case the template file cannot be found or read.
	 *
	 * @return void
	 */
	private static function parse_template( $template_name, $args ) {
		$template_full_name =
			DT_CR_TEMPLATES_DIR . self::TEMPLATES_FOLDER_NAME . $template_name . '.php';

		if ( ! is_file( $template_full_name ) || ! is_readable( $template_full_name ) ) {
			throw new Exception( 'Can not read template: ' . $template_full_name );
		}

		include $template_full_name;
	}

	/**
	 * Returns an array containing the default user defined breakpoints.
	 *
	 * @return array
	 */
	private static function get_default_user_defined_breakpoints() {
		return array(
			'mobile' => array(
				'name'   => 'Mobile',
				'value'  => '480',
				'unit'   => 'px',
				'active' => true,
			),
			'tablet' => array(
				'name'   => 'Tablet',
				'value'  => '960',
				'unit'   => 'px',
				'active' => true,
			),
		);
	}

	/**
	 * Builds the option name used in options API to store the state of a module.
	 *
	 * @param string $module_identifier
	 * @return string
	 */
	private static function build_module_enabled_option_name( $module_identifier ) {
		return DT_CR_PLUGIN_ID . '__module__' . $module_identifier . '__enabled';
	}


	/**
	 * Builds the option name used in options API to store the user defined responsiveness breakpoints.
	 *
	 * @return string The option name.
	 */
	private static function build_user_defined_breakpoints_option_name() {
		return DT_CR_PLUGIN_ID . '__user-defined-responsiveness-breakpoints';
	}
}
