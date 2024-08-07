import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { arrowDown, arrowLeft, arrowRight, arrowUp } from '@wordpress/icons';

const options = [
	{
		value: 'row',
		icon: arrowRight,
		label: __( 'Horizontal' ),
	},
	{
		value: 'column',
		icon: arrowDown,
		label: __( 'Vertical' ),
	},
	{
		value: 'row-reverse',
		icon: arrowLeft,
		label: __( 'Horizontal inversed' ),
	},
	{
		value: 'column-reverse',
		icon: arrowUp,
		label: __( 'Vertical inversed' ),
	},
];

export default function OrientationControl( { justification, orientation, onChange } ) {
	return (
		<>
			<ToggleGroupControl
				__nextHasNoMarginBottom
				label={ __( 'Orientation' ) }
				value={ orientation }
				onChange={ ( value ) => {
					// Make sure the vertical alignment and justification are compatible with the new orientation.
					let newJustification = justification;
					if ( value === 'row' || value === 'row-reverse' ) {
						if ( justification === 'stretch' ) {
							newJustification = 'left';
						}
					} else if ( justification === 'space-between' ) {
						newJustification = 'left';
					}
					onChange( {
						orientation: value,
						justification: newJustification,
					} );
				} }
				isDeselectable={ true }
				className="block-editor-hooks__flex-layout-justification-controls"
			>
				{ options.map( ( { value, icon, label } ) => {
					return (
						<ToggleGroupControlOptionIcon
							key={ value }
							value={ value }
							icon={ icon }
							label={ label }
						/>
					);
				} ) }
			</ToggleGroupControl>
		</>
	);
}
