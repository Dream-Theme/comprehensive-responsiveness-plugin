<?php
/**
 * Add patterns
 *
 * @package DtCr
 */

namespace DtCr\Modules\Patterns;

use DtCr\Base\ManagableModuleInterface;
use DtCr\Base\ModuleBase;

defined( 'ABSPATH' ) || exit;

class Module extends ModuleBase implements ManagableModuleInterface {

	const MODULE_IDENTIFIER = 'patterns';

	const SETTINGS_ORDER = 600;

	public function setup_hooks() {
		add_action( 'init', array( $this, 'register_pattern_categories' ) );
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	public function register_pattern_categories() {
		register_block_pattern_category(
			'dt-cr-comprehensive-responsiveness',
			array( 'label' => __( 'Comprehensive Responsiveness', 'dt-cr' ) )
		);

		register_block_pattern_category(
			'dt-cr-responsive',
			array( 'label' => __( 'Responsive', 'dt-cr' ) )
		);

		register_block_pattern_category(
			'dt-cr-columns',
			array( 'label' => __( 'Columns', 'dt-cr' ) )
		);
	}

	public function get_patterns() {
		return array(
			'responsive-header-cta-button'     => array(
				'title'       => __( 'Simple Responsive Header with CTA Button', 'dt-cr' ),
				'categories'  => array( 'dt-cr-responsive', 'dt-cr-comprehensive-responsiveness', 'header' ),
				'content'     => $this->get_pattern_from_file( 'responsive-header-cta-button' ),
				'keywords'    => '',
				'description' => __( 'This header style offers a clean and simple design with a responsive layout and prominent Call-to-Action (CTA) button, ensuring it looks great on all devices.', 'dt-cr' ),
				'source'      => 'plugin',
			),

			'responsive-two-line-header-wide-menu-cta-button' => array(
				'title'       => __( 'Responsive Two-Line Header with Wide Menu and CTA Button', 'dt-cr' ),
				'categories'  => array( 'dt-cr-responsive', 'dt-cr-comprehensive-responsiveness', 'header' ),
				'content'     => $this->get_pattern_from_file( 'responsive-two-line-header-wide-menu-cta-button' ),
				'keywords'    => '',
				'description' => __( 'This header style features a responsive two-line design, accommodating a wide menu and a prominent Call-to-Action (CTA) button. The layout adjusts seamlessly across different screen sizes, ensuring optimal visibility and accessibility for users on any device.', 'dt-cr' ),
				'source'      => 'plugin',
			),

			'responsive-grid-layout'           => array(
				'title'       => __( 'Responsive Grid Layout', 'dt-cr' ),
				'categories'  => array( 'dt-cr-columns', 'dt-cr-comprehensive-responsiveness', 'dt-cr-responsive' ),
				'content'     => $this->get_pattern_from_file( 'responsive-grid-layout' ),
				'keywords'    => '',
				'description' => __( 'This layout style uses a Responsive Grid widget that adapts to the screen size, offering a flexible and consistent structure across various devices.', 'dt-cr' ),
				'source'      => 'plugin',
			),

			'responsive-rows-checkered-layout' => array(
				'title'       => __( 'Checkered Layout with Responsive Rows', 'dt-cr' ),
				'categories'  => array( 'dt-cr-columns', 'dt-cr-comprehensive-responsiveness', 'dt-cr-responsive' ),
				'content'     => $this->get_pattern_from_file( 'responsive-rows-checkered-layout' ),
				'keywords'    => '',
				'description' => __( 'This layout style uses a checkered pattern with rows that adjust responsively to the screen size, ensuring optimal viewing across different devices.', 'dt-cr' ),
				'source'      => 'plugin',

			),
		);
	}

	public function register_patterns() {
		$patterns = $this->get_patterns();

		foreach ( $patterns as $key => $settings ) {
			register_block_pattern( 'dt-cr/' . $key, $settings );
		}
	}

	/**
	 * Get the pattern from a file.
	 *
	 * @param string $file The file name.
	 *
	 * @return string The pattern content.
	 */
	public function get_pattern_from_file( $file ) {
		$pattern_file = __DIR__ . '/patterns/' . $file . '.php';

		if ( ! file_exists( $pattern_file ) ) {
			return '';
		}

		ob_start();
		require $pattern_file;

		return ob_get_clean();
	}

	public static function get_title() {
		return __( 'Responsive Patterns', 'dt-cr' );
	}

	public static function get_label() {
		return __( 'Add a collection of pre-made responsive Patterns to the block editor.', 'dt-cr' );
	}
}
