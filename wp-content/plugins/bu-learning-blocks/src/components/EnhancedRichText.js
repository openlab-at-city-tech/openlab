const { RichText } = wp.editor;

export default props => (
	<RichText
		keepPlaceholderOnFocus={ true }
		style={ {
			textAlign: props.textAlignment,
			color: props.textColorControl,
			backgroundColor: props.backgroundColorControl,
			fontSize: props.fontSize ? props.fontSize + 'px' : undefined,
		} }
		{ ...props }
	/>
);
