import { isValueSpacingPreset, getSpacingPresetCssVar } from '@wordpress/block-editor';
import classNames from 'classnames';

export function buildCssClasses( attributes ) {
	const { displayMode, verticalAlignment } = attributes;

	return classNames( {
		[ `are-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
		[ `dt-cr-responsive-grid-based-on-${ displayMode }` ]: true,
	} );
}

function handleSpacingPreset( value ) {
	// '0' is also treated as spacing preset for some reason =|
	// add px to '0', otherwise css is not working properly
	if ( '0' === value ) {
		return '0px';
	}

	return isValueSpacingPreset( value ) ? getSpacingPresetCssVar( value ) : value;
}

export function buildCssVariables( attributes ) {
	const { displayMode, columnMinWidth, columnsAmount, style = {} } = attributes;

	const cssVariables = {};
	// for gaps use the same defaults as in core/columns
	cssVariables[ '--gridHorizontalGap' ] =
		handleSpacingPreset( style?.spacing?.blockGap?.left ) || '1.2rem';

	cssVariables[ '--gridVerticalGap' ] =
		handleSpacingPreset( style?.spacing?.blockGap?.top ) || '1.2rem';

	if ( columnMinWidth ) {
		cssVariables[ '--columnMinWidth' ] = columnMinWidth;
	}

	if ( 'amount' === displayMode && columnsAmount ) {
		// cast to string, otherwise browser 'px' are added automatically
		cssVariables[ '--columnsAmount' ] = '' + columnsAmount;
	}

	return cssVariables;
}
