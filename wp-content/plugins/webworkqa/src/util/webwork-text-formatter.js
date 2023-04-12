import React, { Component } from 'react'

const convertLinebreaks = ( text ) => {
	let chunkKey = 0
	return text.split( "\n" ).map( function(item) {
		chunkKey++
		return (
			<span key={'chunk-' + chunkKey}>
				{item}
				<br/>
			</span>
		)
	} )
}

const convertLinebreaksAsString = ( text ) => {
	return text.replace( /(?:\r\n|\r|\n)/g, '<br />' )
}

const collapseLinebreaks = ( text ) => {
	return text.replace( /((<br \/>){3,})/, '<br /><br />' )
}

const attachmentShortcodeRegExp = () => {
	return /\[attachment id="([^"]+)"\]/gm
}

const attachmentMarkup = (attData) => {
	return '<a href="' + attData.urlFull + '"><img class="webwork-embedded-attachment" alt="' + attData.title + '" src="' + attData.urlThumb + '" /></a>'
}

export {
	convertLinebreaks,
	convertLinebreaksAsString,
	collapseLinebreaks,
	attachmentShortcodeRegExp,
	attachmentMarkup
}
