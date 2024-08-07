<?php
namespace DtCr\Base;

defined( 'ABSPATH' ) || exit;

interface ManagableModuleInterface extends ModuleInterface {

	/**
	 * User friendly module title to be displayed in settings page.
	 *
	 * @return string
	 */
	public static function get_title();

	/**
	 * User friendly label for module enable checkbox to be displayed in settings page.
	 *
	 * @return string
	 */
	public static function get_label();

	/**
	 * User friendly module description text to be displayed in settings page.
	 *
	 * @return string
	 */
	public static function get_description();

	/**
	 * Order of module enable checkbox to be displayed in settings page.
	 *
	 * @return int
	 */
	public static function get_settings_order();
}
