/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ContactIcons from './icons.js';
import FontIconPicker from '@fonticonpicker/react-fonticonpicker';

import { __ } from '@wordpress/i18n';
const { MediaUpload } = wp.blockEditor;
const { ButtonGroup, Dashicon, Tooltip, TextControl, Button, TabPanel, RangeControl, Placeholder } = wp.components;

const { Component, Fragment } = wp.element;
class ItemComponent extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			open: false,
		};
	}
	render() {
		return (
			<div className="kadence-sorter-item" data-id={ this.props.item.id } key={ this.props.item.id }>
				<div className="kadence-sorter-item-panel-header">
					<Tooltip text={ __( 'Toggle Item Visibility', 'kadence' ) }>
						<Button
							className={ `kadence-sorter-visiblity ${ ( this.props.item.enabled ? 'item-is-visible' : 'item-is-hidden' ) }`}
							onClick={ () => {
								this.props.toggleEnabled( ( this.props.item.enabled ? false : true ), this.props.index );
							} }
						>
							{ ContactIcons[this.props.item.id] }
							
						</Button>
					</Tooltip>
					<span className="kadence-sorter-title">
						{ ( undefined !== this.props.item.label && '' !== this.props.item.label ? this.props.item.label : __( 'Contact Item', 'kadence' ) ) }
					</span>
					<Tooltip text={ __( 'Expand Item Controls', 'kadence' ) }>
						<Button
							className="kadence-sorter-item-expand"
							onClick={ () => {
								this.setState( { open: ( this.state.open ? false : true ) } )
							} }
						>
							<Dashicon icon={ ( this.state.open ? 'arrow-up-alt2' : 'arrow-down-alt2' ) }/>
						</Button>
					</Tooltip>
				</div>
				{ this.state.open && (
					<div className="kadence-sorter-item-panel-content">
						<TabPanel className="sortable-style-tabs kadence-contact-type"
							activeClass="active-tab"
							initialTabName={ ( undefined !== this.props.item.source ? this.props.item.source : 'icon' ) }
							onSelect={ ( value ) => this.props.onChangeSource( value, this.props.index ) }
							tabs={ [
								{
									name: 'icon',
									title: __( 'Icon', 'kadence' ),
								},
								{
									name: 'image',
									title: __( 'Image', 'kadence' ),
								},
							] }>
							{
								( tab ) => {
									let tabout;
									if ( tab.name ) {
										if ( 'image' === tab.name ) {
											tabout = (
												<Fragment>
													{ ! this.props.item.url && (
														<div className="attachment-media-view">
															<MediaUpload
																onSelect={ ( imageData ) => {
																	this.props.onChangeURL( imageData.url, this.props.index );
																	this.props.onChangeAttachment( imageData.id, this.props.index );
																} }
																allowedTypes={ ['image'] }
																render={ ( { open } ) => (
																	<Button className="button-add-media" isSecondary onClick={ open }>
																		{ __( 'Add Image', 'kadence' )}
																	</Button>
																) }
															/>
														</div>
													) }
													{ this.props.item.url && (
														<div className="contact-custom-image">
															<div className="kadence-Contact-image">
																<img className="kadence-Contact-image-preview" src={ this.props.item.url } />
															</div>
															<Button
																className='remove-image'
																isDestructive
																onClick={ () => {
																	this.props.onChangeURL( '', this.props.index );
																	this.props.onChangeAttachment( '', this.props.index );
																} }
															>
																{ __( 'Remove Image', 'kadence' ) }
																<Dashicon icon='no'/>
															</Button>
														</div>
													) }
													<RangeControl
														label={ __( 'Max Width (px)', 'kadence' ) }
														value={ ( undefined !== this.props.item.width ? this.props.item.width : 24 ) }
														onChange={ ( value ) => {
															this.props.onChangeWidth( value, this.props.index );
														} }
														step={ 1 }
														min={ 2 }
														max={ 100 }
													/>
												</Fragment>
											);
										} else {
											tabout = (
												<Fragment>
													<ButtonGroup className="kadence-radio-container-control">
														<Button
															isTertiary
															className={ ( this.props.item.id === ( undefined !== this.props.item.icon ? this.props.item.icon : this.props.item.id ) ?
																	'active-radio ' :
																	'' ) + 'svg-icon-' + this.props.item.id }
															onClick={ () => {
																this.props.onChangeIcon( this.props.item.id, this.props.index );
															} }
														>
															<span className="kadence-radio-icon">
																{ ContactIcons[this.props.item.id] }
															</span>
														</Button>
														{ ContactIcons[ this.props.item.id + 'Alt' ] && (
															<Button
																isTertiary
																className={ ( this.props.item.id + 'Alt' === ( undefined !== this.props.item.icon ? this.props.item.icon : this.props.item.id ) ?
																		'active-radio ' :
																		'' ) + 'svg-icon-' + this.props.item.id + 'Alt' }
																onClick={ () => {
																	this.props.onChangeIcon( this.props.item.id + 'Alt', this.props.index );
																} }
															>
																<span className="kadence-radio-icon">
																	{ ContactIcons[ this.props.item.id + 'Alt' ] }
																</span>
															</Button>
														) }
														{ ContactIcons[ this.props.item.id + 'Alt2' ] && (
															<Button
																isTertiary
																className={ ( this.props.item.id + 'Alt2' === ( undefined !== this.props.item.icon ? this.props.item.icon : this.props.item.id ) ?
																		'active-radio ' :
																		'' ) + 'svg-icon-' + this.props.item.id + 'Alt2' }
																onClick={ () => {
																	this.props.onChangeIcon( this.props.item.id + 'Alt2', this.props.index );
																} }
															>
																<span className="kadence-radio-icon">
																	{ ContactIcons[ this.props.item.id + 'Alt2' ] }
																</span>
															</Button>
														)}
													</ButtonGroup>
												</Fragment>
											);
										}
									}
									return <div>{ tabout }</div>;
								}
							}
						</TabPanel>
						<TextControl
							label={ __( 'Item Label', 'kadence' ) }
							value={ this.props.item.label ? this.props.item.label : '' }
							onChange={ ( value ) => {
								this.props.onChangeLabel( value, this.props.index );
							} }
						/>
						<TextControl
							label={ __( 'Item Link', 'kadence' ) }
							value={ this.props.item.link ? this.props.item.link : '' }
							onChange={ ( value ) => {
								this.props.onChangeLink( value, this.props.index );
							} }
						/>
						<Button
							className="kadence-sorter-item-remove"
							isDestructive
							onClick={ () => {
								this.props.removeItem( this.props.index );
							} }
						>
							{ __( 'Remove Item', 'kadence' ) }
							<Dashicon icon="no-alt"/>
						</Button>
					</div>
				) }
			</div>
		);
	}
}
export default ItemComponent;
