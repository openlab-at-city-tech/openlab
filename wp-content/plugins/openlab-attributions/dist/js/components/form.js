/**
 * WordPress dependencies
 */
import { useReducer } from '@wordpress/element';

function reducer( state, newState ) {
	return { ...state, ...newState };
}

export default function Form( { item, onSubmit, onClose } ) {
	const [ state, setState ] = useReducer( reducer, { ...item } );
}
