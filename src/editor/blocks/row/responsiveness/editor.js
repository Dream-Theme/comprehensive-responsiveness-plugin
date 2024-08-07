import { BLOCK_PREFIX } from '@dt-cr/constants';
import { addCssClasses } from '@dt-cr/css-classes-utils';
import { DtCrRefAnchor } from '@dt-cr/editor-css-store/components/DtCrRefAnchor';
import { getSwitchWidth } from '@dt-cr/responsive';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useEffect, useMemo, useState } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { useAddEditorStyle } from 'dt-cr/editor-css-store';
import { FEATURE_NAME } from './constants';
import './editor.scss';
import StretchWidthSettings from './stretch-width-settings';

const BLOCK_NAME = 'core/group';

function isApplyChanges( props, attributes ) {
	if ( props.name !== BLOCK_NAME ) {
		return false;
	}
	let attr = attributes;
	if ( ! attr ) {
		attr = props.attributes;
	}
	const { layout } = attr;
	//handle only rows variations
	return layout && !! layout?.type && layout.type === 'flex';
}

function modifyBlockData( settings, name ) {
	if ( name !== BLOCK_NAME ) {
		return settings;
	}

	return {
		...settings,
		attributes: {
			...settings.attributes,
			dtCrResponsive: {
				breakpoint: {
					type: 'string',
				},
				breakpointCustomValue: {
					type: 'string',
				},
				justification: {
					type: 'string',
				},
				orientation: {
					type: 'string',
				},
			},
		},
	};
}

// Used with the default, horizontal(row) flex orientation.
const justifyMap = {
	left: 'flex-start',
	right: 'flex-end',
	center: 'center',
	stretch: 'stretch',
	'space-between': 'space-between',
};

// Used with the vertical (column) flex orientation.
const alignItemsMap = {
	left: 'flex-start',
	right: 'flex-end',
	center: 'center',
	stretch: 'stretch',
};

const reverseMap = {
	left: 'right',
	right: 'left',
	center: 'center',
	stretch: 'stretch',
	'space-between': 'space-between',
};

function getInlineCSS( attributes, clientId ) {
	const { dtCrResponsive } = attributes;
	if ( ! dtCrResponsive ) return null;

	const { breakpoint, breakpointCustomValue, justification, orientation } = dtCrResponsive;

	const switchWidth = getSwitchWidth( breakpoint, breakpointCustomValue );
	if ( ! switchWidth ) return null;

	let justifyCSS = '';
	if ( justification ) {
		if ( orientation && ( orientation === 'column' || orientation === 'column-reverse' ) ) {
			justifyCSS = `align-items:${ alignItemsMap[ justification ] } !important;`;
		} else {
			let justificationFixed = justification;
			if ( orientation === 'row-reverse' || orientation === 'column-reverse' ) {
				justificationFixed = reverseMap[ justification ];
			}
			justifyCSS = `justify-content:${ justifyMap[ justificationFixed ] } !important;`;
		}
	}

	let orientationCSS = '';
	if ( orientation ) {
		orientationCSS = `flex-direction:${ orientation } !important;`;
	}

	if ( ! justifyCSS && ! orientationCSS ) return null;

	const block = `.${ BLOCK_PREFIX + clientId }{
		${ justifyCSS }${ orientationCSS }
	}`;

	return `@media screen and (width <= ${ switchWidth }) {
	 	${ block }
	}`;
}

const extendBlockEdit = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( ! isApplyChanges( props ) ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, clientId } = props;
		const { dtCrResponsive } = attributes;
		const { breakpoint } = dtCrResponsive || {};

		const [ initialOpen, setInitialOpen ] = useState();

		useEffect( () => {
			// If any selected block has a position set, open the panel by default.
			// The first block's value will still be used within the control though.
			//if (initialOpen === undefined) {
			let isOpen = false;
			if ( attributes?.dtCrResponsive ) {
				isOpen = !! breakpoint;
			}
			setInitialOpen( isOpen );
			//}

			// eslint-disable-next-line react-hooks/exhaustive-deps
		}, [ initialOpen, breakpoint, setInitialOpen ] );

		const inlineCSS = useMemo(
			() => getInlineCSS( attributes, clientId ),
			[ attributes, clientId ]
		);

		const rowRef = useAddEditorStyle( inlineCSS, FEATURE_NAME + '__' + clientId );

		return (
			<>
				<DtCrRefAnchor ref={ rowRef } />

				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Responsiveness', 'dt-cr' ) }
						initialOpen={ initialOpen ?? false }
						className="responsive-row"
					>
						<StretchWidthSettings props={ props } />
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'extendBlockEdit' );

const addExtraPropsEditor = createHigherOrderComponent(
	( BlockListBlock ) => ( props ) => {
		if ( ! isApplyChanges( props ) ) {
			return <BlockListBlock { ...props } />;
		}

		return (
			<BlockListBlock
				{ ...props }
				className={ addCssClasses(
					props.className,
					`${ BLOCK_PREFIX }${ props.clientId }`
				) }
				wrapperProps={ props.wrapperProps }
			/>
		);
	},
	'addExtraPropsEditor'
);

addFilter( 'blocks.registerBlockType', 'dt-cr/row/responsiveness/modify-block', modifyBlockData );
addFilter( 'editor.BlockEdit', 'dt-cr/row/responsiveness/edit-block', extendBlockEdit );
addFilter( 'editor.BlockListBlock', 'dt-cr/row/responsiveness/props-editor', addExtraPropsEditor );
