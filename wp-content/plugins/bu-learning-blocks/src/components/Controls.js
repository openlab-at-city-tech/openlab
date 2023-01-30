const { Component } = wp.element;
const { AlignmentToolbar, BlockControls } = wp.editor;

/**
 * Create a Block Controls wrapper Component
 */
export default class Controls extends Component {
	render() {
		const {
			attributes: { textAlignment },
			setAttributes,
		} = this.props;

		// Change Handlers
		const onChangeTextAlignment = newAlignment =>
			setAttributes( { textAlignment: newAlignment } );

		return (
			<BlockControls>
				<AlignmentToolbar
					value={ textAlignment }
					onChange={ onChangeTextAlignment }
				/>
			</BlockControls>
		);
	}
}
