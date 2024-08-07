import { __ } from '@wordpress/i18n';
import { Flex, FlexItem } from '@wordpress/components';
import JustificationControl from './justification-control';
import { useEffect } from '@wordpress/element';
import OrientationControl from './orientation-control';
import ResponsiveBreakpointCustomValue from '@dt-cr/components/responsive-breakpoint-custom-value';
import ResponsiveBreakpointControl, {
	BREAKPOINT_OPTION_CUSTOM,
} from '@dt-cr/components/responsive-breakpoint-control';
import { getUserDefinedBreakpointValue } from '@dt-cr/responsive';
import { useHandleDeletedUserBreakpoint } from '@dt-cr/hooks/useHandleDeletedUserBreakpoint';

export default function StretchWidthSettings( { props } ) {
	const { attributes, setAttributes } = props;
	const { dtCrResponsive } = attributes;
	const { breakpoint, breakpointCustomValue, justification, orientation } = dtCrResponsive || {};

	// if breakpoint was deactivated by user, reset to custom one with the same breakpoint value
	useHandleDeletedUserBreakpoint( breakpoint, ( newValue ) => {
		setAttributes( {
			dtCrResponsive: {
				...dtCrResponsive,
				breakpoint: BREAKPOINT_OPTION_CUSTOM,
				breakpointCustomValue: newValue,
			},
		} );
	} );

	useEffect( () => {
		if ( ! breakpoint && dtCrResponsive ) {
			setAttributes( {
				dtCrResponsive: undefined,
			} );
		}
	}, [ breakpoint, dtCrResponsive, setAttributes ] );

	function handleAttrChange( values ) {
		setAttributes( {
			dtCrResponsive: { ...dtCrResponsive, ...values },
		} );
	}

	const helpText = __(
		'Change justification and orientation at this breakpoint and below.',
		'dt-cr'
	);

	return (
		<>
			<ResponsiveBreakpointControl
				label={ __( 'Breakpoint', 'dt-cr' ) }
				value={ breakpoint }
				onChange={ ( value ) => {
					handleAttrChange( {
						breakpoint: value,
						breakpointCustomValue:
							value === BREAKPOINT_OPTION_CUSTOM
								? getUserDefinedBreakpointValue( value )
								: undefined,
					} );
				} }
				help={ breakpoint !== BREAKPOINT_OPTION_CUSTOM && helpText }
			/>
			{ breakpoint === BREAKPOINT_OPTION_CUSTOM && (
				<ResponsiveBreakpointCustomValue
					onChange={ ( value ) => handleAttrChange( { breakpointCustomValue: value } ) }
					value={ breakpointCustomValue }
					help={ helpText }
				/>
			) }

			{ breakpoint && (
				<>
					<Flex>
						<FlexItem>
							<JustificationControl
								justification={ justification }
								orientation={ orientation }
								onChange={ handleAttrChange }
							/>
						</FlexItem>
						<FlexItem>
							<OrientationControl
								justification={ justification }
								orientation={ orientation }
								onChange={ handleAttrChange }
							/>
						</FlexItem>
					</Flex>
				</>
			) }
		</>
	);
}
