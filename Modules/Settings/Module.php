<?php

namespace DtCr\Modules\Settings;

use DtCr\Base\ModuleBase;
use DtCr\Core\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Expose user defined responsiveness breakpoints to frontend
 */
class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-settings';
	const ASSETS_BUILD_PATH = 'editor/plugins/settings/';

	const IS_CORE_MODULE = true;

	/**
	 * rewrite processing assets as we need to increase priority in enqueue_block_editor_assets
	 * default priority is 10 and it's too late as when all blocks are loaded in editor already
	 * but we need user defined breakpoints to be used in our blocks
	 * 9 is fine (empirically)
	 */
	protected function process_assets() {
		// editor interface assets
		if ( ! file_exists( $this->get_assets_full_path() . $this::EDITOR_ASSET_KEY . '.js' ) ) {
			return;
		}

		$this->register_assets( $this::EDITOR_ASSET_KEY );

		add_action(
			'enqueue_block_editor_assets',
			function () {
				$this->enqueue_assets( $this::EDITOR_ASSET_KEY );
			},
			9
		);
	}

	protected function enqueue_assets( $key ) {
		$script_handle = $this->build_script_handle( $key );

		$js_user_defined_breakpoints = array();

		foreach ( Settings::get_user_defined_breakpoints() as $key => $breakpoint ) {
			$js_data           = array();
			$js_data['key']    = $key;
			$js_data['name']   = $breakpoint['name'];
			$js_data['value']  = $breakpoint['value'] . $breakpoint['unit'];
			$js_data['active'] = $breakpoint['active'] ? true : false;

			$js_user_defined_breakpoints[] = $js_data;
		}

		wp_localize_script(
			$script_handle,
			'DT_CR_USER_DEFINED_RESPONSIVE_BREAKPOINTS',
			$js_user_defined_breakpoints
		);

		wp_enqueue_script( $script_handle );
	}
}
