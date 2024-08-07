import ResponsiveBreakpointControl, {
	BREAKPOINT_OPTION_CUSTOM,
	BREAKPOINT_OPTION_MOBILE,
	BREAKPOINT_OPTION_OFF,
} from '@dt-cr/components/responsive-breakpoint-control';
import ResponsiveBreakpointCustomValue from '@dt-cr/components/responsive-breakpoint-custom-value';
import { BLOCK_PREFIX } from '@dt-cr/constants';
import { addCssClasses } from '@dt-cr/css-classes-utils';
import { DtCrRefAnchor } from '@dt-cr/editor-css-store/components/DtCrRefAnchor';
import { useHandleDeletedUserBreakpoint } from '@dt-cr/hooks/useHandleDeletedUserBreakpoint';
import { getSwitchWidth } from '@dt-cr/responsive';
import { InspectorControls, store as blockEditorStore } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { Notice, PanelBody, RangeControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo, useState } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { useAddEditorStyle } from 'dt-cr/editor-css-store';
import { FEATURE_NAME } from './constants';
import {
	getMappedColumnWidths,
	getRedistributedColumnWidths,
	hasExplicitPercentColumnWidths,
	toWidthPrecision,
} from './dependencies/utils';
import './editor.scss';

const COLUMN_BLOCK_NAME = 'core/column';
const COLUMNS_BLOCK_NAME = 'core/columns';

function isApplyChanges( props ) {
	return props.name === COLUMNS_BLOCK_NAME;
}

/**
 * Try to stay as much close to the original behavior as possible:
 * in case stack on mobile was turned on we turn on our stack on mobile as well
 * and in case it was disabled we turn off our stack on
 * @param {*} attributes
 */
function getBreakpointFromAttributes( attributes ) {
	if ( attributes.dtCrStackOn ) {
		return attributes.dtCrStackOn;
	}

	return {
		breakpoint: attributes.isStackedOnMobile ? BREAKPOINT_OPTION_MOBILE : BREAKPOINT_OPTION_OFF,
		breakpointCustomValue: undefined,
	};
}

function modifyBlockData( settings, name ) {
	if ( name !== COLUMNS_BLOCK_NAME ) {
		return settings;
	}

	return {
		...settings,
		attributes: {
			...settings.attributes,
			dtCrStackOn: {
				breakpoint: {
					type: 'string',
				},

				breakpointCustomValue: {
					type: 'string',
				},
			},
		},
	};
}

function getInlineCSS( attributes, clientId ) {
	const { breakpoint, breakpointCustomValue } = getBreakpointFromAttributes( attributes );

	if ( breakpoint === BREAKPOINT_OPTION_OFF ) {
		return null;
	}

	// prevent the columns from being stacked when custom breakpoint is empty
	// such stacking is caused by core/columns css rules
	const switchWidth = getSwitchWidth( breakpoint, breakpointCustomValue ) ?? '0px';

	const columnsSelector = `.wp-block-columns.${ BLOCK_PREFIX + clientId }`;

	const columnsStackedSelector = `${ columnsSelector }:not(.is-not-stacked-on-mobile)`;

	return [
		`${ columnsSelector } {
			flex-wrap: wrap !important;
		}`,

		`@media screen and (width <= ${ switchWidth }) {
			${ columnsStackedSelector } > .wp-block-column.wp-block-column.wp-block-column {
				flex-basis: 100% !important;
			}
		}`,

		`@media screen and (width > ${ switchWidth }) {
			${ columnsSelector } {
				flex-wrap: nowrap !important;
			}

			${ columnsStackedSelector } > .wp-block-column {
				flex-basis: 0 !important;
				flex-grow: 1;
			}

			${ columnsStackedSelector } > .wp-block-column[style*=flex-basis] {
				flex-grow: 0;
			}
		}`,

		`${ columnsSelector }.is-not-stacked-on-mobile {
			flex-wrap: nowrap !important;
		}`,
	];
}

