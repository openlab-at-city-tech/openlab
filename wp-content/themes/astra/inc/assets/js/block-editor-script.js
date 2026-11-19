/**
 * Safely access an iframe's document, returning null if cross-origin.
 *
 * @since x.x.x
 * @param {HTMLIFrameElement} iframe
 * @return {Document|null}
 */
function astraGetIframeDoc( iframe ) {
	if ( ! iframe ) {
		return null;
	}
	try {
		return iframe.contentWindow.document || iframe.contentDocument || null;
	} catch ( e ) {
		return null;
	}
}

// Editor-chrome lookups must never match elements inside block content — saved block markup can carry
// arbitrary classes, and a forged chrome class would capture Astra's injected UI and listeners.
function astraQueryEditorChrome( doc, selector ) {
	const element = doc ? doc.querySelector( selector ) : null;
	return ( element && element.closest( '[data-block]' ) ) ? null : element;
}

window.addEventListener( 'load', function(e) {
	astra_onload_function();
});

document.addEventListener('DOMContentLoaded', function () {
	if ( astraColors?.is_dark_palette ) {
		document.documentElement.classList.add('astra-dark-mode-enable');
	}
});

function addTitleVisibility() {
	let titleVisibility = astraQueryEditorChrome( document, '.title-visibility' ),
		titleBlock = astraQueryEditorChrome( document, '.edit-post-visual-editor__post-title-wrapper' ),
		editorDocument = document,
		postTitleOption = ( undefined !== wp.data.select( 'core/editor' ) && null !== wp.data.select( 'core/editor' ) && undefined !== wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) && wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-post-title'] ) ? wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-post-title'] : '';

	if ( astraColors.ast_wp_version_higher_6_3 ) {
		let desktopPreview = document.getElementsByClassName('is-desktop-preview'),
			tabletPreview = document.getElementsByClassName('is-tablet-preview'),
			mobilePreview = document.getElementsByClassName('is-mobile-preview'),
			devicePreview = desktopPreview[0];

		if ( tabletPreview.length > 0 ) {
			devicePreview = tabletPreview[0];
		} else if ( mobilePreview.length > 0 ) {
			devicePreview = mobilePreview[0];
		}

		let iframe = undefined !== devicePreview ? devicePreview.getElementsByTagName('iframe')[0] : undefined;
		if ( iframe && devicePreview.querySelector('iframe') !== null ) {
			editorDocument = astraGetIframeDoc( iframe ) || editorDocument;
		}

		// Addressed the WordPress 6.5 issue involving an extraneous iframe layer.
		if ( ! iframe && astraColors.ast_wp_version_higher_6_4 ) {
			let _iframe = document.querySelector('.editor-canvas__iframe') || document.querySelector('.block-editor-iframe__scale-container iframe[name="editor-canvas"]');
			editorDocument = astraGetIframeDoc( _iframe ) || editorDocument;

			if (editorDocument) {
				titleVisibility = astraQueryEditorChrome( editorDocument, '.title-visibility' );
				titleBlock = astraQueryEditorChrome( editorDocument, '.edit-post-visual-editor__post-title-wrapper' );
			}
		}
	}

	if( null !== titleBlock && null === titleVisibility ) {
		let titleVisibilityTrigger = '<span class="ast-title title-visibility" data-tooltip="Disable Title"> <svg xmlns="http://www.w3.org/2000/svg" width="0px" viewBox="0 0 576 512"><path d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg> </span>';

		if ( 'disabled' === postTitleOption ) {
			titleVisibilityTrigger = '<span class="ast-title title-visibility" data-tooltip="Enable Title"> <svg xmlns="http://www.w3.org/2000/svg" width="0px" viewBox="0 0 640 512"><path d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path></svg> </span>';
		}

		titleBlock.insertAdjacentHTML( 'beforeend', titleVisibilityTrigger );

		let titleVisibilityTriggerElement = astraQueryEditorChrome( editorDocument, '.title-visibility' );

		if (titleVisibilityTriggerElement) {
		titleVisibilityTriggerElement.addEventListener("click", function() {
			let metaTitleOptions = postTitleOption || '';
			if ( this.parentNode.classList.contains( 'invisible' ) && ( 'disabled' === metaTitleOptions || '' === metaTitleOptions ) ) {
				this.parentNode.classList.remove( 'invisible' );
				this.dataset.tooltip = 'Disable Title';
				titleVisibilityTriggerElement.innerHTML = '';
				titleVisibilityTriggerElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="0px" viewBox="0 0 576 512"><path d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg>';

				wp.data.dispatch( 'core/editor' ).editPost(
					{
						meta: {
							'site-post-title': '',
						}
					}
				);
			} else {
				this.parentNode.classList.add( 'invisible' );
				this.dataset.tooltip = 'Enable Title';
				titleVisibilityTriggerElement.innerHTML = '';
				titleVisibilityTriggerElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="0px" viewBox="0 0 640 512"><path d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path></svg>';

				wp.data.dispatch( 'core/editor' ).editPost(
					{
						meta: {
							'site-post-title': 'disabled',
						}
					}
				);
			}
		});
		}
	}

	// Sync the wrapper visibility from the meta on every run — undo/redo restores the meta without re-inserting the icon.
	if ( null !== titleBlock ) {
		if ( 'disabled' === postTitleOption && ! titleBlock.classList.contains('invisible') ) {
			titleBlock.classList.add('invisible');
		} else if ( 'disabled' !== postTitleOption && titleBlock.classList.contains('invisible') ) {
			titleBlock.classList.remove('invisible');
		}
	}
}

