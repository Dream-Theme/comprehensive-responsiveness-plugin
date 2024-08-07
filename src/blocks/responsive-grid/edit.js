import ResponsiveBreakpointControl, {
	BREAKPOINT_OPTION_CUSTOM,
} from '@dt-cr/components/responsive-breakpoint-control';
import { BLOCK_PREFIX } from '@dt-cr/constants';
import { addCssClasses } from '@dt-cr/css-classes-utils';
import { getSwitchWidth } from '@dt-cr/responsive';
import {
	BlockControls,
	BlockVerticalAlignmentToolbar,
	InspectorControls,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { useSelect, withDispatch } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { buildCssClasses, buildCssVariables } from './utils';

import ResponsiveBreakpointCustomValue from '@dt-cr/components/responsive-breakpoint-custom-value';
import { useAvailableUnits } from '@dt-cr/hooks/useAvailableUnits';
import { useHandleDeletedUserBreakpoint } from '@dt-cr/hooks/useHandleDeletedUserBreakpoint';
import { useAddEditorStyle } from 'dt-cr/editor-css-store';
import { FEATURE_NAME } from './constants';
import './editor.scss';

function buildBlockUniqueCssClassName( clientId ) {
	return BLOCK_PREFIX + FEATURE_NAME + '__' + clientId;
}

function buildResponsivenessCss( attributes, clientId ) {
	const switchWidth = getSwitchWidth(
		attributes.stackedViewBreakpoint,
		attributes.stackedViewBreakpointCustomValue
	);

	if ( ! switchWidth ) return '';

	const blockUniqueCssClassName = buildBlockUniqueCssClassName( clientId );

	// implement stack after breakpoint use CSS class twice to increase it's weight
	return `@media screen and (width <= ${ switchWidth }) {
		body .${ blockUniqueCssClassName }.${ blockUniqueCssClassName } {
			grid-template-columns: repeat(1, 1fr);
		}
	}`;
}
const ALLOWED_BLOCKS = [ 'dt-cr/responsive-grid-column' ];

function ColumnsEditContainer( { attributes, setAttributes, updateAlignment, clientId } ) {
	const {
		displayMode,
		columnMinWidth,
		columnsAmount,
		stackedViewBreakpoint,
		stackedViewBreakpointCustomValue,
		verticalAlignment,
		templateLock,
	} = attributes;

	// if breakpoint was deactivated by user, reset to custom one with the same breakpoint value
	useHandleDeletedUserBreakpoint( stackedViewBreakpoint, ( newValue ) =>
		setAttributes( {
			stackedViewBreakpoint: BREAKPOINT_OPTION_CUSTOM,
			stackedViewBreakpointCustomValue: newValue,
		} )
	);

	const units = useAvailableUnits();

	const responsivenessCss = useMemo(
		() => buildResponsivenessCss( attributes, clientId ),
		[ attributes, clientId ]
	);

	const blockRef = useAddEditorStyle(
		responsivenessCss,
		buildBlockUniqueCssClassName( clientId )
	);

	const blockProps = useBlockProps( {
		className: addCssClasses(
			buildCssClasses( attributes ),
			buildBlockUniqueCssClassName( clientId )
		),
		style: buildCssVariables( attributes ),
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		templateLock,
	} );

	return (
		<>
			<BlockControls>
				<BlockVerticalAlignmentToolbar
					onChange={ updateAlignment }
					value={ verticalAlignment }
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					initialOpen={ true }
					title={ __( 'Display mode', 'dt-cr' ) }
					className="dt-cr responsive-grid-display-mode"
				>
					<ToggleGroupControl
						__nextHasNoMarginBottom
						value={ displayMode || 'witdh' }
						onChange={ ( value ) => {
							setAttributes( { displayMode: value } );
						} }
						label={ __( 'Set number of columns by', 'dt-cr' ) }
						help={ __( 'Define column number by width or count.', 'dt-cr' ) }
						isBlock={ true }
					>
						<ToggleGroupControlOption
							key={ 'width' }
							value={ 'width' }
							label={ __( 'Minimum width', 'dt-cr' ) }
						/>
						<ToggleGroupControlOption
							key={ 'amount' }
							value={ 'amount' }
							label={ __( 'Target number', 'dt-cr' ) }
						/>
					</ToggleGroupControl>

					{ 'amount' === displayMode && (
						<RangeControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Target number of columns', 'dt-cr' ) }
							value={ columnsAmount }
							onChange={ ( value ) => setAttributes( { columnsAmount: value } ) }
							min={ 1 }
							max={ Math.max( 12, columnsAmount ) }
						/>
					) }
					<UnitControl
						label={ __( 'Column minimum width', 'dt-cr' ) }
						labelPosition="top"
						size={ '__unstable-large' }
						value={ columnMinWidth || '' }
						onChange={ ( nextWidth ) => {
							nextWidth = 0 > parseFloat( nextWidth ) ? '0' : nextWidth;
							setAttributes( { columnMinWidth: nextWidth } );
						} }
						units={ units }
					/>

					<ResponsiveBreakpointControl
						label={ __( 'Stack On', 'dt-cr' ) }
						value={ stackedViewBreakpoint }
						onChange={ ( value ) => {
							const stakedViewAttributes = {
								stackedViewBreakpoint: value,
								stackedViewBreakpointCustomValue:
									value !== BREAKPOINT_OPTION_CUSTOM
										? undefined
										: stackedViewBreakpointCustomValue,
							};

							setAttributes( stakedViewAttributes );
						} }
						help={
							stackedViewBreakpoint !== BREAKPOINT_OPTION_CUSTOM &&
							__(
								'Stack columns and stretch them to 100% at this breakpoint and below.',
								'dt-cr'
							)
						}
					/>
					{ stackedViewBreakpoint === BREAKPOINT_OPTION_CUSTOM && (
						<ResponsiveBreakpointCustomValue
							value={ stackedViewBreakpointCustomValue }
							onChange={ ( value ) => {
								setAttributes( {
									stackedViewBreakpointCustomValue: value,
								} );
							} }
							help={ __(
								'Stack columns and stretch them to 100% at this breakpoint and below.',
								'dt-cr'
							) }
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } ref={ blockRef } />
		</>
	);
}

const ColumnsEditContainerWrapper = withDispatch( ( dispatch, ownProps, registry ) => ( {
	/**
	 * Update all child Column blocks with a new vertical alignment setting
	 * based on whatever alignment is passed in. This allows change to parent
	 * to overide anything set on a individual column basis.
	 *
	 * @param {string} verticalAlignment the vertical alignment setting
	 */
	updateAlignment( verticalAlignment ) {
		const { clientId, setAttributes } = ownProps;
		const { updateBlockAttributes } = dispatch( blockEditorStore );
		const { getBlockOrder } = registry.select( blockEditorStore );

		// Update own alignment.
		setAttributes( { verticalAlignment } );

		// Update all child Column Blocks to match.
		const innerBlockClientIds = getBlockOrder( clientId );
		innerBlockClientIds.forEach( ( innerBlockClientId ) => {
			updateBlockAttributes( innerBlockClientId, {
				verticalAlignment,
			} );
		} );
	},
} ) )( ColumnsEditContainer );

function Placeholder() {
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: [ [ 'dt-cr/responsive-grid-column' ] ],
		templateInsertUpdatesSelection: true,
	} );

	return <div { ...innerBlocksProps } />;
}

const ColumnsEdit = ( props ) => {
	const { clientId } = props;

	const hasInnerBlocks = useSelect(
		( select ) => select( blockEditorStore ).getBlocks( clientId ).length > 0,
		[ clientId ]
	);
	const Component = hasInnerBlocks ? ColumnsEditContainerWrapper : Placeholder;

	return <Component { ...props } />;
};

export default ColumnsEdit;