// eslint-disable-next-line jsdoc/require-param
/**
 * Copied from `core/columns` without any chages.
 * @see core/columns
 */

const extendBlockEdit = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( ! isApplyChanges( props ) ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes, clientId } = props;

		const { count, canInsertColumnBlock, minCount } = useSelect(
			( select ) => {
				const { canInsertBlockType, canRemoveBlock, getBlocks, getBlockCount } =
					select( blockEditorStore );
				const innerBlocks = getBlocks( clientId );

				// Get the indexes of columns for which removal is prevented.
				// The highest index will be used to determine the minimum column count.
				const preventRemovalBlockIndexes = innerBlocks.reduce( ( acc, block, index ) => {
					if ( ! canRemoveBlock( block.clientId ) ) {
						acc.push( index );
					}
					return acc;
				}, [] );

				return {
					count: getBlockCount( clientId ),
					canInsertColumnBlock: canInsertBlockType( 'core/column', clientId ),
					minCount: Math.max( ...preventRemovalBlockIndexes ) + 1,
				};
			},
			[ clientId ]
		);

		const { breakpoint, breakpointCustomValue } = getBreakpointFromAttributes( attributes );

		// if breakpoint was deactivated by user, reset to custom one with the same breakpoint value
		useHandleDeletedUserBreakpoint( breakpoint, ( newValue ) => {
			setAttributes( {
				dtCrStackOn: {
					...attributes.dtCrStackOn,
					breakpoint: BREAKPOINT_OPTION_CUSTOM,
					breakpointCustomValue: newValue,
				},
			} );
		} );
		const [ initialOpen ] = useState();

		const inlineCSS = useMemo(
			() => getInlineCSS( attributes, clientId ),
			[ attributes, clientId ]
		);

		const columnsRef = useAddEditorStyle( inlineCSS, FEATURE_NAME + '__' + clientId );

		// imports for updateBlocks
		const { getBlocks } = useSelect( blockEditorStore );
		const { replaceInnerBlocks } = useDispatch( blockEditorStore );

		function updateColumns( previousColumns, newColumns ) {
			let innerBlocks = getBlocks( clientId );
			const hasExplicitWidths = hasExplicitPercentColumnWidths( innerBlocks );

			// Redistribute available width for existing inner blocks.
			const isAddingColumn = newColumns > previousColumns;

			if ( isAddingColumn && hasExplicitWidths ) {
				// If adding a new column, assign width to the new column equal to
				// as if it were `1 / columns` of the total available space.
				const newColumnWidth = toWidthPrecision( 100 / newColumns );

				// Redistribute in consideration of pending block insertion as
				// constraining the available working width.
				const widths = getRedistributedColumnWidths( innerBlocks, 100 - newColumnWidth );

				innerBlocks = [
					...getMappedColumnWidths( innerBlocks, widths ),
					...Array.from( {
						length: newColumns - previousColumns,
					} ).map( () => {
						return createBlock( 'core/column', {
							width: `${ newColumnWidth }%`,
						} );
					} ),
				];
			} else if ( isAddingColumn ) {
				innerBlocks = [
					...innerBlocks,
					...Array.from( {
						length: newColumns - previousColumns,
					} ).map( () => {
						return createBlock( 'core/column' );
					} ),
				];
			} else if ( newColumns < previousColumns ) {
				// The removed column will be the last of the inner blocks.
				innerBlocks = innerBlocks.slice( 0, -( previousColumns - newColumns ) );
				if ( hasExplicitWidths ) {
					// Redistribute as if block is already removed.
					const widths = getRedistributedColumnWidths( innerBlocks, 100 );

					innerBlocks = getMappedColumnWidths( innerBlocks, widths );
				}
			}

			replaceInnerBlocks( clientId, innerBlocks );
		}

		return (
			<>
				<DtCrRefAnchor ref={ columnsRef } />
				<BlockEdit { ...props } />

				<InspectorControls>
					<PanelBody
						title={ __( 'Settings' ) }
						initialOpen={ initialOpen ?? true }
						className="dt-cr stack-on-with-responsiveness"
					>
						{ canInsertColumnBlock && (
							<>
								<RangeControl
									__nextHasNoMarginBottom
									__next40pxDefaultSize
									label={ __( 'Columns' ) }
									value={ count }
									onChange={ ( value ) =>
										updateColumns( count, Math.max( minCount, value ) )
									}
									min={ Math.max( 1, minCount ) }
									max={ Math.max( 6, count ) }
								/>
								{ count > 6 && (
									<Notice status="warning" isDismissible={ false }>
										{ __(
											'This column count exceeds the recommended amount and may cause visual breakage.'
										) }
									</Notice>
								) }
							</>
						) }

						<ResponsiveBreakpointControl
							label={ __( 'Stack on', 'dt-cr' ) }
							value={ breakpoint }
							onChange={ ( value ) => {
								setAttributes( {
									// update core stacked implementation to keep logic solid
									// when we enable/disable this feature or full plugin
									isStackedOnMobile: value !== BREAKPOINT_OPTION_OFF,
									dtCrStackOn: {
										breakpoint: value,
										breakpointCustomValue: undefined,
									},
								} );
							} }
						/>
						{ breakpoint === BREAKPOINT_OPTION_CUSTOM && (
							<ResponsiveBreakpointCustomValue
								onChange={ ( value ) => {
									setAttributes( {
										dtCrStackOn: {
											breakpoint,
											breakpointCustomValue: value,
										},
									} );
								} }
								value={ breakpointCustomValue }
							/>
						) }
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'extendBlockEdit' );