function siteLogoImageChange() {
	let mobileLogoState = astraColors.mobile_logo_state;

	if (!mobileLogoState) {
		return;
	}

	let mobileLogo = astraColors.mobile_logo;
	// Added OR condtion to check iframe content from WordPress 6.6 structure.
	let iframe = document.querySelector('.editor-canvas__iframe') || document.querySelector('.block-editor-iframe__scale-container iframe[name="editor-canvas"]');

	if (iframe) {
		let iframeDoc = astraGetIframeDoc( iframe );
		if ( ! iframeDoc ) {
			return;
		}
		let is_desktop = iframeDoc.querySelector(
			".is-desktop-preview"
		);

		if (!is_desktop) {
			let logoElement = iframeDoc.querySelector(".custom-logo");

			if (logoElement && logoElement.getAttribute("src") !== mobileLogo) {
				// Updating logo in the editor iframe preview with the mobile logo.
				logoElement.setAttribute("src", mobileLogo);
			}
		}
	}
}

function astra_onload_function() {

	/* Do things after DOM has fully loaded */

	var astraMetaBox = document.querySelector( '#astra_settings_meta_box' );
	if( astraMetaBox != null ){

		var titleCheckbox = document.getElementById('site-post-title');

		if( null === titleCheckbox ) {
			titleCheckbox = document.querySelector('.site-post-title input');
		}

		if( null !== titleCheckbox ) {
			titleCheckbox.addEventListener('change',function() {
				var titleBlock = document.querySelector('.editor-post-title__block');
				if( null !== titleBlock ) {
					if( titleCheckbox.checked ){
						titleBlock.style.opacity = '0.2';
					} else {
						titleBlock.style.opacity = '1.0';
					}
				}
			});
		}
	}

	// Replace 'VAR(--AST-GLOBAL-COLOR-X)' shown on the color palette custom color button with its color code.
	// textContent instead of innerText — innerText is layout-dependent and forces a reflow.
	// Live collection — only populated while a color picker popover is open, so per-notification syncs do no fresh DOM query.
	const customColorPickerButtons = document.getElementsByClassName( 'components-color-palette__custom-color-value' );

	const astraUpdateColorPalettePlaceholders = function () {
		for ( let btnCount = 0; btnCount < customColorPickerButtons.length; btnCount++ ) {
			const colorCode = customColorPickerButtons[btnCount].textContent || '',
				transformedCode = colorCode.toLowerCase();
			// Uppercase needle match retained from the innerText implementation, where CSS text-transform uppercased the value.
			if ( colorCode.toUpperCase().indexOf( 'VAR(--AST-GLOBAL-COLOR' ) > -1 && astraColors[ transformedCode ] ) {
				customColorPickerButtons[btnCount].textContent = astraColors[ transformedCode ];
			}
		}
	};

	// Keeps 'ast-stacked-title-visibility' in sync with the canvas width without reading layout from the store-notification path.
	const astraObserveCanvasWidth = function ( editorStylesWrapper ) {
		if ( editorStylesWrapper.dataset.astWidthObserved ) {
			return;
		}
		editorStylesWrapper.dataset.astWidthObserved = '1';

		const syncStackedTitleVisibility = function ( width ) {
			const shouldStack = width < 1350;
			if ( shouldStack !== editorStylesWrapper.classList.contains( 'ast-stacked-title-visibility' ) ) {
				editorStylesWrapper.classList.toggle( 'ast-stacked-title-visibility', shouldStack );
			}
		};

		if ( 'undefined' !== typeof ResizeObserver ) {
			new ResizeObserver( function ( entries ) {
				// contentRect is delivered by the observer — reading it forces no layout.
				syncStackedTitleVisibility( entries[0].contentRect.width );
			} ).observe( editorStylesWrapper );
		} else {
			syncStackedTitleVisibility( parseInt( editorStylesWrapper.offsetWidth ) );
		}
	};

	// Resolve the document the layout pass decorates — the device preview iframe on WP 6.3/6.4, the canvas iframe on WP 6.5+, the top document otherwise.
	// The subscriber's canvas-identity bail and the pass itself must resolve identically, so both use this helper.
	const astraResolveEditorDocument = function () {
		let editorDocument = document;

		if ( astraColors.ast_wp_version_higher_6_3 ) {
			let desktopPreview = document.getElementsByClassName('is-desktop-preview'),
				tabletPreview = document.getElementsByClassName('is-tablet-preview'),
				mobilePreview = document.getElementsByClassName('is-mobile-preview'),
				devicePreview = desktopPreview[0];

			if ( tabletPreview.length > 0 ) {
				devicePreview = tabletPreview[0];
			} else if ( mobilePreview.length > 0 ) {
				devicePreview = mobilePreview[0];
			}

			let iframe = undefined !== devicePreview ? devicePreview.getElementsByTagName('iframe')[0] : undefined;
			if ( iframe && devicePreview.querySelector('iframe') !== null ) {
				editorDocument = astraGetIframeDoc( iframe ) || editorDocument;
			}

			// Addressed the WordPress 6.5 issue involving an extraneous iframe layer.
			if ( ! iframe && astraColors.ast_wp_version_higher_6_4 ) {
				const _iframe = document.querySelector('.editor-canvas__iframe') || document.querySelector('.block-editor-iframe__scale-container iframe[name="editor-canvas"]');

				if ( !! _iframe ){
					editorDocument = astraGetIframeDoc( _iframe ) || editorDocument;
				}
			}
		}

		return editorDocument;
	};

	// Keep 'inherit-container-width' on root Group blocks using "Inherit default layout" in sync —
	// React rewrites the wrapper's className on re-render (selection, alignment), dropping the class.
	const astraSyncGroupInheritClasses = function ( editorDocument ) {
		const blockEditorSelect = wp.data.select( 'core/block-editor' ) || null;
		const rootClientIds = blockEditorSelect ? blockEditorSelect.getBlockOrder() : [];
		for ( let blockNum = 0; blockNum < rootClientIds.length; blockNum++ ) {
			if ( 'core/group' !== blockEditorSelect.getBlockName( rootClientIds[ blockNum ] ) ) {
				continue;
			}
			const groupAttributes = blockEditorSelect.getBlockAttributes( rootClientIds[ blockNum ] );
			if ( ! groupAttributes || ! groupAttributes.layout || undefined === groupAttributes.layout.inherit ) {
				continue;
			}
			// The canvas is iframed on WP 6.5+ — query the canvas document and match by client id, not by index.
			const groupElement = editorDocument.querySelector( '[data-block="' + CSS.escape( rootClientIds[ blockNum ] ) + '"]' );
			if ( ! groupElement ) {
				continue;
			}
			groupElement.classList.toggle( 'inherit-container-width', !! groupAttributes.layout.inherit );
		}
	};

	// Preview device from the store — core/editor on WP 6.5+, core/edit-post before that; lowercase-normalized.
	// Not from the DOM: during dispatch (and for a beat after) React has not committed the preview classes yet.
	const astraGetPreviewDevice = function () {
		const editorSelect = wp.data.select( 'core/editor' );
		const editPostSelect = wp.data.select( 'core/edit-post' );
		let device = 'desktop';
		if ( editorSelect && 'function' === typeof editorSelect.getDeviceType ) {
			device = editorSelect.getDeviceType();
		} else if ( editPostSelect && 'function' === typeof editPostSelect.__experimentalGetPreviewDeviceType ) {
			device = editPostSelect.__experimentalGetPreviewDeviceType();
		} else if ( document.getElementsByClassName( 'is-tablet-preview' ).length > 0 ) {
			device = 'tablet';
		} else if ( document.getElementsByClassName( 'is-mobile-preview' ).length > 0 ) {
			device = 'mobile';
		}
		return String( device ).toLowerCase();
	};

	// Cheap signature of every input the editor layout pass below depends on.
	// The pass runs only when this changes — not on every store notification.
	// KEEP IN SYNC: every post-meta key the pass (or updatePageBackground) reads must be listed here,
	// or edits to that setting will stop reflecting live in the editor.
	const astraGetEditorLayoutSignature = function () {
		const editorSelect = wp.data.select( 'core/editor' );
		if ( undefined === editorSelect || null === editorSelect ) {
			return null;
		}

		const meta = editorSelect.getEditedPostAttribute( 'meta' ) || {};

		const device = astraGetPreviewDevice();

		return JSON.stringify( [
			meta['ast-site-content-layout'] || '',
			meta['site-content-style'] || '',
			meta['site-sidebar-style'] || '',
			meta['site-sidebar-layout'] || '',
			meta['site-post-title'] || '',
			meta['ast-page-background-enabled'] || '',
			meta['ast-page-background-meta'] || '',
			meta['ast-content-background-meta'] || '',
			device,
		] );
	};

	let astraLastLayoutSignature = null;
	let astraLastCanvasWrapper = null;
	let astraLayoutPassScheduled = false;
	let astraLightSyncScheduled = false;

	// Show post/page title wrapper outline & eye icon only when clicked. Bound once per rendered title
	// input (astBound gate); called every light sync so a remounted title input re-binds.
	const astraBindTitleVisibilityListeners = function ( editorDocument ) {
		const titleBlock = astraQueryEditorChrome( editorDocument, '.edit-post-visual-editor__post-title-wrapper' );
		const titleInput = astraQueryEditorChrome( editorDocument, '.editor-post-title__input' );
		const visibilityIcon = astraQueryEditorChrome( editorDocument, '.title-visibility' );
		if ( null == titleBlock || null == titleInput || null == visibilityIcon || titleInput.dataset.astBound ) {
			return;
		}
		titleInput.dataset.astBound = '1';
		// The injected icon can be recreated by later renders — resolve it at event time, not bind time.
		const astraGetVisibilityIcon = function () {
			return astraQueryEditorChrome( editorDocument, '.title-visibility' ) || visibilityIcon;
		};
		if ( ! astraColors.ast_wp_version_higher_6_3 ) {
			editorDocument.addEventListener('click', function (event){
				if( ! titleBlock.contains( event.target ) ){
					astraGetVisibilityIcon().classList.remove('ast-show-visibility-icon');
					titleInput.classList.remove('ast-show-editor-title-outline');
				}
			});
		}
		editorDocument.addEventListener('visibilitychange', function (){
			astraGetVisibilityIcon().classList.remove('ast-show-visibility-icon');
			titleInput.classList.remove('ast-show-editor-title-outline');
		});
		titleBlock.addEventListener('focusout', function (){
			astraGetVisibilityIcon().classList.remove('ast-show-visibility-icon');
			titleInput.classList.remove('ast-show-editor-title-outline');
		});
		titleBlock.addEventListener('click', function (){
			astraGetVisibilityIcon().classList.add('ast-show-visibility-icon');
			titleInput.classList.add('ast-show-editor-title-outline');
		});
		titleInput.addEventListener('input', function (){
			astraGetVisibilityIcon().classList.add('ast-show-visibility-icon');
			this.classList.add('ast-show-editor-title-outline');
		});
	};

	// Cheap self-healing sync, run once per store-notification burst and deferred past React's commit:
	// these pieces decorate DOM that renders can recreate without changing anything the signature tracks
	// (selecting a Group rewrites its className, template toggles remount the logo/title, popovers mount late).
	const astraRunEditorLightSync = function () {
		astraLightSyncScheduled = false;
		const editorDocument = astraResolveEditorDocument();
		astraUpdateColorPalettePlaceholders();
		siteLogoImageChange();
		addTitleVisibility();
		astraBindTitleVisibilityListeners( editorDocument );
		astraSyncGroupInheritClasses( editorDocument );
	};

	wp.data.subscribe(function () {
		if ( ! astraLightSyncScheduled ) {
			astraLightSyncScheduled = true;
			setTimeout( astraRunEditorLightSync, 1 );
		}

		const layoutSignature = astraGetEditorLayoutSignature();
		if ( null === layoutSignature ) {
			return;
		}

		// Bail when nothing this pass depends on changed AND the canvas it last decorated is still the one on screen —
		// the overwhelming majority of notifications, including every keystroke. The identity check catches the canvas
		// being remounted with an unchanged signature (code editor round-trip, iframe reload).
		const currentCanvasWrapper = astraResolveEditorDocument().querySelector( '.editor-styles-wrapper' );
		if ( layoutSignature === astraLastLayoutSignature && currentCanvasWrapper === astraLastCanvasWrapper ) {
			return;
		}
		astraLastLayoutSignature = layoutSignature;

		// Coalesce multiple store notifications for the same change into one pass.
		if ( astraLayoutPassScheduled ) {
			return;
		}
		astraLayoutPassScheduled = true;

		setTimeout( function () {
			astraLayoutPassScheduled = false;
			var editorDocument = astraResolveEditorDocument();

			// Compatibility for updating layout in editor with direct reflection.
			const contentLayout = ( undefined !== wp.data.select( 'core/editor' ) && null !== wp.data.select( 'core/editor' ) && undefined !== wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) && wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['ast-site-content-layout'] ) ? wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['ast-site-content-layout'] : 'default',
				bodyClass       = document.querySelector('body'),
				editorBodyClass = astraColors.ast_wp_version_higher_6_3 ? editorDocument.querySelector('html') : false;
			const contentStyle = ( undefined !== wp.data.select( 'core/editor' ) && null !== wp.data.select( 'core/editor' ) && undefined !== wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) && wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-content-style'] ) ? wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-content-style'] : 'default';
			const sidebarStyle = ( undefined !== wp.data.select( 'core/editor' ) && null !== wp.data.select( 'core/editor' ) && undefined !== wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) && wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-sidebar-style'] ) ? wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-sidebar-style'] : 'default';
			const sidebarLayout = ( undefined !== wp.data.select( 'core/editor' ) && null !== wp.data.select( 'core/editor' ) && undefined !== wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' ) && wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-sidebar-layout'] ) ? wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )['site-sidebar-layout'] : 'default';
			const applyContainerLayoutClasses = (layout) => {
				switch (layout) {
					case "plain-container":
						bodyClass.classList.add("ast-plain-container");
						bodyClass.classList.remove(
							"ast-two-container",
							"ast-page-builder-template",
							"ast-separate-container",
							"ast-narrow-container"
						);
						if ( editorBodyClass ) {
							editorBodyClass.classList.add("ast-plain-container");
							editorBodyClass.classList.remove(
								"ast-two-container",
								"ast-page-builder-template",
								"ast-separate-container",
								"ast-narrow-container"
							);
						}
						break;
					case "content-boxed-container":
						bodyClass.classList.add("ast-separate-container");
						bodyClass.classList.remove(
							"ast-two-container",
							"ast-page-builder-template",
							"ast-plain-container",
							"ast-narrow-container"
						);
						if ( editorBodyClass ) {
							editorBodyClass.classList.add("ast-separate-container");
							editorBodyClass.classList.remove(
								"ast-two-container",
								"ast-page-builder-template",
								"ast-plain-container",
								"ast-narrow-container"
							);
						}
						break;
					case "boxed-container":
						bodyClass.classList.add(
							"ast-separate-container",
							"ast-two-container"
						);
						bodyClass.classList.remove(
							"ast-page-builder-template",
							"ast-plain-container",
							"ast-narrow-container"
						);
						if ( editorBodyClass ) {
							editorBodyClass.classList.add(
								"ast-separate-container",
								"ast-two-container"
							);
							editorBodyClass.classList.remove(
								"ast-page-builder-template",
								"ast-plain-container",
								"ast-narrow-container"
							);
						}
						break;
					case "page-builder-template":
						bodyClass.classList.add("ast-page-builder-template");
						bodyClass.classList.remove(
							"ast-two-container",
							"ast-plain-container",
							"ast-separate-container",
							"ast-narrow-container"
						);
						if ( editorBodyClass ) {
							editorBodyClass.classList.add("ast-page-builder-template");
							editorBodyClass.classList.remove(
								"ast-two-container",
								"ast-plain-container",
								"ast-separate-container",
								"ast-narrow-container"
							);
						}
						break;
					case "narrow-container":
						bodyClass.classList.add("ast-narrow-container");
						bodyClass.classList.remove(
							"ast-two-container",
							"ast-plain-container",
							"ast-separate-container",
							"ast-page-builder-template"
						);
						if ( editorBodyClass ) {
							editorBodyClass.classList.add("ast-narrow-container");
							editorBodyClass.classList.remove(
								"ast-two-container",
								"ast-plain-container",
								"ast-separate-container",
								"ast-page-builder-template"
							);
						}
						break;
					default:
						break;
				}
			};

			switch( contentLayout ) {
				case 'normal-width-container':
					applyContainerLayoutClasses( 'plain-container' );
				break;
				case 'narrow-width-container':
					applyContainerLayoutClasses( 'narrow-container' );
				break;
				case 'full-width-container':
					applyContainerLayoutClasses( 'page-builder-template' );
				break;
				case 'default':
					if( bodyClass && bodyClass.classList.contains( 'ast-default-layout-boxed-container' ) ) {
						applyContainerLayoutClasses( 'boxed-container' );
					} else if( bodyClass && bodyClass.classList.contains( 'ast-default-layout-content-boxed-container' ) ) {
						applyContainerLayoutClasses( 'content-boxed-container' );
					} else if( bodyClass && bodyClass.classList.contains( 'ast-default-layout-page-builder' ) ) {
						applyContainerLayoutClasses( 'page-builder-template' );
					} else if( bodyClass && bodyClass.classList.contains( 'ast-default-layout-plain-container' ) ) {
						applyContainerLayoutClasses( 'plain-container' );
					} else if( bodyClass && bodyClass.classList.contains( 'ast-default-layout-narrow-container' ) ) {
						applyContainerLayoutClasses( 'narrow-container' );
					}
				break;
			}

			const is_default_boxed         = bodyClass && bodyClass.classList.contains( 'ast-default-layout-boxed-container' ) ? true : false;
			const is_default_content_boxed = bodyClass && bodyClass.classList.contains( 'ast-default-layout-content-boxed-container' ) ? true : false;
			const is_default_normal        = bodyClass && bodyClass.classList.contains( 'ast-default-layout-plain-container' ) ? true : false;
			const is_default_normal_width  = ( 'default' === contentLayout && ( is_default_boxed || is_default_content_boxed || is_default_normal ) );
			const is_content_style_boxed   = bodyClass && bodyClass.classList.contains( 'ast-default-content-style-boxed' ) ? true : false;
			const is_sidebar_style_boxed   = bodyClass && bodyClass.classList.contains( 'ast-default-sidebar-boxed' ) ? true : false;

			if ( 'normal-width-container' === contentLayout || is_default_normal_width ) {
				switch ( contentStyle ) {
					case 'boxed':
						applyContainerLayoutClasses( 'boxed-container' );
						break;
					case 'unboxed':
						applyContainerLayoutClasses( 'plain-container' );
					break;
					default:
						if ( is_content_style_boxed ) {
							applyContainerLayoutClasses( 'boxed-container' );
						}
						break;
				}

				const is_sidebar_default_enabled = 'default' === sidebarLayout && ( ! bodyClass.classList.contains( 'ast-sidebar-default-no-sidebar' ) );
				if( ( 'default' !== sidebarLayout && 'no-sidebar' !== sidebarLayout || is_sidebar_default_enabled ) ) {
					switch ( sidebarStyle ) {
						case 'boxed':
							applyContainerLayoutClasses( 'boxed-container' );
							break;
						case 'unboxed':
							applyContainerLayoutClasses( 'content-boxed-container' );
							if ( 'unboxed' === contentStyle || 'default' === contentStyle && ! is_content_style_boxed ) {
								applyContainerLayoutClasses( 'plain-container' );
							}
							break;
						default:
							if (
								( 'unboxed' === contentStyle && ! is_sidebar_style_boxed ) ||
								( 'default' === contentStyle && ! is_sidebar_style_boxed && ! is_content_style_boxed )
							) {
								applyContainerLayoutClasses( 'plain-container' );
							}
							else if ( is_sidebar_style_boxed ) {
								applyContainerLayoutClasses( 'boxed-container' );
							}
							else if ( ! is_sidebar_style_boxed ) {
								applyContainerLayoutClasses( 'content-boxed-container' );
							}
							break;
					}
				}
			}

			// Narrow + Boxed compatibility in editor.
			if ( 'narrow-width-container' === contentLayout && ( 'boxed' === contentStyle || 'default' === contentStyle && is_content_style_boxed ) ) {
				let editorArea = document.querySelector('.edit-post-visual-editor__content-area');
				if ( ! editorArea ) {
					editorArea = document.querySelector('.edit-post-visual-editor');
				}
				if ( editorArea ) {
					editorArea.style.padding = '20px';
				}
			}
			else {
				let editorArea = document.querySelector('.edit-post-visual-editor__content-area');

				if ( ! editorArea ) {
					editorArea = document.querySelector('.edit-post-visual-editor');
				}

				if ( editorArea ) {
					editorArea.style.padding = '0px';
				}
			}

			// Container unboxed + sidebar boxed case.
			let isUnboxedContainer = false;
			const is_sidebar_default_enabled = 'default' === sidebarLayout && ( ! bodyClass.classList.contains( 'ast-sidebar-default-no-sidebar' ) );
			if ( ( 'normal-width-container' === contentLayout || is_default_normal_width ) ) {
				if ( is_sidebar_default_enabled || 'no-sidebar' !== sidebarLayout && 'default' !== sidebarLayout ) {
					if ( 'default' === contentStyle && ! is_content_style_boxed ||  'unboxed' === contentStyle ) {
						if ( 'boxed' === sidebarStyle || 'default' === sidebarStyle && is_sidebar_style_boxed ) {
							isUnboxedContainer = true;
						}
					}
				}
			}

			const editorStylesWrapper = editorDocument.querySelector( '.editor-styles-wrapper' );

			// Record which canvas this pass decorated (null when none was mounted) — the subscriber re-runs the pass
			// when a different or newly mounted canvas appears, even with an unchanged signature.
			astraLastCanvasWrapper = editorStylesWrapper;

			if( null !== editorStylesWrapper ) {
				astraObserveCanvasWidth( editorStylesWrapper );
			}

			// Store-derived device — the DOM preview classes may not be committed yet when this pass runs.
			const previewDevice = astraGetPreviewDevice();
			document.body.classList.toggle( 'responsive-enabled', 'desktop' !== previewDevice );


			// Live reflections for page background setting.
			if ( astraColors.is_astra_pro_colors_activated ) {
				const backgroundToggle = (undefined !== wp.data.select('core/editor') &&
				null !== wp.data.select('core/editor') &&
				undefined !== wp.data.select('core/editor').getEditedPostAttribute('meta') &&
				wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-page-background-enabled'])
				? wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-page-background-enabled']
				: 'default';

				if ( 'enabled' === backgroundToggle ) {
					if ( isUnboxedContainer ) {
						updatePageBackground( false, isUnboxedContainer, previewDevice );
					}
					else {
						updatePageBackground( false, false, previewDevice );
					}
				}
				else if ( 'default' === backgroundToggle ) {
					updatePageBackground( true );
				}
			}

		}, 1 );
	});

	// Redirect to Site Builder on click of "View Posts" Icon if Site Builder layout.
	if ( document && document.body ) {
		const isSiteBuilderLayout = document.body.classList.contains( 'post-type-astra-advanced-hook' );
		if ( isSiteBuilderLayout ) {
			const viewPostsIcon = document.querySelector( '#editor .interface-navigable-region .edit-post-header > div a.components-button.edit-post-fullscreen-mode-close' );
			if ( viewPostsIcon ) {
				viewPostsIcon.addEventListener( 'click', function(e) {
					e.preventDefault();
					window.location.href = astraColors.site_builder_url;
				});
			}
		}
	}

}

