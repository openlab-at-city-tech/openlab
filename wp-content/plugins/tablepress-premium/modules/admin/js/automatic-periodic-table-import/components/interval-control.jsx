/**
 * JavaScript code for the IntervalControl component.
 *
 * @package TablePress
 * @subpackage Automatic Periodic Table Import Screen
 * @author Tobias Bäthge
 * @since 2.3.0
 */

/**
 * WordPress dependencies.
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import {
	Icon,
	Tooltip,
} from '@wordpress/components';
import { info } from '@wordpress/icons';

const availableIntervals = {
	60: __( 'Once per minute', 'tablepress' ),
	900: __( 'Once per 15 minutes', 'tablepress' ),
	3600: __( 'Once per hour', 'tablepress' ),
	43200: __( 'Twice per day', 'tablepress' ),
	86400: __( 'Once per day', 'tablepress' ),
	604800: __( 'Once per week', 'tablepress' ),
};

const availableIntervalsOptions = Object.entries( availableIntervals ).map( ( [ interval, intervalLabel ] ) =>
	<option key={ interval } value={ interval }>{ intervalLabel }</option>
);
availableIntervalsOptions.push( <option key="custom" value="custom">{ __( 'Custom', 'tablepress' ) }</option> );

/**
 * Returns the IntervalControl component's JSX markup.
 *
 * @param {Object}        props          Function parameters.
 * @param {boolean}       props.disabled Whether the control is disabled. Default false.
 * @param {number|string} props.value    Current interval value.
 * @param {Function}      props.onChange Callback for interval value changes.
 * @return {Object} IntervalControl component.
 */
const IntervalControl = ( { disabled = false, value, onChange } ) => {
	const [ customIntervalSelected, setCustomIntervalSelected ] = useState( false );

	const customIntervalConfigured = ! availableIntervals.hasOwnProperty( value );

	return (
		<>
			<select
				disabled={ disabled }
				value={ ( customIntervalSelected || customIntervalConfigured ) ? 'custom' : value }
				onChange={ ( event ) => {
					if ( 'custom' === event.target.value ) {
						setCustomIntervalSelected( true );
					} else {
						setCustomIntervalSelected( false );
						onChange( event.target.value );
					}
				} }
			>
				{ availableIntervalsOptions }
			</select>
			{ ( customIntervalSelected || customIntervalConfigured ) &&
				<>
					{ ' ' }
					<input
						type="text"
						className="code"
						disabled={ disabled }
						value={ value }
						onChange={ ( event ) => {
							onChange( /^\d+$/.test( event.target.value ) ? parseInt( event.target.value, 10 ) : event.target.value );
						} }
					/>
					{ ' ' }
					{ ! disabled ? (
						<Tooltip text={ __( 'Interval in seconds or Cron-like schedule', 'tablepress' ) }>
							{ /* @todo Remove <span> when the required WordPress version is 6.5. */ }
							<span>
								<Icon
									icon={ info }
									style={ {
										verticalAlign: 'middle',
									} }
								/>
							</span>
						</Tooltip>
					) : (
						<Icon
							icon={ info }
							style={ {
								fill: 'rgba(44, 51, 56, 0.5)',
								verticalAlign: 'middle',
							} }
						/>
					) }
				</>
			}
		</>
	);
};

export default IntervalControl;
