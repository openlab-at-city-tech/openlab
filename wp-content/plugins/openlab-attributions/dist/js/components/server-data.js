function ServerData( { item, index } ) {
	const inputs = Object.entries( item );

	return (
		<>
			{ inputs.map( ( [ name, value ] ) => (
				<input
					type="hidden"
					name={ `attributions[${ index }][${ name }]` }
					value={ value }
					key={ name }
				/>
			) ) }
		</>
	);
}
export default ServerData;
