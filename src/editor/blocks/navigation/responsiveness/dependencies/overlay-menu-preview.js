/**
 * copy of 
 * @link https://github.com/WordPress/gutenberg/blob/v17.7.0/packages/block-library/src/navigation/edit/overlay-menu-preview.js
 */

/**
 * WordPress dependencies
 */
import {
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import OverlayMenuIcon from './overlay-menu-icon';

export default function OverlayMenuPreview( { setAttributes, hasIcon, icon } ) {
	return (
		<>
			<ToggleControl
				__nextHasNoMarginBottom
				label={ __( 'Show icon button' ) }
				help={ __(
					'Configure the visual appearance of the button that toggles the overlay menu.'
				) }
				onChange={ ( value ) => setAttributes( { hasIcon: value } ) }
				checked={ hasIcon }
			/>

			<ToggleGroupControl
				__nextHasNoMarginBottom
				label={ __( 'Icon' ) }
				value={ icon }
				onChange={ ( value ) => setAttributes( { icon: value } ) }
				isBlock
			>
				<ToggleGroupControlOption
					value="handle"
					aria-label={ __( 'handle' ) }
					label={ <OverlayMenuIcon icon="handle" /> }
				/>
				<ToggleGroupControlOption
					value="menu"
					aria-label={ __( 'menu' ) }
					label={ <OverlayMenuIcon icon="menu" /> }
				/>
			</ToggleGroupControl>
		</>
	);
}