/*
* Updates the page background css from the color picker.
*/
const updatePageBackground = ( apply_customizer_default = false, isUnboxedContainer = false, device = 'desktop' ) => {

	// Document as per wp version.
	let editorDoc = document;
	let is_boxed_based_layout = false;
	
	let _iframe = document.querySelector("#editor iframe.editor-canvas__iframe") || document.querySelector('.block-editor-iframe__scale-container iframe[name="editor-canvas"]');
	
	if (_iframe && astraColors.ast_wp_version_higher_6_4) {
		editorDoc = astraGetIframeDoc( _iframe ) || editorDoc;
	}

	let desktopPreview = editorDoc.getElementsByClassName('is-desktop-preview'),
		tabletPreview = editorDoc.getElementsByClassName('is-tablet-preview'),
		mobilePreview = editorDoc.getElementsByClassName('is-mobile-preview'),
		devicePreview = desktopPreview[0];
	if ( astraColors.ast_wp_version_higher_6_3 ) {

		if ( tabletPreview.length > 0 ) {
			devicePreview = tabletPreview[0];
		} else if ( mobilePreview.length > 0 ) {
			devicePreview = mobilePreview[0];
		}

		let iframe = undefined !== devicePreview ? devicePreview.getElementsByTagName('iframe')[0] : undefined;
		if ( iframe && devicePreview.querySelector('iframe') !== null ) {
			editorDoc = astraGetIframeDoc( iframe ) || editorDoc;
		}
	}

	if ( apply_customizer_default ) {

		if ( document ) {
			const pageBgWrapper = document.querySelector('#editor .edit-post-visual-editor');

			if ( pageBgWrapper ) {
				pageBgWrapper.style['background-color'] = '';
				pageBgWrapper.style['background-image'] = '';
				pageBgWrapper.style['background-size'] = '';
				pageBgWrapper.style['background-position'] = '';
				pageBgWrapper.style['background-repeat'] = '';
				pageBgWrapper.style['background-attachment'] = '';

			}
		}

		if ( editorDoc ) {

			const contentBgWrapper = editorDoc.querySelector('.editor-styles-wrapper');

			if ( contentBgWrapper ) {
				contentBgWrapper.style['background-color'] = '';
				contentBgWrapper.style['background-image'] = '';
				contentBgWrapper.style['background-size'] = '';
				contentBgWrapper.style['background-position'] = '';
				contentBgWrapper.style['background-repeat'] = '';
				contentBgWrapper.style['background-attachment'] = '';
			}
		}

		return;
	}

	let bgObj = (undefined !== wp.data.select('core/editor') &&
	null !== wp.data.select('core/editor') &&
	undefined !== wp.data.select('core/editor').getEditedPostAttribute('meta') &&
	wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-page-background-meta'])
	? wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-page-background-meta']
	: 'default';

	let contentObj = (undefined !== wp.data.select('core/editor') &&
	null !== wp.data.select('core/editor') &&
	undefined !== wp.data.select('core/editor').getEditedPostAttribute('meta') &&
	wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-content-background-meta'])
	? wp.data.select('core/editor').getEditedPostAttribute('meta')['ast-content-background-meta']
	: 'default';

	if ( 'desktop' === device ) {

		// Get the background object css values and update page background.
		const desktopCSS = astraGetResponsiveBackgroundObj(bgObj, 'desktop');
		applyStylesToElement('#editor .edit-post-visual-editor', desktopCSS, document );

		// Check current layout.
		is_boxed_based_layout = false;
		if ( document && document.querySelector('body') ) {
			is_boxed_based_layout = document.querySelector('body').classList.contains('ast-separate-container');
		}

		if ( astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Content Bg */
			// Get the background object css values and update page content background.
			const desktopContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'desktop');
			applyStylesToElement('.editor-styles-wrapper', desktopContentCSS, editorDoc );

		}
		else if ( ! astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Page Bg */
			// Get the background object css values and update page background.
			const desktopCSS = astraGetResponsiveBackgroundObj(bgObj, 'desktop');
			applyStylesToElement('.editor-styles-wrapper', desktopCSS, document );

		}
		else if ( is_boxed_based_layout ) {

			/** Boxed Layouts with Content Bg & Page Bg */
			// Get the background object css values and update page background.
			const desktopCSS = astraGetResponsiveBackgroundObj(bgObj, 'desktop');
			applyStylesToElement('#editor .edit-post-visual-editor', desktopCSS, document );

			// Get the background object css values and update page content background.
			const desktopContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'desktop');
			applyStylesToElement('.editor-styles-wrapper', desktopContentCSS, editorDoc );

		}

		if ( isUnboxedContainer ) {

			// Container unboxed + sidebar boxed -> update page content background to site background.
			applyStylesToElement('.editor-styles-wrapper', desktopCSS, editorDoc );
		}

	}
	else if ( 'tablet' === device ) {

		// Check current layout.
		is_boxed_based_layout = false;
		if ( document && document.querySelector('body') ) {
			is_boxed_based_layout = document.querySelector('body').classList.contains('ast-separate-container');
		}

		if ( astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Content Bg */
			// Get the background object css values and update page content background.
			const tabletContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'tablet');
			applyStylesToElement('.editor-styles-wrapper', tabletContentCSS, editorDoc );

			// Set page background to black to indicate that page background not applicable.
			applyStylesToElement('#editor .edit-post-visual-editor', {'background-color' : '#363636'}, document );
		}
		else if ( ! astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Page Bg */
			// Get the background object css values and update page background.
			const tabletCSS = astraGetResponsiveBackgroundObj(bgObj, 'tablet');
			applyStylesToElement('.editor-styles-wrapper', tabletCSS, document );

		}
		else if ( is_boxed_based_layout ) {

			/** Boxed Layouts with Content Bg & Page Bg */
			// Get the background object css values and update page background.
			const tabletCSS = astraGetResponsiveBackgroundObj(bgObj, 'tablet');
			applyStylesToElement('#editor .edit-post-visual-editor', tabletCSS, document );

			// Get the background object css values and update page content background.
			const tabletContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'tablet');
			applyStylesToElement('.editor-styles-wrapper', tabletContentCSS, editorDoc );

		}
	}
	else if ( 'mobile' === device ) {

		// Check current layout.
		is_boxed_based_layout = false;
		if ( document && document.querySelector('body') ) {
			is_boxed_based_layout = document.querySelector('body').classList.contains('ast-separate-container');
		}

		if ( astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Content Bg */
			// Get the background object css values and update page content background.
			const mobileContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'mobile');
			applyStylesToElement('.editor-styles-wrapper', mobileContentCSS, editorDoc );

			// Set page background to black to indicate that page background not applicable.
			applyStylesToElement('#editor .edit-post-visual-editor', {'background-color' : '#363636'}, document );
		}
		else if ( ! astraColors.apply_content_bg_fullwidth && ( ! is_boxed_based_layout ) ) {

			/** Fullwidth with Page Bg */
			// Get the background object css values and update page background.
			const mobileCSS = astraGetResponsiveBackgroundObj(bgObj, 'mobile');
			applyStylesToElement('.editor-styles-wrapper', mobileCSS, document );

		}
		else if ( is_boxed_based_layout ) {

			/** Boxed Layouts with Content Bg & Page Bg */
			// Get the background object css values and update page background.
			const mobileCSS = astraGetResponsiveBackgroundObj(bgObj, 'mobile');
			applyStylesToElement('#editor .edit-post-visual-editor', mobileCSS, document );

			// Get the background object css values and update page content background.
			const mobileContentCSS = astraGetResponsiveBackgroundObj(contentObj, 'mobile');
			applyStylesToElement('.editor-styles-wrapper', mobileContentCSS, editorDoc );

		}
	}

}

