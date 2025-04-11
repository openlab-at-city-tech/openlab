import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { collapseLinebreaks, attachmentShortcodeRegExp, attachmentMarkup } from '../util/webwork-text-formatter.js'
import { __, sprintf } from '@wordpress/i18n';

export default class FormattedProblem extends Component {
	componentDidMount() {
		document.webwork_scaffold_init( ReactDOM.findDOMNode( this.refs.problem ) )
	}

	shouldComponentUpdate( nextProps, nextState ) {
		return nextProps.content !== this.props.content || nextProps.isVisible !== this.props.isVisible
	}

	render() {
		const { attachments, isVisible, itemId, content, contentSwappedUrl } = this.props

		if ( ! content ) {
			return ( <span></span> )
		}

		const texRegExp = /\{\{\{LATEX_DELIM_((?:DISPLAY)|(?:INLINE))_((?:OPEN)|(?:CLOSE))\}\}\}/gm

		let markup = content
		let toQueue = []

		const replacements = [
			{ 'search': '&lt;', 'replace': '<' },
			{ 'search': '&gt;', 'replace': '>' },
			{ 'search': '&amp;', 'replace': '&' },
		]

		for ( let i in replacements ) {
			markup = markup.replace(
				new RegExp(replacements[i].search.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1"), 'g'),
				replacements[i].replace
			)
		}

		// Remove empty TeX chunks.
		const texChunkRegExp = /\{\{\{LATEX_DELIM_((?:DISPLAY)|(?:INLINE))_OPEN\}\}\}(.*?)\{\{\{LATEX_DELIM_((?:DISPLAY)|(?:INLINE))_CLOSE\}\}\}/gm
		markup = markup.replace( texChunkRegExp, function( chunk, mode, contents ) {
			if ( 0 === contents.length ) {
				return ''
			}

			return chunk
		} )

		markup = markup.replace( texRegExp, function( delim, mode, openOrClose ) {
			if ( 'CLOSE' == openOrClose ) {
				return '</script>'
			}

			let typeAttr = 'math/tex'
			if ( 'DISPLAY' == mode ) {
				typeAttr += ';mode=display'
			}

			if ( ! document.hasOwnProperty( 'latexIncrementor' ) ) {
				document.latexIncrementor = 1;
			}

			let cssId = 'latex-' + document.latexIncrementor
			toQueue.push( cssId )

			document.latexIncrementor = document.latexIncrementor + 1

			return '<script type="' + typeAttr + '" id="' + cssId + '">'
		} )

		markup = markup.replace( '{{{GEOGEBRA_PROBLEM}}}', '<div class="geogebra-placeholder">' + __( 'This problem contains interactive elements that cannot be displayed here. Please visit your WeBWorK course to see the full problem content.', 'webworkqa' ) + '</div>' )

		const divRegExp = /<div[^>]*>([\s\S]*?)<\/div>/gm
		markup = markup.replace( divRegExp, function( div, innerText ) {
			if ( 0 === innerText.length ) {
				return ''
			}

			return div
		} )

		// Collapse line breaks between elements inside of tables.
		const tableRegExp = /<table[^>]*>([\s\S]*?)<\/table>/gm
		const lineBreakRegExp = />[(\r?\n)\s]+</gm
		markup = markup.replace( tableRegExp, function( table, tableContent ) {
			return table.replace( lineBreakRegExp, '' )
		} )

		// Line break substitution must skip <script> tags.
		markup = markup.replace( /(?!<script[^>]*?>)(?:\r\n|\r|\n)(?![^<]*?<\/script>)/g, '<br />' )

		// But don't allow many breaks in a row :(
		markup = collapseLinebreaks( markup )

		markup = markup.replace( attachmentShortcodeRegExp(), function( a, attId ) {
			if ( ! attachments.hasOwnProperty( attId ) ) {
				return a
			}

			return attachmentMarkup( attachments[ attId ] )
		} )

		if ( contentSwappedUrl ) {
			markup += '<div class="question-swapped">' + sprintf( __( 'The problem text stored with this question contains references to deleted images. In order to provide a more accurate visual record, we\'ve provided the problem text from <a href="%1$s">another question in this thread</a>', 'webworkqa' ), contentSwappedUrl ) + '</div>';
		}

		if ( window.hasOwnProperty( 'MathJax' ) && window.MathJax.hasOwnProperty( 'Hub' ) ) {
			for ( var i = 0; i <= toQueue.length; i++ ) {
				window.MathJax.Hub.Queue(["Update", window.MathJax.Hub, toQueue[i] ]);
			}
		}

		return (
			<div
			  className="formatted-problem"
			  id={itemId}
			  dangerouslySetInnerHTML={{__html: markup}}
			  ref="problem"
			/>
		)
	}
}

FormattedProblem.defaultProps = {
	isVisible: true
}
