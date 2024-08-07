<?php
/**
 * Extend Row block
 *
 * @package DtCr
 */

namespace DtCr\Modules\RowResponsive;

use DtCr\Base\ManagableModuleInterface;
use DtCr\Base\ModuleBase;
use DtCr\Core\BlockUtils;
use DtCr\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'row-responsive';
	const ASSETS_BUILD_PATH = 'editor/blocks/row/responsiveness/';

	const SETTINGS_ORDER = 200;

	const ATTRIBUTES = 'dtCrResponsive';
	const BlOCK_NAME = 'core/group';

	public function setup_hooks() {
		add_filter( 'render_block', array( $this, 'render' ), 20, 3 );
	}

	function render( $block_content, $block ) {
		if ( ! isset( $block['blockName'] ) || $block['blockName'] !== self::BlOCK_NAME ) {
			return $block_content;
		}

		$attributes = isset( $block['attrs'] ) ? $block['attrs'] : null;

		if ( ! isset( $attributes['layout'] ) || ! isset( $attributes['layout']['type'] ) || $attributes['layout']['type'] !== 'flex' || ! isset( $attributes[ self::ATTRIBUTES ] ) ) {
			return $block_content;
		}

		$class_id       = BlockUtils::get_class_id_from_atts( $attributes );
		$custom_classes = $this->get_custom_classes( $attributes, $class_id );
		$block_content  = BlockUtils::append_classes( $block_content, $custom_classes );
		$this->add_styles( $attributes, $class_id );

		return $block_content;
	}

	/**
	 * @param array $attributes Block attributes.
	 *
	 * @return array Custom classes to be added on render.
	 */
	function get_custom_classes( $attributes, $class_id ) {
		$custom_classes = array();

		$custom_classes[] = $class_id;

		return $custom_classes;
	}

	function add_styles( $attributes, $class_id ) {
		$atts = $this->get_atts( $attributes[ self::ATTRIBUTES ] );

		$breakpoint              = $atts['breakpoint'] ?? null;
		$breakpoint_custom_value = $atts['breakpointCustomValue'] ?? null;
		$justification           = $atts['justification'] ?? null;
		$orientation             = $atts['orientation'] ?? null;

		$switch_width = CssMediaBreakpoints::getSwitchWidth( $breakpoint, $breakpoint_custom_value );
		if ( ! $switch_width ) {
			return;
		}

		// Used with the default, horizontal(row) flex orientation.
		$justify_map = array(
			'left'          => 'flex-start',
			'right'         => 'flex-end',
			'center'        => 'center',
			'stretch'       => 'stretch',
			'space-between' => 'space-between',
		);

		// Used with the vertical (column) flex orientation.
		$align_items_map = array(
			'left'    => 'flex-start',
			'right'   => 'flex-end',
			'center'  => 'center',
			'stretch' => 'stretch',
		);

		$reverse_map = array(
			'left'          => 'right',
			'right'         => 'left',
			'center'        => 'center',
			'stretch'       => 'stretch',
			'space-between' => 'space-between',
		);

		$declarations = array();

		if ( ! empty( $justification ) ) {
			if ( $orientation && ( $orientation === 'column' || $orientation === 'column-reverse' ) ) {
				$declarations['align-items'] = "{$align_items_map[$justification]}";
			} else {
				$justification_fixed = $justification;
				if ( $orientation === 'row-reverse' || $orientation === 'column-reverse' ) {
					$justification_fixed = $reverse_map[ $justification ];
				}
				$declarations['justify-content'] = "{$justify_map[$justification_fixed]}";
			}
		}

		if ( ! empty( $orientation ) ) {
			$declarations['flex-direction'] = "{$orientation}";
		}

		if ( empty( $declarations ) ) {
			return;
		}

		BlockUtils::add_styles_from_css_rules(
			array(
				array(
					'selector'     => "@media screen and (width <= {$switch_width})",
					'declarations' => array(
						array(
							'selector'     => "body .{$class_id}.{$class_id}",
							'declarations' => $declarations,
						),
					),
				),
			)
		);
	}

	function get_atts( $attributes ) {
		return wp_parse_args(
			$attributes,
			array(
				'breakpoint'            => '',
				'breakpointCustomValue' => '',
				'justification'         => '',
				'orientation'           => '',
			)
		);
	}

	public static function get_title() {
		return __( 'Responsive Rows', 'dt-cr' );
	}

	public static function get_label() {
		return __( 'Add responsiveness settings to Row block.', 'dt-cr' );
	}
}
