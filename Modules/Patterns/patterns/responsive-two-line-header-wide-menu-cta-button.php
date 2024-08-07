<?php
/**
 * Pattern Responsive two line header with wide menu and CTA button.
 *
 * @package DtCr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"20px","bottom":"20px"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-base-background-color has-background" style="padding-top:20px;padding-bottom:20px">
	<!-- wp:group {"align":"wide","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch","verticalAlignment":"center"},"dtCrResponsive":{"breakpoint":"tablet","orientation":"row"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"metadata":{"name":"Site Identity"},"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","justifyContent":"left"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":60} /-->

			<!-- wp:group {"style":{"spacing":{"blockGap":"4px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:site-title {"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} /-->

				<!-- wp:site-tagline {"fontSize":"small"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"align":"right","style":{"layout":{"selfStretch":"fill","flexSize":null}},"dtCrVisibility":{"breakpoint":"tablet"}} -->
			<p class="has-text-align-right">+1 (234) 567 89 12</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"metadata":{"name":"Site Navigation"},"style":{"layout":{"selfStretch":"fill","flexSize":null}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"},"dtCrResponsive":{"breakpoint":"tablet","orientation":"row-reverse","justification":"right"}} -->
		<div class="wp-block-group">
			<!-- wp:navigation {"icon":"menu","layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"},"style":{"spacing":{"margin":{"top":"0"},"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fit","flexSize":null}},"dtCrOverlayMenu":{"breakpoint":"tablet"}} -->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Link 1', 'dt-cr' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Link 2', 'dt-cr' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Link 3', 'dt-cr' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Link 4', 'dt-cr' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Link 5', 'dt-cr' ); ?>","url":"#"} /-->
			<!-- /wp:navigation --> 

			<!-- wp:buttons {"metadata":{"name":"Action Button"},"layout":{"type":"flex","orientation":"horizontal"},"dtCrVisibility":{"breakpoint":"mobile"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-fill"} -->
				<div class="wp-block-button is-style-fill">
					<a class="wp-block-button__link wp-element-button">Click Me</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
