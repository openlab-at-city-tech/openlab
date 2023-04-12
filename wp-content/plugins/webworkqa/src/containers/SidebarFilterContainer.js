import { connect } from 'react-redux'
import SidebarFilter from '../components/SidebarFilter'
import { processFilterChange } from '../actions/app'
import { fetchQuestionIndexList } from '../actions/questions'

import { push } from 'react-router-redux'

const mapStateToProps = ( state, ownProps ) => {
	const { currentFilters, filterOptions } = state
	const { slug } = ownProps

	let options = filterOptions[ slug ]
	let value = currentFilters[ slug ]

	return {
		options,
		value
	}
}

const mapDispatchToProps = ( dispatch, ownProps ) => {
	return {
		onFilterChange: function( selected ) {
			const { slug } = ownProps

			let value = ''
			if ( selected ) {
				value = selected.value
			}

			dispatch( processFilterChange( slug, value ) )

			// For the theme to know when to collapse the menu.
			const event = new Event( 'webworkFilterChange' );
			document.body.dispatchEvent( event );
		}
	}
}

const SidebarFilterContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(SidebarFilter)

export default SidebarFilterContainer
