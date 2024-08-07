<?php
/**
 * Pattern Navigation test.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","className":"is-style-asterisk"} -->
		<h2 class="wp-block-heading has-text-align-center is-style-asterisk">An array of resources</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","style":{"layout":{"selfStretch":"fit","flexSize":null}}} -->
		<p class="has-text-align-center">Our comprehensive suite of professional services caters to a diverse clientele, ranging from homeowners to commercial developers.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
	<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"},"dtCrResponsive":{"breakpoint":"tablet","orientation":"column"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"layout":{"selfStretch":"fit","flexSize":null}},"layout":{"type":"constrained","justifyContent":"center"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"level":3,"className":"is-style-asterisk"} -->
				<h3 class="wp-block-heading is-style-asterisk">Études Architect App</h3>
				<!-- /wp:heading -->

				<!-- wp:list {"className":"is-style-checkmark-list","style":{"typography":{"lineHeight":"1.75"}}} -->
				<ul style="line-height:1.75" class="wp-block-list is-style-checkmark-list">
					<!-- wp:list-item -->
					<li>Collaborate with fellow architects.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Showcase your projects.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Experience the world of architecture.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"layout":{"selfStretch":"fixed","flexSize":"50%"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:image {"scale":"cover","sizeSlug":"large","linkDestination":"none","className":"is-style-rounded"} -->
				<figure class="wp-block-image size-large is-style-rounded">
					<img src="https://wp-themes.com/wp-content/themes/twentytwentyfour/assets/images/tourist-and-building.webp" alt="Tourist taking photo of a building" style="object-fit:cover"/>
				</figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
		<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"},"dtCrResponsive":{"breakpoint":"tablet","orientation":"column-reverse"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"layout":{"selfStretch":"fixed","flexSize":"50%"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-rounded"} -->
				<figure class="wp-block-image size-large is-style-rounded">
					<img src="https://wp-themes.com/wp-content/themes/twentytwentyfour/assets/images/windows.webp" alt="Windows of a building in Nuremberg, Germany"/>
				</figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"layout":{"selfStretch":"fit","flexSize":""}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"level":3,"className":"is-style-asterisk"} -->
				<h3 class="wp-block-heading is-style-asterisk">Études Newsletter</h3>
				<!-- /wp:heading -->

				<!-- wp:list {"className":"is-style-checkmark-list","style":{"typography":{"lineHeight":"1.75"}}} -->
				<ul style="line-height:1.75" class="wp-block-list is-style-checkmark-list">
					<!-- wp:list-item -->
					<li>A world of thought-provoking articles.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Case studies that celebrate architecture.</li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li>Exclusive access to design insights.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
		<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
