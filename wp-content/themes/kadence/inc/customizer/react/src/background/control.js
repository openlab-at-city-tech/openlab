import { createRoot } from '@wordpress/element';

import BackgroundComponent from './background-component.js';

export const BackgroundControl = wp.customize.MediaControl.extend( {
	renderContent: function renderContent() {
		let control = this;
		let root = createRoot( control.container[0] );
		root.render( <BackgroundComponent control={control} customizer={ wp.customize }/> );
	},
	initialize: function( id, options ) {
		var control = this,
			args    = options || {};

		args.params = args.params || {};
		if ( ! args.params.type ) {
			args.params.type = 'kadence-basic';
		}
		if ( ! args.params.content ) {
			args.params.content = jQuery( '<li></li>' );
			args.params.content.attr( 'id', 'customize-control-' + id.replace( /]/g, '' ).replace( /\[/g, '-' ) );
			args.params.content.attr( 'class', 'customize-control customize-control-' + args.params.type );
		}
		control.propertyElements = [];
		wp.customize.Control.prototype.initialize.call( control, id, args );
	},

	/**
	 * Add bidirectional data binding links between inputs and the setting(s).
	 *
	 * This is copied from wp.customize.Control.prototype.initialize(). It
	 * should be changed in Core to be applied once the control is embedded.
	 *
	 * @private
	 * @returns {null}
	 */
	_setUpSettingRootLinks: function() {
		var control = this,
			nodes   = control.container.find( '[data-customize-setting-link]' );

		nodes.each( function() {
			var node = jQuery( this );

			wp.customize( node.data( 'customizeSettingLink' ), function( setting ) {
				var element = new wp.customize.Element( node );
				control.elements.push( element );
				element.sync( setting );
				element.set( setting() );
			} );
		} );
	},

	/**
	 * Add bidirectional data binding links between inputs and the setting properties.
	 *
	 * @private
	 * @returns {null}
	 */
	_setUpSettingPropertyLinks: function() {
		var control = this,
			nodes;

		if ( ! control.setting ) {
			return;
		}

		nodes = control.container.find( '[data-customize-setting-property-link]' );

		nodes.each( function() {
			var node = jQuery( this ),
				element,
				propertyName = node.data( 'customizeSettingPropertyLink' );

			element = new wp.customize.Element( node );
			control.propertyElements.push( element );
			element.set( control.setting()[ propertyName ] );

			element.bind( function( newPropertyValue ) {
				var newSetting = control.setting();
				if ( newPropertyValue === newSetting[ propertyName ] ) {
					return;
				}
				newSetting = _.clone( newSetting );
				newSetting[ propertyName ] = newPropertyValue;
				control.setting.set( newSetting );
			} );
			control.setting.bind( function( newValue ) {
				if ( newValue[ propertyName ] !== element.get() ) {
					element.set( newValue[ propertyName ] );
				}
			} );
		} );
	},

	/**
	 * @inheritdoc
	 */
	ready: function() {
		var control = this;
		// Shortcut so that we don't have to use _.bind every time we add a callback.
		_.bindAll( control, 'openFrame', 'select' );
		// Bind events, with delegation to facilitate re-rendering.
		control.container.on( 'click keydown', '.upload-button', function( e ) {
			let event = new CustomEvent( 'kadenceOpenMediaModal', {
				'detail': false,
			} );
			document.dispatchEvent( event );
			control.openFrame( e );
		} );

		control._setUpSettingRootLinks();
		control._setUpSettingPropertyLinks();

		wp.customize.Control.prototype.ready.call( control );

		control.setting.bind( control.renderContent() );

		control.deferred.embedded.done( function() {
		} );
	},

	/**
	 * Embed the control in the document.
	 *
	 * Override the embed() method to do nothing,
	 * so that the control isn't embedded on load,
	 * unless the containing section is already expanded.
	 *
	 * @returns {null}
	 */
	embed: function() {
		var control   = this,
			sectionId = control.section();

		if ( ! sectionId ) {
			return;
		}

		wp.customize.section( sectionId, function( section ) {
			if ( section.expanded() || wp.customize.settings.autofocus.control === control.id ) {
				control.actuallyEmbed();
			} else {
				section.expanded.bind( function( expanded ) {
					if ( expanded ) {
						control.actuallyEmbed();
					}
				} );
			}
		} );
	},

	/**
	 * Deferred embedding of control when actually
	 *
	 * This function is called in Section.onChangeExpanded() so the control
	 * will only get embedded when the Section is first expanded.
	 *
	 * @returns {null}
	 */
	actuallyEmbed: function() {
		var control = this;
		if ( 'resolved' === control.deferred.embedded.state() ) {
			return;
		}
		control.renderContent();
		control.deferred.embedded.resolve(); // This triggers control.ready().
		// Fire event after control is initialized.
		control.container.trigger( 'init' );
	},

	/**
	 * This is not working with autofocus.
	 *
	 * @param {object} [args] Args.
	 * @returns {null}
	 */
	focus: function( args ) {
		var control = this;
		control.actuallyEmbed();
		wp.customize.Control.prototype.focus.call( control, args );
	},
	/**
	 * Callback handler for when an attachment is selected in the media modal.
	 * Gets the selected image information, and sets it within the control.
	 */
	select: function() {
		// Get the attachment from the modal frame.
		var node,
			attachment = this.frame.state().get( 'selection' ).first().toJSON(),
			device = wp.customize.previewedDevice.get(),
			settings = this.setting();
		// This is a hack to make the popup background work, I don't like this and need to find a better solution.
		if ( this.id && this.id === 'header_popup_background' ) {
			if ( device === 'tablet' || device ===  'mobile' ) {
				if ( undefined !== settings['desktop'] && undefined !== settings['desktop'].type && settings['desktop'].type === 'image' ) {
					if ( undefined !== settings[device] && undefined !== settings[device].type && settings[device].type === 'image' ) {
						// Leave this alone
					} else {
						device = 'desktop';
					}
				}
			}
		}
		if ( undefined === this.params.attachment ) {
			this.params.attachment = {};
		}
		if ( undefined === this.params.input_attrs.attachments ) {
			this.params.input_attrs.attachments = {};
		}
		this.params.input_attrs.attachments[ device ] = attachment;
		if ( undefined === settings[ device ] ) {
			settings[ device ] = {};
		}
		if ( undefined === settings[ device ].image ) {
			settings[ device ].image = {};
		}
		settings[ device ].image.url = attachment.url;
		// Set the Customizer setting; the callback takes care of rendering.
		let event = new CustomEvent( 'kadenceOpenMediaModal', {
			'detail': true,
		} );
		document.dispatchEvent( event );
		this.setting.set( {
			...this.setting.get(),
			...settings,
			flag: ! this.setting.get().flag
		} );
	},
} );
