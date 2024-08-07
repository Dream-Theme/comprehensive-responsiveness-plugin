import classnames from 'classnames';

import { useInnerBlocksProps, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { verticalAlignment } = attributes;

	const blockProps = useBlockProps.save( {
		className: classnames( {
			[ `is-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
		} ),
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <div { ...innerBlocksProps } />;
}
