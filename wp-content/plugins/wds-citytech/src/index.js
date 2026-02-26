//import './blocks/openlab-help'
//import './blocks/openlab-support'

import './components/post-sharing-options'
import './components/pre-publication-privacy'

import './store'

wp.domReady( () => {
	const markVisibilityPanel = () => {
		const buttons = document.querySelectorAll(
			'.editor-post-publish-panel__prepublish .components-panel__body-toggle'
		)

		buttons.forEach( ( button ) => {
			if ( button.textContent.trim().startsWith( 'Visibility' ) ) {
				const panelBody = button.closest( '.components-panel__body' )
				if ( panelBody ) {
					panelBody.classList.add( 'openlab-hide-visibility' )
				}
			}
		} )
	}

	markVisibilityPanel()

	// In case the prepublish panel mounts later
	const observer = new MutationObserver( markVisibilityPanel )
	observer.observe( document.body, { childList: true, subtree: true } )
} )
