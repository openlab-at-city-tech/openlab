/**
 * External dependencies
 */

import { some } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const {
	useRef,
	useReducer,
	useState
} = wp.element;
import { plusCircle } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import {
	Button,
	Dropdown,
	ColorPicker,
} from '@wordpress/components';
import KadenceGradientColorPicker from './color-picker';

import ControlPoints from './control-points';
import {
	INSERT_POINT_WIDTH,
	COLOR_POPOVER_PROPS,
	MINIMUM_DISTANCE_BETWEEN_INSERTER_AND_POINT,
} from './constants';
import { serializeGradient } from './serializer';
import {
	getGradientWithColorAtPositionChanged,
	getGradientWithColorStopAdded,
	getHorizontalRelativeGradientPosition,
	getMarkerPoints,
	getGradientParsed,
	getLinearGradientRepresentationOfARadial,
} from './utils';

function InsertPoint( {
	onChange,
	gradientAST,
	onOpenInserter,
	onCloseInserter,
	insertPosition,
	activePalette,
} ) {
	const [ alreadyInsertedPoint, setAlreadyInsertedPoint ] = useState( false );
	return (
		<Dropdown
			className="components-custom-gradient-picker__inserter"
			onClose={ () => {
				onCloseInserter();
			} }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Button
					aria-expanded={ isOpen }
					onClick={ () => {
						if ( isOpen ) {
							onCloseInserter();
						} else {
							setAlreadyInsertedPoint( false );
							onOpenInserter();
						}
						onToggle();
					} }
					className="components-custom-gradient-picker__insert-point"
					icon={ plusCircle }
					style={ {
						left:
							insertPosition !== null
								? `${ insertPosition }%`
								: undefined,
					} }
				/>
			) }
			renderContent={ () => (
				<KadenceGradientColorPicker
					color={''}
					onChange={ ( value ) => onChange( value ) }
					activePalette={ activePalette }
					gradientAST={ gradientAST }
					insertPosition={ insertPosition }
				/>
			) }
			popoverProps={ COLOR_POPOVER_PROPS }
		/>
	);
}

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

export default function CustomGradientBar( { value, onChange, activePalette } ) {
	const { gradientAST, gradientValue, hasGradient } = getGradientParsed(
		value
	);

	const onGradientStructureChange = ( newGradientStructure ) => {
		onChange( serializeGradient( newGradientStructure ) );
	};

	const gradientPickerDomRef = useRef();
	const markerPoints = getMarkerPoints( gradientAST );

	const [ gradientBarState, gradientBarStateDispatch ] = useReducer(
		customGradientBarReducer,
		customGradientBarReducerInitialState
	);
	const onMouseEnterAndMove = ( event ) => {
		const insertPosition = getHorizontalRelativeGradientPosition(
			event.clientX,
			gradientPickerDomRef.current,
			INSERT_POINT_WIDTH
		);

		// If the insert point is close to an existing control point don't show it.
		if (
			some( markerPoints, ( { positionValue } ) => {
				return (
					Math.abs( insertPosition - positionValue ) <
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
			ref={ gradientPickerDomRef }
			className={ classnames(
				'components-custom-gradient-picker__gradient-bar',
				{ 'has-gradient': hasGradient }
			) }
			onMouseEnter={ onMouseEnterAndMove }
			onMouseMove={ onMouseEnterAndMove }
			// On radial gradients the bar should display a linear gradient.
			// On radial gradients the bar represents a slice of the gradient from the center until the outside.
			style={ {
				background:
					gradientAST.type === 'radial-gradient'
						? getLinearGradientRepresentationOfARadial(
								gradientAST
						  )
						: gradientValue,
			} }
			onMouseLeave={ onMouseLeave }
		>
			<div className="components-custom-gradient-picker__markers-container">
				{ ( isMovingInserter || isInsertingControlPoint ) && (
					<InsertPoint
						insertPosition={ gradientBarState.insertPosition }
						onChange={ onGradientStructureChange }
						gradientAST={ gradientAST }
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
						activePalette={ activePalette }
					/>
				) }
				<ControlPoints
					gradientPickerDomRef={ gradientPickerDomRef }
					ignoreMarkerPosition={
						isInsertingControlPoint
							? gradientBarState.insertPosition
							: undefined
					}
					markerPoints={ markerPoints }
					onChange={ onGradientStructureChange }
					gradientAST={ gradientAST }
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
					activePalette={ activePalette }
				/>
			</div>
		</div>
	);
}
