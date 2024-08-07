<?php
/**
 * To all blocks in frontend add attribute with unique identifier
 * that can be used later to add unique CSS class
 *
 * @package DtCr
 */

namespace DtCr\Modules\AddBlockIdentifier;

use DtCr\Base\ModuleBase;
use DtCr\Core\BlockUtils;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase {

	const MODULE_IDENTIFIER = 'core-add-block-identifier';
	const IS_CORE_MODULE    = true;

	public function setup_hooks() {
		add_filter( 'render_block_data', array( $this, 'modify_render_block_data' ), 20 );
	}

	/**
	 * @param array $parsed_block The block to be rendered.
	 *
	 * @return array
	 */
	public function modify_render_block_data( $parsed_block ) {
		if ( isset( $parsed_block['blockName'] ) ) {
			$parsed_block['attrs'][ BlockUtils::BLOCK_ID ] = wp_unique_prefixed_id( BlockUtils::BLOCK_ID_PREFIX );
		}

		return $parsed_block;
	}
}
