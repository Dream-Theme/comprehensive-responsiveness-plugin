<?php
/**
 * Extend Columns block
 *
 * @package DtCr
 */

namespace DtCr\Modules\ColumnsResponsive;

use DtCr\Base\ModuleBase;
use DtCr\Base\ManagableModuleInterface;
use DtCr\Core\BlockUtils;
use DtCr\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'columns-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/columns/stack-on-responsive/';

	const SETTINGS_ORDER = 400;

	const ATTRIBUTES                        = 'dtCrStackOn';
	const ATTRIBUTE_BREAKPOINT              = 'breakpoint';
	const ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE = 'breakpointCustomValue';

	const COLUMNS_BlOCK_NAME = 'core/columns';
	const COLUMN_BlOCK_NAME  = 'core/column';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render_columns' ), 20, 3 );
		add_filter( 'render_block', array( $this, 'render_column' ), 20, 3 );
	}

	/**
	 * @param string $block_content The block frontend output.
	 * @param array  $block         The block info and attributes.
	 *
	 * @return mixed                Return $block_content
	 */
	function render_column( $block_content, $block ) {
		if ( ! isset( $block['blockName'] ) || $block['blockName'] !== self::COLUMN_BlOCK_NAME ) {
			return $block_content;
		}
		$attributes = isset( $block['attrs'] ) ? $block['attrs'] : null;

		if ( ! isset( $attributes['width'] ) ) {
			return $block_content;
		}

		$class_id      = BlockUtils::get_class_id_from_atts( $attributes );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		$column_selector = ".wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column.{$class_id}[style*=flex-basis]";

		BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => $column_selector,
					'declarations' => array(
						'flex-basis' => $attributes['width'] . ' !important',
					),
				),
			)
		);

		return $block_content;
	}

	/**
	 * @param string $block_content The block frontend output.
	 * @param array  $block         The block info and attributes.
	 *
	 * @return mixed                Return $block_content
	 */
	function render_columns( $block_content, $block ) {
		if ( ! isset( $block['blockName'] ) || $block['blockName'] !== self::COLUMNS_BlOCK_NAME ) {
			return $block_content;
		}

		$attributes = isset( $block['attrs'] ) ? $block['attrs'] : null;

		$class_id      = BlockUtils::get_class_id_from_atts( $attributes );
		$block_content = BlockUtils::append_classes( $block_content, array( $class_id ) );

		// native attribute isStackedOnMobile is changed together with our the custom attribute
		if ( ! ( $attributes['isStackedOnMobile'] ?? true ) ) {
			// add is-not-stacked-on-mobile class as stacking is turned off
			$block_content = BlockUtils::append_classes( $block_content, array( 'is-not-stacked-on-mobile' ) );
		} else {
			$this->add_styles_to_columns( $attributes, $class_id );
		}

		return $block_content;
	}

	/**
	 * in case we just turned module on and there is no settings in self::ATTRIBUTES
	 * we use value based on isStackedOnMobile attribute
	 *
	 * @param array $attributes
	 */
	private function get_breakpoint_from_attributes( $attributes ) {
		if ( isset( $attributes[ self::ATTRIBUTES ] ) ) {
			return $attributes[ self::ATTRIBUTES ];
		}

		// if isStackedOnMobile not set it equals true (see core/columns block.json default value)
		return array(
			self::ATTRIBUTE_BREAKPOINT              => ( $attributes['isStackedOnMobile'] ?? true )
				? CssMediaBreakpoints::BREAKPOINT_NAME_MOBILE
				: CssMediaBreakpoints::BREAKPOINT_NAME_OFF,
			self::ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE => null,
		);
	}


	function add_styles_to_columns( $attributes, $class_id ) {
		$breakpoint_data = $this->get_breakpoint_from_attributes( $attributes );

		$switch_width = CssMediaBreakpoints::getSwitchWidth(
			$breakpoint_data[ self::ATTRIBUTE_BREAKPOINT ],
			$breakpoint_data[ self::ATTRIBUTE_BREAKPOINT_CUSTOM_VALUE ] ?? null
		);

		// prevent the columns from being stacked when custom breakpoint is empty
		// such stacking is caused by core/columns css rules
		$switch_width = $switch_width ?: '0px';

		// original CSS to stack on mobile
		/*
		@media (max-width: 781px) {
			.wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column {
			flex-basis: 100% !important;
			}
		}

		@media (min-width: 782px) {
			.wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column {
			flex-basis: 0;
			flex-grow: 1;
			}
			.wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column[style*=flex-basis] {
			flex-grow: 0;
			}
		}

		.wp-block-columns.is-not-stacked-on-mobile {
			flex-wrap: nowrap !important;
		}

		.wp-block-columns.is-not-stacked-on-mobile > .wp-block-column {
			flex-basis: 0;
			flex-grow: 1;
		}

		.wp-block-columns.is-not-stacked-on-mobile > .wp-block-column[style*=flex-basis] {
			flex-grow: 0;
		}
		*/

		$colums_block_selector         = ".wp-block-columns.{$class_id}.{$class_id}";
		$colums_block_stacked_selector = "$colums_block_selector:not(.is-not-stacked-on-mobile)";
		BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => ".wp-block-columns.{$class_id}",
					'declarations' => array( 'flex-wrap' => 'wrap !important' ),
				),
				array(
					'selector'     => "@media screen and (width <= {$switch_width})",
					'declarations' => array(
						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column.wp-block-column",
							'declarations' => array( 'flex-basis' => '100% !important' ),
						),
					),
				),
				array(
					'selector'     => "@media screen and (width > {$switch_width})",
					'declarations' => array(
						array(
							'selector'     => "$colums_block_selector",
							'declarations' => array( 'flex-wrap' => 'nowrap !important' ),
						),
						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column:not([style*=flex-basis])",
							'declarations' => array(
								'flex-basis' => '0 !important',
								'flex-grow'  => '1',
							),
						),
						array(
							'selector'     => "$colums_block_stacked_selector > .wp-block-column[style*=flex-basis]",
							'declarations' => array(
								'flex-grow' => '0',
							),
						),
					),
				),

				array(
					'selector'     => "$colums_block_selector.is-not-stacked-on-mobile",
					'declarations' => array( 'flex-wrap' => 'nowrap !important' ),
				),
			)
		);
	}

	public static function get_title() {
		return __( 'Responsive Columns', 'dt-cr' );
	}

	public static function get_label() {
		return __( 'Add responsiveness settings to Columns block.', 'dt-cr' );
	}
}
