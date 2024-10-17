/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';
import { ReactSortable } from "react-sortablejs";
import uniqueId from 'lodash/uniqueId';

import ItemComponent from './item-component';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Tooltip, Popover, Button, SelectControl } = wp.components;

const { Component, Fragment } = wp.element;
class SocialComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.onDragEnd = this.onDragEnd.bind( this );
		this.onDragStart = this.onDragStart.bind( this );
		this.onDragStop = this.onDragStop.bind( this );
		this.removeItem = this.removeItem.bind( this );
		this.saveArrayUpdate = this.saveArrayUpdate.bind( this );
		this.toggleEnableItem = this.toggleEnableItem.bind( this );
		this.onChangeIcon = this.onChangeIcon.bind( this );
		this.onChangeLabel = this.onChangeLabel.bind( this );
		this.onChangeURL = this.onChangeURL.bind( this );
		this.onChangeAttachment = this.onChangeAttachment.bind( this );
		this.onChangeWidth = this.onChangeWidth.bind( this );
		this.onChangeSVG = this.onChangeSVG.bind( this );
		this.onChangeSource = this.onChangeSource.bind( this );
		this.addItem = this.addItem.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault = {
			'items': [
				{
					'id': 'facebook',
					'enabled': true,
					'source': 'icon',
					'url': '',
					'imageid': '',
					'width': 24,
					'icon': 'facebook',
					'label': 'Facebook',
					'svg': '',
				},
				{
					'id': 'twitter',
					'enabled': true,
					'source': 'icon',
					'url': '',
					'imageid': '',
					'width': 24,
					'icon': 'twitterAlt2',
					'label': 'X',
					'svg': '',
				}
			],
		};
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...this.defaultValue,
			...value
		} : this.defaultValue;
		let defaultParams = {
			'group' : 'social_item_group',
			'options': [
				{ value: 'facebook', label: __( 'Facebook', 'kadence' ) },
				{ value: 'twitter', label: __( 'X formerly Twitter', 'kadence' ) },
				{ value: 'instagram', label: __( 'Instagram', 'kadence' ) },
				{ value: 'threads', label: __( 'Threads', 'kadence' ) },
				{ value: 'youtube', label: __( 'YouTube', 'kadence' ) },
				{ value: 'facebook_group', label: __( 'Facebook Group', 'kadence' ) },
				{ value: 'vimeo', label: __( 'Vimeo', 'kadence' ) },
				{ value: 'pinterest', label: __( 'Pinterest', 'kadence' ) },
				{ value: 'linkedin', label: __( 'Linkedin', 'kadence' ) },
				{ value: 'medium', label: __( 'Medium', 'kadence' ) },
				{ value: 'wordpress', label: __( 'WordPress', 'kadence' ) },
				{ value: 'reddit', label: __( 'Reddit', 'kadence' ) },
				{ value: 'patreon', label: __( 'Patreon', 'kadence' ) },
				{ value: 'github', label: __( 'GitHub', 'kadence' ) },
				{ value: 'dribbble', label: __( 'Dribbble', 'kadence' ) },
				{ value: 'behance', label: __( 'Behance', 'kadence' ) },
				{ value: 'vk', label: __( 'VK', 'kadence' ) },
				{ value: 'xing', label: __( 'Xing', 'kadence' ) },
				{ value: 'rss', label: __( 'RSS', 'kadence' ) },
				{ value: 'email', label: __( 'Email', 'kadence' ) },
				{ value: 'phone', label: __( 'Phone', 'kadence' ) },
				{ value: 'whatsapp', label: __( 'WhatsApp', 'kadence' ) },
				{ value: 'google_reviews', label: __( 'Google Reviews', 'kadence' ) },
				{ value: 'telegram', label: __( 'Telegram', 'kadence' ) },
				{ value: 'yelp', label: __( 'Yelp', 'kadence' ) },
				{ value: 'trip_advisor', label: __( 'Trip Advisor', 'kadence' ) },
				{ value: 'imdb', label: __( 'IMDB', 'kadence' ) },
				{ value: 'soundcloud', label: __( 'SoundCloud', 'kadence' ) },
				{ value: 'tumblr', label: __( 'Tumblr', 'kadence' ) },
				{ value: 'discord', label: __( 'Discord', 'kadence' ) },
				{ value: 'tiktok', label: __( 'TikTok', 'kadence' ) },
				{ value: 'spotify', label: __( 'Spotify', 'kadence' ) },
				{ value: 'apple_podcasts', label: __( 'Apple Podcast', 'kadence' ) },
				{ value: 'flickr', label: __( 'Flickr', 'kadence' ) },
				{ value: '500px', label: __( '500PX', 'kadence' ) },
				{ value: 'bandcamp', label: __( 'Bandcamp', 'kadence' ) },
				{ value: 'anchor', label: __( 'Anchor', 'kadence' ) },
				{ value: 'custom1', label: __( 'Custom 1', 'kadence' ) },
				{ value: 'custom2', label: __( 'Custom 2', 'kadence' ) },
				{ value: 'custom3', label: __( 'Custom 3', 'kadence' ) },
			],
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		let availableSocialOptions = [];
		this.controlParams.options.map( ( option ) => {
			if ( ! value.items.some( obj => obj.id === option.value ) ) {
				availableSocialOptions.push( option );
			}
		} );
		this.state = {
			value: value,
			isVisible: false,
			control: ( undefined !== availableSocialOptions[0] && undefined !== availableSocialOptions[0].value ? availableSocialOptions[0].value : '' ),
		};
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
	saveArrayUpdate( value, index ) {
		let updateState = this.state.value;
		let items = updateState.items;

		const newItems = items.map( ( item, thisIndex ) => {
			if ( index === thisIndex ) {
				item = { ...item, ...value };
			}

			return item;
		} );
		updateState.items = newItems;
		this.setState( { value: updateState } );
		this.updateValues( updateState );
	}
	toggleEnableItem( value, itemIndex ) {
		this.saveArrayUpdate( { enabled: value }, itemIndex );
	}
	onChangeLabel( value, itemIndex ) {
		this.saveArrayUpdate( { label: value }, itemIndex );
	}
	onChangeIcon( value, itemIndex ) {
		this.saveArrayUpdate( { icon: value }, itemIndex );
	}
	onChangeURL( value, itemIndex ) {
		this.saveArrayUpdate( { url: value }, itemIndex );
	}
	onChangeAttachment( value, itemIndex ) {
		this.saveArrayUpdate( { imageid: value }, itemIndex );
	}
	onChangeWidth( value, itemIndex ) {
		this.saveArrayUpdate( { width: value }, itemIndex );
	}
	onChangeSVG( value, itemIndex ) {
		this.saveArrayUpdate( { svg: value }, itemIndex );
	}
	onChangeSource( value, itemIndex ) {
		this.saveArrayUpdate( { source: value }, itemIndex );
	}
	removeItem( itemIndex ) {
		let updateState = this.state.value;
		let update = updateState.items;
		let updateItems = [];
		{ update.length > 0 && (
			update.map( ( old, index ) => {
				if ( itemIndex !== index ) {
					updateItems.push( old );
				}
			} )
		) };
		updateState.items = updateItems;
		this.setState( { value: updateState } );
		this.updateValues( updateState );
	}
	addItem() {
		const itemControl = this.state.control;
		this.setState( { isVisible: false } );
		if ( itemControl ) {
			let updateState = this.state.value;
			let update = updateState.items;
			const itemLabel = this.controlParams.options.filter(function(o){return o.value === itemControl;} );
			let newItem = {
				'id': itemControl,
				'enabled': true,
				'source': 'icon',
				'url': '',
				'imageid': '',
				'width': 24,
				'icon': itemControl,
				'label': itemLabel[0].label,
				'svg': '',
			};
			update.push( newItem );
			updateState.items = update;
			let availableSocialOptions = [];
			this.controlParams.options.map( ( option ) => {
				if ( ! update.some( obj => obj.id === option.value ) ) {
					availableSocialOptions.push( option );
				}
			} );
			this.setState( { control: ( undefined !== availableSocialOptions[0] && undefined !== availableSocialOptions[0].value ? availableSocialOptions[0].value : '' ) } );
			this.setState( { value: updateState } );
			this.updateValues( updateState );
		}
	}
	onDragEnd( items ) {
		let updateState = this.state.value;
		let update = updateState.items;
		let updateItems = [];
		{ items.length > 0 && (
			items.map( ( item ) => {
				update.filter( obj => {
					if ( obj.id === item.id ) {
						updateItems.push( obj );
					}
				} )
			} )
		) };
		if ( ! this.arraysEqual( update, updateItems ) ) {
			update.items = updateItems;
			updateState.items = updateItems;
			this.setState( { value: updateState } );
			this.updateValues( updateState );
		}
	}
	arraysEqual( a, b ) {
		if (a === b) return true;
		if (a == null || b == null) return false;
		if (a.length != b.length) return false;		
		for (var i = 0; i < a.length; ++i) {
			if (a[i] !== b[i]) return false;
		}
		return true;
	}
	render() {
		const currentList = ( typeof this.state.value != "undefined" && this.state.value.items != null && this.state.value.items.length != null && this.state.value.items.length > 0 ? this.state.value.items : [] );
		let theItems = [];
		{ currentList.length > 0 && (
			currentList.map( ( item ) => {
				theItems.push(
					{
						id: item.id,
					}
				)
			} )
		) };
		const availableSocialOptions = [];
		this.controlParams.options.map( ( option ) => {
			if ( ! theItems.some( obj => obj.id === option.value ) ) {
				availableSocialOptions.push( option );
			}
		} );
		const toggleClose = () => {
			if ( this.state.isVisible === true ) {
				this.setState( { isVisible: false } );
			}
		};
		return (
			<div className="kadence-control-field kadence-sorter-items">
				<div className="kadence-sorter-row">
					<ReactSortable animation={100} onStart={ () => this.onDragStop() } onEnd={ () => this.onDragStop() } group={ this.controlParams.group } className={ `kadence-sorter-drop kadence-sorter-sortable-panel kadence-sorter-drop-${ this.controlParams.group }` } handle={ '.kadence-sorter-item-panel-header' } list={ theItems } setList={ ( newState ) => this.onDragEnd( newState ) } >
						{ currentList.length > 0 && (
							currentList.map( ( item, index ) => {
								return <ItemComponent removeItem={ ( remove ) => this.removeItem( remove ) } toggleEnabled={ ( enable, itemIndex ) => this.toggleEnableItem( enable, itemIndex ) } onChangeLabel={ ( label, itemIndex ) => this.onChangeLabel( label, itemIndex ) } onChangeSource={ ( source, itemIndex ) => this.onChangeSource( source, itemIndex ) } onChangeWidth={ ( width, itemIndex ) => this.onChangeWidth( width, itemIndex ) } onChangeSVG={ ( svg, itemIndex ) => this.onChangeSVG( svg, itemIndex ) } onChangeURL={ ( url, itemIndex ) => this.onChangeURL( url, itemIndex ) } onChangeAttachment={ ( imageid, itemIndex ) => this.onChangeAttachment( imageid, itemIndex ) } onChangeIcon={ ( icon, itemIndex ) => this.onChangeIcon( icon, itemIndex ) } key={ item.id } index={ index } item={ item } controlParams={ this.controlParams } />;
							} )
						) }
					</ReactSortable>
				</div>
				{ undefined !== availableSocialOptions[0] && undefined !== availableSocialOptions[0].value && (
					<div className="kadence-social-add-area">
						{/* <SelectControl
							value={ this.state.control }
							options={ availableSocialOptions }
							onChange={ value => {
								this.setState( { control: value } );
							} }
						/> */}
						{ this.state.isVisible && (
							<Popover position="top right" inline={true} className="kadence-popover-color kadence-popover-social kadence-customizer-popover" onClose={ toggleClose }>
								<div className="kadence-popover-social-list">
									<ButtonGroup className="kadence-radio-container-control">
										{ availableSocialOptions.map( ( item, index ) => {
											return (
												<Fragment>
													<Button
														isTertiary
														className={ 'social-radio-btn' }
														onClick={ () => {
															this.setState( { control: availableSocialOptions[index].value } );
															this.state.control = availableSocialOptions[index].value;
															this.addItem();
														} }
													>
														{ availableSocialOptions[index].label && (
															availableSocialOptions[index].label
														) }
													</Button>
												</Fragment>
											);
										} ) }
									</ButtonGroup>
								</div>
							</Popover>
						) }
						<Button
							className="kadence-sorter-add-item"
							isPrimary
							onClick={ () => {
								this.setState( { isVisible: true } );
							} }
						>
							{ __( 'Add Social', 'kadence' ) }
							<Dashicon icon="plus"/>
						</Button>
						{/* <Button
							className="kadence-sorter-add-item"
							isPrimary
							onClick={ () => {
								this.addItem();
							} }
						>
							{ __( 'Add Item', 'kadence' ) }
							<Dashicon icon="plus"/>
						</Button> */}
					</div>
				) }
			</div>
		);
	}
	updateValues( value ) {
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: !this.props.control.setting.get().flag
		} );
	}
}

SocialComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default SocialComponent;
