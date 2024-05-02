/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';
import { ReactSortable } from "react-sortablejs";
import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Tooltip, Button } = wp.components;

const { Component, Fragment } = wp.element;
class AvailableComponent extends Component {
	constructor() {
		super( ...arguments );
		this.linkRemovingItem = this.linkRemovingItem.bind( this );
		this.onDragEnd = this.onDragEnd.bind( this );
		this.onDragStart = this.onDragStart.bind( this );
		this.onUpdate = this.onUpdate.bind( this );
		this.onDragStop = this.onDragStop.bind( this );
		this.focusPanel = this.focusPanel.bind( this );
		let settings = {};
		let defaultParams = {};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		if ( kadenceCustomizerControlsData.source && undefined !== this.props.customizer.control( kadenceCustomizerControlsData.source + '[' + this.controlParams.group + ']' ) ) {
			settings = this.props.customizer.control( kadenceCustomizerControlsData.source + '[' + this.controlParams.group + ']' ).setting.get();
		} else if ( undefined !== this.props.customizer.control( this.controlParams.group ) ) {
			settings = this.props.customizer.control( this.controlParams.group ).setting.get();
		}
		this.choices = ( kadenceCustomizerControlsData && kadenceCustomizerControlsData.choices && kadenceCustomizerControlsData.choices[ this.controlParams.group ] ? kadenceCustomizerControlsData.choices[ this.controlParams.group ] : [] );
		this.state = {
			settings: settings,
		};
		this.linkRemovingItem();
	}
	onUpdate() {
		if ( kadenceCustomizerControlsData.source && undefined !== this.props.customizer.control( kadenceCustomizerControlsData.source + '[' + this.controlParams.group + ']' ) ) {
			const settings = this.props.customizer.control( kadenceCustomizerControlsData.source + '[' + this.controlParams.group + ']' ).setting.get();
			this.setState( { settings: settings } );
		} else if ( undefined !== this.props.customizer.control( this.controlParams.group ) ) {
			const settings = this.props.customizer.control( this.controlParams.group ).setting.get();
			this.setState( { settings: settings } );
		}
	}
	onDragStart() {
		var dropzones = document.querySelectorAll( '.kadence-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.add( 'kadence-dragging-dropzones' );
		}
	}
	onDragStop() {
		var dropzones = document.querySelectorAll( '.kadence-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.remove( 'kadence-dragging-dropzones' );
		}
	}
	focusPanel( item ) {
		if ( undefined !== this.props.customizer.section( this.choices[ item ].section ) ) {
			this.props.customizer.section( this.choices[ item ].section ).focus();
		}
	}
	onDragEnd( items ) {
		if ( items.length != null && items.length === 0 ) {
			this.onUpdate();
		}
	}
	render() {
		const renderItem = ( item, row ) => {
			let available = true;
			this.controlParams.zones.map( ( zone ) => {
				Object.keys( this.state.settings[zone] ).map( ( area ) => {
					if ( this.state.settings[zone][area].includes( item ) ) {
						available = false;
					}
				} );
			} );
			let theitem = [ {
				id: item,
			} ];
			return (
				<Fragment>
					{ available && row === 'available' && (
						<ReactSortable animation={100} onStart={ () => this.onDragStart() } onEnd={ () => this.onDragStop() } group={ { name: this.controlParams.group, put: false } } className={ 'kadence-builder-item-start kadence-move-item' } list={ theitem } setList={ newState => this.onDragEnd( newState ) } >
							<div className="kadence-builder-item" data-id={ item } data-section={ undefined !== this.choices[ item ] && undefined !== this.choices[ item ].section ? this.choices[ item ].section : '' } key={ item }>
								<span
									className="kadence-builder-item-icon kadence-move-icon"
								>
									{ Icons['drag'] }
								</span>
								{ ( undefined !== this.choices[ item ] && undefined !== this.choices[ item ].name ? this.choices[ item ].name : '' ) }
							</div>
						</ReactSortable>
					) }
					{ ! available && row === 'links' && (
						<div className={ 'kadence-builder-item-start' }>
							<Button className="kadence-builder-item" data-id={ item } onClick={ () => this.focusPanel( item ) } data-section={ undefined !== this.choices[ item ] && undefined !== this.choices[ item ].section ? this.choices[ item ].section : '' } key={ item }>
								{ ( undefined !== this.choices[ item ] && undefined !== this.choices[ item ].name ? this.choices[ item ].name : '' ) }
								<span
									className="kadence-builder-item-icon"
								>
									<Dashicon icon="arrow-right-alt2"/>
								</span>
							</Button>
						</div>
					) }
				</Fragment>
			);
		};
		return (
			<div className="kadence-control-field kadence-available-items">
				{ Object.keys( this.choices ).map( ( item ) => {
					return renderItem( item, 'links' );
				} ) }
				<div className="kadence-available-items-title">
					<span className="customize-control-title">{ __( 'Available Items', 'kadence' ) }</span>
				</div>
				<div className="kadence-available-items-pool">
					{ Object.keys( this.choices ).map( ( item ) => {
						return renderItem( item, 'available' );
					} ) }
				</div>
			</div>
		);
	}

	linkRemovingItem() {
		let self = this;
		document.addEventListener( 'kadenceRemovedBuilderItem', function( e ) {
			if ( e.detail === self.controlParams.group ) {
				self.onUpdate();
			}
		} );
	}
}

AvailableComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default AvailableComponent;
