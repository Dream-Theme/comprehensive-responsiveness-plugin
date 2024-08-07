import { __ } from '@wordpress/i18n';
import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

export default function VisibilityControl( { value = '', onChange } ) {
	return (
		<>
			<ToggleGroupControl
				__nextHasNoMarginBottom
				size={ '__unstable-large' }
				label={ __( 'Block visibility', 'dt-cr' ) }
				value={ value || '' }
				onChange={ onChange }
				isBlock={ true }
			>
				<ToggleGroupControlOption
					key={ 'visible' }
					value={ '' }
					label={ __( 'Visible', 'dt-cr' ) }
				/>
				<ToggleGroupControlOption
					key={ 'hidden' }
					value={ 'hidden' }
					label={ __( 'Hidden', 'dt-cr' ) }
				/>
			</ToggleGroupControl>
		</>
	);
}
