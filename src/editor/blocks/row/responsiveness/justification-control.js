import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import {
	justifyCenter,
	justifyLeft,
	justifyRight,
	justifySpaceBetween,
	justifyStretch,
} from '@wordpress/icons';

export default function JustificationControl( { justification, orientation, onChange } ) {
	const justificationOptions = [
		{
			value: 'left',
			icon: justifyLeft,
			label: __( 'Justify items left' ),
		},
		{
			value: 'center',
			icon: justifyCenter,
			label: __( 'Justify items center' ),
		},
		{
			value: 'right',
			icon: justifyRight,
			label: __( 'Justify items right' ),
		},
	];

	if ( orientation === 'row' || orientation === 'row-reverse' ) {
		justificationOptions.push( {
			value: 'space-between',
			icon: justifySpaceBetween,
			label: __( 'Space between items' ),
		} );
	} else {
		justificationOptions.push( {
			value: 'stretch',
			icon: justifyStretch,
			label: __( 'Stretch items' ),
		} );
	}

	return (
		<>
			<ToggleGroupControl
				__nextHasNoMarginBottom
				label={ __( 'Justification' ) }
				value={ justification }
				onChange={ ( value ) => {
					onChange( {
						justification: value,
					} );
				} }
				isDeselectable={ true }
				className="block-editor-hooks__flex-layout-justification-controls"
			>
				{ justificationOptions.map( ( { value, icon, label } ) => {
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
