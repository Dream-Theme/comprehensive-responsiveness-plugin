<?php
/**
 * Editor CSS Store
 *
 * @package DtCr
 */

namespace DtCr\Modules\EditorCssStore;

use DtCr\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-editor-css-store';
	const ASSETS_BUILD_PATH = 'editor/editor-css-store/';

	const IS_CORE_MODULE = true;

	/**
	 * !!! IMPORTANT !!!
	 * rewrite to MATCH HANDLE in webpack settings
	 *
	 * @see parent::build_script_handle() comment
	 */
	protected function build_script_handle( $key ) {
		return 'dt-cr-editor-css-store';
	}

	/**
	 * It's a core module which is included in dependency lists of other modules
	 * need only register script but not include it manually somewhere
	 *
	 * also register here style to fix margins in block editor (details in readme)
	 */
	protected function process_assets() {
		$asset_file = require $this->get_assets_full_path() . 'index.asset.php';

		wp_register_script(
			$this->build_script_handle( 'index' ),
			DT_CR_URL_DIST . $this::ASSETS_BUILD_PATH . 'index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);

		wp_register_style(
			$this->build_style_handle( 'index' ),
			DT_CR_URL_DIST . $this::ASSETS_BUILD_PATH . 'index.css',
			array(),
			$asset_file['version']
		);

		add_action(
			'enqueue_block_assets',
			function () {
				$this->enqueue_assets( 'index' );
			}
		);
	}
}
