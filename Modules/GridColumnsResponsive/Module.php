<?php

namespace DtCr\Modules\GridColumnsResponsive;

use DtCr\Base\ModuleBase;
use DtCr\Base\ManagableModuleInterface;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'grid-columns-responsive';

	const SETTINGS_ORDER = 500;

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function init() {
		register_block_type( DT_CR_BLOCKS_DIR . 'responsive-grid-column' );
		register_block_type( DT_CR_BLOCKS_DIR . 'responsive-grid' );
	}

	public static function get_title() {
		return __( 'Grid Block', 'dt-cr' );
	}

	public static function get_label() {
		return __( 'Add responsive Grid block to the block editor.', 'dt-cr' );
	}
}
