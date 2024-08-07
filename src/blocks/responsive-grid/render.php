<?php
use DtCr\Core\BlockUtils;
use DtCr\Core\CssMediaBreakpoints;

defined( 'ABSPATH' ) || exit;

$switch_width = CssMediaBreakpoints::getSwitchWidth(
	$attributes['stackedViewBreakpoint'] ?? null,
	$attributes['stackedViewBreakpointCustomValue'] ?? null
);

if ( null !== $switch_width ) {
	$class_id = BlockUtils::get_class_id_from_atts( $attributes );
	BlockUtils::add_styles_from_css_rules(
		array(
			array(
				'selector'     => "@media screen and (width <= {$switch_width})",
				'declarations' => array(
					array(
						'selector'     => "body .{$class_id}.{$class_id}",
						'declarations' => array( 'grid-template-columns' => 'repeat(1, 1fr)' ),
					),
				),
			),
		)
	);

	$content = BlockUtils::append_classes( $content, array( $class_id ) );
}
?>
	
<?php
echo $content;
