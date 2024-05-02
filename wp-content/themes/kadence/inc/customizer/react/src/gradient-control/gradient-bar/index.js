/**
 * External dependencies
 */

import { some } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { useRef, useReducer } from '@wordpress/element';

/**
 * Internal dependencies
 */
import ControlPoints from './control-points';
import { getHorizontalRelativeGradientPosition } from './utils';
import { MINIMUM_DISTANCE_BETWEEN_INSERTER_AND_POINT } from './constants';

function customGradientBarReducer( state, action ) {
	switch ( action.type ) {
		case 'MOVE_INSERTER':
			if ( state.id === 'IDLE' || state.id === 'MOVING_INSERTER' ) {
				return {
					id: 'MOVING_INSERTER',
					insertPosition: action.insertPosition,
				};
			}
			break;
		case 'STOP_INSERTER_MOVE':
			if ( state.id === 'MOVING_INSERTER' ) {
				return {
					id: 'IDLE',
				};
			}
			break;
		case 'OPEN_INSERTER':
			if ( state.id === 'MOVING_INSERTER' ) {
				return {
					id: 'INSERTING_CONTROL_POINT',
					insertPosition: state.insertPosition,
				};
			}
			break;
		case 'CLOSE_INSERTER':
			if ( state.id === 'INSERTING_CONTROL_POINT' ) {
				return {
					id: 'IDLE',
				};
			}
			break;
		case 'START_CONTROL_CHANGE':
			if ( state.id === 'IDLE' ) {
				return {
					id: 'MOVING_CONTROL_POINT',
				};
			}
			break;
		case 'STOP_CONTROL_CHANGE':
			if ( state.id === 'MOVING_CONTROL_POINT' ) {
				return {
					id: 'IDLE',
				};
			}
			break;
	}
	return state;
}
const customGradientBarReducerInitialState = { id: 'IDLE' };

export default function CustomGradientBar( {
	background,
	hasGradient,
	value: controlPoints,
	onChange,
	disableInserter = false,
	isRenderedInSidebar,
	activePalette,
} ) {
	const gradientMarkersContainerDomRef = useRef();

	const [ gradientBarState, gradientBarStateDispatch ] = useReducer(
		customGradientBarReducer,
		customGradientBarReducerInitialState
	);
	const popoverRef = useRef();
	const onMouseEnterAndMove = ( event ) => {
		const insertPosition = getHorizontalRelativeGradientPosition(
			event.clientX,
			gradientMarkersContainerDomRef.current
		);

		// If the insert point is close to an existing control point don't show it.
		if (
			some( controlPoints, ( { position } ) => {
				return (
					Math.abs( insertPosition - position ) <
					MINIMUM_DISTANCE_BETWEEN_INSERTER_AND_POINT
				);
			} )
		) {
			if ( gradientBarState.id === 'MOVING_INSERTER' ) {
				gradientBarStateDispatch( { type: 'STOP_INSERTER_MOVE' } );
			}
			return;
		}

		gradientBarStateDispatch( { type: 'MOVE_INSERTER', insertPosition } );
	};

	const onMouseLeave = () => {
		gradientBarStateDispatch( { type: 'STOP_INSERTER_MOVE' } );
	};

	const isMovingInserter = gradientBarState.id === 'MOVING_INSERTER';
	const isInsertingControlPoint =
		gradientBarState.id === 'INSERTING_CONTROL_POINT';

	return (
		<div
			ref={ popoverRef }
			className={ classnames(
				'components-custom-gradient-picker__gradient-bar',
				{ 'has-gradient': hasGradient }
			) }
			onMouseEnter={ onMouseEnterAndMove }
			onMouseMove={ onMouseEnterAndMove }
			style={ { background } }
			onMouseLeave={ onMouseLeave }
		>
			<div
				ref={ gradientMarkersContainerDomRef }
				className="components-custom-gradient-picker__markers-container"
			>
				{ ! disableInserter &&
					( isMovingInserter || isInsertingControlPoint ) && (
						<ControlPoints.InsertPoint
							isRenderedInSidebar={ isRenderedInSidebar }
							insertPosition={ gradientBarState.insertPosition }
							value={ controlPoints }
							onChange={ onChange }
							activePalette={ activePalette }
							popoverRef={ popoverRef }
							onOpenInserter={ () => {
								gradientBarStateDispatch( {
									type: 'OPEN_INSERTER',
								} );
							} }
							onCloseInserter={ () => {
								gradientBarStateDispatch( {
									type: 'CLOSE_INSERTER',
								} );
							} }
						/>
					) }
				<ControlPoints
					isRenderedInSidebar={ isRenderedInSidebar }
					activePalette={ activePalette }
					disableRemove={ disableInserter }
					gradientPickerDomRef={ gradientMarkersContainerDomRef }
					ignoreMarkerPosition={
						isInsertingControlPoint
							? gradientBarState.insertPosition
							: undefined
					}
					value={ controlPoints }
					onChange={ onChange }
					popoverRef={ popoverRef }
					onStartControlPointChange={ () => {
						gradientBarStateDispatch( {
							type: 'START_CONTROL_CHANGE',
						} );
					} }
					onStopControlPointChange={ () => {
						gradientBarStateDispatch( {
							type: 'STOP_CONTROL_CHANGE',
						} );
					} }
				/>
			</div>
		</div>
	);
}
