import { useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';
import { buildCssClasses, buildCssVariables } from './utils';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save( {
		className: buildCssClasses( attributes ),
		style: buildCssVariables( attributes ),
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <div { ...innerBlocksProps } />;
}