const addColumnsExtraPropsEditor = createHigherOrderComponent(
	( BlockListBlock ) => ( props ) => {
		const { clientId, className } = props;

		if ( ! isApplyChanges( props ) ) {
			return <BlockListBlock { ...props } />;
		}

		const cssClasses = addCssClasses( className, BLOCK_PREFIX + `${ clientId }` );

		return <BlockListBlock { ...props } className={ cssClasses } />;
	},
	'addColumnsExtraPropsEditor'
);

const addColumnExtraPropsEditor = createHigherOrderComponent(
	( BlockListBlock ) => ( props ) => {
		if ( props.name !== COLUMN_BLOCK_NAME || ! props?.attributes.width ) {
			return <BlockListBlock { ...props } />;
		}

		const columnCssClass = BLOCK_PREFIX + props.clientId;
		const inlineCSS = `
		.wp-block-columns:not(.is-not-stacked-on-mobile) > .wp-block-column.${ columnCssClass }[style*=flex-basis] {
			flex-basis: ${ props.attributes.width } !important;
		}
		`;

		const columnRef = useAddEditorStyle( inlineCSS, FEATURE_NAME + '__' + props.clientId );

		return (
			<>
				<DtCrRefAnchor ref={ columnRef } />
				<BlockListBlock { ...props } />
			</>
		);
	},
	'addColumnExtraPropsEditor'
);

addFilter(
	'blocks.registerBlockType',
	'dt-cr/columns/stack-on-responsive/modify-block',
	modifyBlockData
);

addFilter( 'editor.BlockEdit', 'dt-cr/columns/stack-on-responsive/edit-block', extendBlockEdit );

addFilter(
	'editor.BlockListBlock',
	'dt-cr/columns/stack-on-responsive/columns-props-editor',
	addColumnsExtraPropsEditor
);

// we have to add separate one for core/column coz if we handle it in addColumnsExtraPropsEditor
// it doesn't handle changes in core/column block width until saved
addFilter(
	'editor.BlockListBlock',
	'dt-cr/columns/stack-on-responsive/column-props-editor',
	addColumnExtraPropsEditor
);
