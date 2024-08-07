<?php defined( 'ABSPATH' ) || exit; ?>

<script type="text/javascript">
	function dtCrSettingsGetTemplate (fieldName, identifier, option) {
		const key = identifier || self.crypto.randomUUID().substring(19);
		const name = option?.name || '';
		const value = option?.value || null;
		const unit = option?.unit || 'px';

		// FIXME escape here ???
		const allowedUnits = []; 
		<?php
		foreach ( $args['allowed_breakpoint_units'] as $unit ) {
			?>
			allowedUnits.push( '<?php echo esc_js( $unit ); ?>' );
		<?php } ?>
		
		
		const unitSelect = 
			`<select name="${fieldName}[${key}][unit]">` 
			+ allowedUnits.map( el => `<option value="${el}" ${el === unit ? 'selected' : ''}>${el}</option>`).join('')
			+ '</select>';

		const removeButton = ['tablet', 'mobile'].includes(key) ? '' : `<span 
				class="dashicons dashicons-trash" 
				onclick="
					if (window.confirm('<?php echo __( 'Do you want to remove this breakpoint ?', 'dt-cr' ); ?>')) {
						this.parentNode.remove();
					}
					
					return false;
				"; 
				style="width: auto; height: auto; line-height: inherit; font-size: 1.5em; vertical-align: middle; cursor: pointer;"
				title="<?php echo __( 'Remove breakpoint', 'dt-cr' ); ?>"
			/>`;

		return `
		<div class="user-defined-breakpoint item" style="margin-bottom: .5em;">
			<input 
				name="${fieldName}[${key}][name]" 
				required
				type="text" 
				value="${name}" 
				size="15"
				maxlength="20"
			/>

			<input 
				name="${fieldName}[${key}][value]" 
				type="number"
				required
				min="0" 
				step="1"
				max="9999" 
				class="small-text"
				value="${value}" 
			/>
			
			${unitSelect}

			${removeButton}
		</div>
		`;
	} 
	
	function dtCrSettingsAddBreakpoint(event, fieldName) {
		event.stopPropagation();
		event.preventDefault();
		document.getElementById('user-defined-breakpoint-list').insertAdjacentHTML('beforeend', dtCrSettingsGetTemplate(fieldName));
	}
</script>

<div >
	<div id="user-defined-breakpoint-list">
	</div>

	<button 
		type="button" 
		class="button button-secondary" 
		onclick="dtCrSettingsAddBreakpoint(event, '<?php echo esc_js( $args['name'] ); ?>')"
	>
		<span 
			class="dashicons dashicons-plus" 
			style="width: auto; height: auto; font-size: 1.2em; vertical-align: middle;" 
			title="<?php echo __( 'Add breakpoint', 'dt-cr' ); ?>"
		/>
	</button>
</div>


<script type="text/javascript">
	<?php foreach ( is_array( $args['options'] ) ? $args['options'] : array() as $key => $data ) : ?>
		document.getElementById('user-defined-breakpoint-list').insertAdjacentHTML(
			'beforeend',
			dtCrSettingsGetTemplate(
				'<?php echo esc_js( $args['name'] ); ?>',
				'<?php echo esc_js( $key ); ?>',
				{
					name: '<?php echo esc_js( $data['name'] ); ?>',
					value: '<?php echo esc_js( $data['value'] ); ?>',
					unit: '<?php echo esc_js( $data['unit'] ); ?>',
				}
			)
		);  
	<?php endforeach; ?>
</script>
