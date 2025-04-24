import { connect } from 'react-redux'
import Sidebar from '../components/Sidebar'

const mapStateToProps = ( state ) => {
	return {}
}

const mapDispatchToProps = ( dispatch ) => {
	return {}
}

const SidebarContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)(Sidebar)

export default SidebarContainer
