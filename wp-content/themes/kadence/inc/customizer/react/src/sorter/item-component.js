/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import capitalizeFirstLetter from '../common/capitalize-first.js';
import { __ } from '@wordpress/i18n';
import Icons from '../common/icons.js';
const { MediaUpload } = wp.blockEditor;
const { ButtonGroup, Dashicon, Tooltip, TextControl, Button, TabPanel, ToggleControl, RangeControl, Placeholder } = wp.components;

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
				<div className={ `kadence-sorter-item-panel-header ${ ( this.props.item.enabled ? 'panel-item-is-visible' : 'panel-item-is-hidden' ) }` }>
					<Tooltip text={ __( 'Toggle Item Visibility', 'kadence' ) }>
						<Button
							className={ `kadence-sorter-visiblity ${ ( this.props.item.enabled ? 'item-is-visible' : 'item-is-hidden' ) }`}
							onClick={ () => {
								this.props.onItemChange( { enabled: ( this.props.item.enabled ? false : true ) }, this.props.index );
							} }
						>
							<Dashicon icon={ ( this.props.item.enabled ? 'visibility' : 'hidden' ) } />
						</Button>
					</Tooltip>
					<span className="kadence-sorter-title">
						{ capitalizeFirstLetter( this.props.item.id ) }
					</span>
					{ 'title' !== this.props.item.id && (
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
					) }
				</div>
				{ this.state.open && (
					<div className="kadence-sorter-item-panel-content">
						{ undefined !== this.props.item.divider && (
							<div class="components-base-control">
								<span className="sorter-control-title">{ __( 'Choose a Divider', 'kadence' ) }</span>
								<ButtonGroup className="kadence-radio-container-control">
									{ Object.keys( this.props.controlParams.dividers ).map( ( item ) => {
										return (
											<Fragment>
												<Button
													isTertiary
													className={ ( item === this.props.item.divider ?
													'active-radio ' :
													'' ) + 'radio-btn-' + item }
													onClick={ () => {
														this.props.onItemChange( { divider: item }, this.props.index );
													} }
												>
													{ this.props.controlParams.dividers[ item ].icon && (
														<span className="kadence-radio-icon">
															{ Icons[ this.props.controlParams.dividers[ item ].icon ] }
														</span>
													) }
													{ this.props.controlParams.dividers[ item ].name && (
														this.props.controlParams.dividers[ item ].name
													) }
												</Button>
											</Fragment>
										);
									} ) }
								</ButtonGroup>
							</div>
						) }
						{ undefined !== this.props.item.author && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Author?', 'kadence' ) }
									checked={ this.props.item.author ? this.props.item.author : this.props.item.author }
									onChange={ ( value ) => {
										this.props.onItemChange( { author: value }, this.props.index );
									} }
								/>
								{ this.props.item.author && (
									<Fragment>
										{ undefined !== this.props.item.authorImage && (
											<ToggleControl
												label={ __( 'Show Author Image?', 'kadence' ) }
												checked={ this.props.item.authorImage ? this.props.item.authorImage : this.props.item.authorImage }
												onChange={ ( value ) => {
													this.props.onItemChange( { authorImage: value }, this.props.index );
												} }
											/>
										) }
										{ undefined !== this.props.item.authorLabel && (
											<div className="meta-label-control">
												<span className="sorter-control-title">{ __( 'Author Label', 'kadence' ) }</span>
												<div className={ `meta-label-input-control ${ ( this.props.item.authorEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
													<Tooltip text={ __( 'Toggle Label Visibility', 'kadence' ) }>
														<Button
															className={ `kadence-label-visiblity ${ ( this.props.item.authorEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
															onClick={ () => {
																this.props.onItemChange( { authorEnableLabel: ( this.props.item.authorEnableLabel ? false : true ) }, this.props.index );
															} }
														>
															<Dashicon icon={ ( this.props.item.authorEnableLabel ? 'visibility' : 'hidden' ) } />
														</Button>
													</Tooltip>
													<TextControl
														label=""
														placeholder={ __( 'By', 'kadence' ) }
														value={ this.props.item.authorLabel ? this.props.item.authorLabel : this.props.item.authorLabel }
														onChange={ ( value ) => {
															this.props.onItemChange( { authorLabel: value }, this.props.index );
														} }
													/>
												</div>
											</div>
										) }
									</Fragment>
								) }
							</div>
						) }
						{ undefined !== this.props.item.date && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Date?', 'kadence' ) }
									checked={ this.props.item.date ? this.props.item.date : this.props.item.date }
									onChange={ ( value ) => {
										this.props.onItemChange( { date: value }, this.props.index );
									} }
								/>
								{ this.props.item.date && (
									<ToggleControl
										label={ __( 'Show Time?', 'kadence' ) }
										checked={ this.props.item.dateTime ? this.props.item.dateTime : false }
										onChange={ ( value ) => {
											this.props.onItemChange( { dateTime: value }, this.props.index );
										} }
									/>
								) }
								{ undefined !== this.props.item.dateLabel && this.props.item.date && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Date Label', 'kadence' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.props.item.dateEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'kadence' ) }>
												<Button
													className={ `kadence-label-visiblity ${ ( this.props.item.dateEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.props.onItemChange( { dateEnableLabel: ( this.props.item.dateEnableLabel ? false : true ) }, this.props.index );
													} }
												>
													<Dashicon icon={ ( this.props.item.dateEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Posted on', 'kadence' ) }
												value={ this.props.item.dateLabel ? this.props.item.dateLabel : this.props.item.dateLabel }
												onChange={ ( value ) => {
													this.props.onItemChange( { dateLabel: value }, this.props.index );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.props.item.dateUpdated && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __(  'Show Last Updated Date?', 'kadence' ) }
									checked={ this.props.item.dateUpdated ? this.props.item.dateUpdated : this.props.item.dateUpdated }
									onChange={ ( value ) => {
										this.props.onItemChange( { dateUpdated: value }, this.props.index );
									} }
								/>
								{ undefined !== this.props.item.dateUpdatedLabel && this.props.item.dateUpdated && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Updated Date Label', 'kadence' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.props.item.dateUpdatedEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'kadence' ) }>
												<Button
													className={ `kadence-label-visiblity ${ ( this.props.item.dateUpdatedEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.props.onItemChange( { dateUpdatedEnableLabel: ( this.props.item.dateUpdatedEnableLabel ? false : true ) }, this.props.index );
													} }
												>
													<Dashicon icon={ ( this.props.item.dateUpdatedEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Updated on', 'kadence' ) }
												value={ this.props.item.dateUpdatedLabel ? this.props.item.dateUpdatedLabel : this.props.item.dateUpdatedLabel }
												onChange={ ( value ) => {
													this.props.onItemChange( { dateUpdatedLabel: value }, this.props.index );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.props.item.categories && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __( 'Show Categories?', 'kadence' ) }
									checked={ this.props.item.categories ? this.props.item.categories : this.props.item.categories }
									onChange={ ( value ) => {
										this.props.onItemChange( { categories: value }, this.props.index );
									} }
								/>
								{ undefined !== this.props.item.categoriesLabel && this.props.item.categories && this.props.item.metaLabel && (
									<div className="meta-label-control">
										<span className="sorter-control-title">{ __( 'Categories Label', 'kadence' ) }</span>
										<div className={ `meta-label-input-control ${ ( this.props.item.categoriesEnableLabel ? 'label-is-visible' : 'label-is-hidden' ) }` }>
											<Tooltip text={ __( 'Toggle Label Visibility', 'kadence' ) }>
												<Button
													className={ `kadence-label-visiblity ${ ( this.props.item.categoriesEnableLabel ? 'item-is-visible' : 'item-is-hidden' ) }`}
													onClick={ () => {
														this.props.onItemChange( { categoriesEnableLabel: ( this.props.item.categoriesEnableLabel ? false : true ) }, this.props.index );
													} }
												>
													<Dashicon icon={ ( this.props.item.categoriesEnableLabel ? 'visibility' : 'hidden' ) } />
												</Button>
											</Tooltip>
											<TextControl
												label=""
												placeholder={ __( 'Posted in', 'kadence' ) }
												value={ this.props.item.categoriesLabel ? this.props.item.categoriesLabel : this.props.item.categoriesLabel }
												onChange={ ( value ) => {
													this.props.onItemChange( { categoriesLabel: value }, this.props.index );
												} }
											/>
										</div>
									</div>
								) }
							</div>
						) }
						{ undefined !== this.props.item.comments && (
							<div className="sorter-sub-option">
								<ToggleControl
									label={ __(  'Show Comments?', 'kadence' ) }
									checked={ this.props.item.comments ? this.props.item.comments : this.props.item.comments }
									onChange={ ( value ) => {
										this.props.onItemChange( { comments: value }, this.props.index );
									} }
								/>
							</div>
						) }
					</div>
				) }
			</div>
		);
	}
}
export default ItemComponent;
