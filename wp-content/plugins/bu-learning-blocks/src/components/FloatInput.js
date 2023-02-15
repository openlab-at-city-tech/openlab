import React from 'react';

class FloatInput extends React.Component {
	constructor( props ) {
		super( props );
		this.state = { error: '' };
		this.onChange = this.onChange.bind( this );
	}

	onChange( event ) {
		const { value } = event.target;
		const error = isNaN( parseFloat( value ) ) ? 'Invalid number' : '';
		this.props.onChange( value );
		this.setState( {
			error,
		} );
	}

	render() {
		return (
			<React.Fragment>
				<input
					type="number"
					step="any"
					value={ this.props.value }
					onChange={ this.onChange.bind( this ) }
				/>
				{ this.state.error && <div>{ this.state.error }</div> }
			</React.Fragment>
		);
	}
}

export default FloatInput;
