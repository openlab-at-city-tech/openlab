/**
 * A dynamic control.
 *
 * @class
 * @augments wp.customize.Control
 * @augments wp.customize.Class
 */
export const baseControl = wp.customize.KadenceControl = wp.customize.Control.extend( {

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

		control._setUpSettingRootLinks();
		control._setUpSettingPropertyLinks();

		wp.customize.Control.prototype.ready.call( control );

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
	 * Returns a WP-style warning notice React element if a notice string is
	 * provided via input_attrs.notice, otherwise returns null.
	 * Call this inside any child component's render() to get universal notice support.
	 *
	 * @returns {wp.element.Element|null}
	 */
	renderNotice: function() {
		var control = this;
		if ( ! control.params.input_attrs || ! control.params.input_attrs.notice ) {
			return null;
		}
		return wp.element.createElement(
			'div',
			{ className: 'notice notice-warning kadence-control-notice' },
			wp.element.createElement( 'p', { dangerouslySetInnerHTML: { __html: control.params.input_attrs.notice } } )
		);
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

} );