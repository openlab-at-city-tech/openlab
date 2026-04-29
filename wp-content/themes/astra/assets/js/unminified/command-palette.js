/**
 * Astra Command Palette Integration
 *
 * Registers Astra customizer panels with WordPress Command Palette.
 *
 * @package Astra
 * @since 4.11.18
 */

( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.data || ! wp.commands ) {
		return;
	}

	const { dispatch } = wp.data;
	const { store: commandsStore } = wp.commands;

	const config = window.astraCommandPalette || {};
	const customizerUrl = config.customizerUrl || '';
	const panels = config.panels || [];
	const iconUrl = config.iconUrl || '';

	if ( ! customizerUrl || panels.length === 0 ) {
		return;
	}

	const { createElement } = wp.element;
	const astraIcon = iconUrl ? createElement(
		'img',
		{
			src: iconUrl,
			alt: 'Astra',
			width: 20,
			height: 20,
		}
	) : null;

	// Function to register commands.
	function registerAstraCommands() {
		panels.forEach( function ( panel ) {
			let url = customizerUrl;
			if ( panel.type === 'panel' ) {
				url += '?autofocus[panel]=' + panel.id;
			} else if ( panel.type === 'section' ) {
				url += '?autofocus[section]=' + panel.id;
			}

			try {
				// Register the command.
				dispatch( commandsStore ).registerCommand( {
					name: panel.name,
					label: panel.label,
					searchLabel: panel.searchLabel || panel.label,
					icon: astraIcon,
					callback: function () {
						window.location.href = url;
						},
				} );
			} catch ( error ) {
				console.error( 'Astra Command Palette: Failed to register', panel.name, error );
			}
		} );
	}

	// Function to add click handler for admin bar search icon.
	function addSearchIconClickHandler() {
		const searchTrigger = document.querySelector( '#wp-admin-bar-astra-command-palette-search > a' );
		if ( searchTrigger ) {
			searchTrigger.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( wp.data && wp.data.dispatch ) {
					wp.data.dispatch( 'core/commands' ).open();
				}
			} );
		}
	}

	// Initialize the command registration and event handlers.
	const init = () => {
		registerAstraCommands();
		addSearchIconClickHandler();
	}

	// Wait for the editor to be ready before registering commands.
	if ( wp.domReady ) {
		wp.domReady( init );
	} else {
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', init );
		} else {
			init();
		}
	}
} )( window.wp );
