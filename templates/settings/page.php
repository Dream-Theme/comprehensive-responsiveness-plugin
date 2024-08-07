<?php defined( 'ABSPATH' ) || exit; ?>

<div class="wrap">

	<h1><?php echo __( 'Responsiveness Settings', 'dt-cr' ); ?></h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( DT_CR_PLUGIN_ID . '_settings' );

		do_settings_sections( 'responsiveness-settings' );


		submit_button( __( 'Save Settings', 'dt-cr' ) );
		?>
	</form>

</div>