/*
* Dynamically applies styles to DOM element.
*/
function applyStylesToElement( selector, styles, docObj ) {
  if ( docObj ) {
	  const element = docObj.querySelector(selector);
	  if (element) {
	  // Remove any prior cache values if set already.
  	  element.style.backgroundImage = 'none';
	  	Object.keys(styles).forEach((property) => {
			element.style[property] = styles[property];
  		});
	  } else {
	  	console.error(`Element with selector "${selector}" not found.`);
	  }
  }
}

/*
* Generate Responsive Background Color CSS.
*/
function astraGetResponsiveBackgroundObj(bgObjRes, device) {
 const genBgCss = {};

 const bgObj = bgObjRes[device];
 const bgImg = bgObj['background-image'] || '';
 const bgTabImg = bgObjRes['tablet']['background-image'] || '';
 const bgDeskImg = bgObjRes['desktop']['background-image'] || '';
 const bgColor = bgObj['background-color'] || '';
 const tabletCss = bgObjRes['tablet']['background-image'] ? true : false;
 const desktopCss = bgObjRes['desktop']['background-image'] ? true : false;

 const bgType = bgObj['background-type'] || '';

 if ('' !== bgType) {
   switch (bgType) {
	 case 'color':
	   if ('' !== bgImg && '' !== bgColor) {
		 genBgCss['background-image'] = `linear-gradient(to right, ${bgColor}, ${bgColor}), url(${bgImg})`;
	   } else if ('mobile' === device) {
		 if (desktopCss) {
		   genBgCss['background-image'] = `linear-gradient(to right, ${bgColor}, ${bgColor}), url(${bgDeskImg})`;
		 } else if (tabletCss) {
		   genBgCss['background-image'] = `linear-gradient(to right, ${bgColor}, ${bgColor}), url(${bgTabImg})`;
		 } else {
		   if ('' !== bgColor) {
			 genBgCss['background-color'] = bgColor;
			 genBgCss['background-image'] = 'none';
		   }
		 }
	   } else if ('tablet' === device) {
		 if (desktopCss) {
		   genBgCss['background-image'] = `linear-gradient(to right, ${bgColor}, ${bgColor}), url(${bgDeskImg})`;
		 } else {
		   if ('' !== bgColor) {
			 genBgCss['background-color'] = bgColor;
			 genBgCss['background-image'] = 'none';
		   }
		 }
	   } else if ('' === bgImg) {
		 genBgCss['background-color'] = bgColor;
		 genBgCss['background-image'] = 'none';
	   }
	   break;

	 case 'image':
	   const overlayType = bgObj['overlay-type'] || 'none';
	   const overlayColor = bgObj['overlay-color'] || '';
	   const overlayGrad = bgObj['overlay-gradient'] || '';

	   if ('' !== bgImg) {
		 if ('none' !== overlayType) {
		   if ('classic' === overlayType && '' !== overlayColor) {
			 genBgCss['background-image'] = `linear-gradient(to right, ${overlayColor}, ${overlayColor}), url(${bgImg})`;
		   } else if ('gradient' === overlayType && '' !== overlayGrad) {
			 genBgCss['background-image'] = `${overlayGrad}, url(${bgImg})`;
		   } else {
			 genBgCss['background-image'] = `url(${bgImg})`;
		   }
		 } else {
		   genBgCss['background-image'] = `url(${bgImg})`;
		 }
	   }
	   break;

	 case 'gradient':
	   if (bgColor) {
		 genBgCss['background-image'] = bgColor;
	   }
	   break;

	 default:
	   break;
   }
 } else if ('' !== bgColor) {
   genBgCss['background-color'] = bgColor;
 }

 if ('' !== bgImg) {
   if (bgObj['background-repeat']) {
	 genBgCss['background-repeat'] = bgObj['background-repeat'];
   }

   if (bgObj['background-position']) {
	 genBgCss['background-position'] = bgObj['background-position'];
   }

   if (bgObj['background-size']) {
	 genBgCss['background-size'] = bgObj['background-size'];
   }

   if (bgObj['background-attachment']) {
	 genBgCss['background-attachment'] = bgObj['background-attachment'];
   }
 }

  return genBgCss;
}
