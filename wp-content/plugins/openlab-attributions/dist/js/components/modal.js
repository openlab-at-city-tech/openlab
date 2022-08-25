import ContentEditable from 'react-contenteditable';

/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { Button, Modal, Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import TextControl from './text-control';
import SelectControl from './select-control';
import PluginAttribution from './plugin-attribution';
import { formatAttribution } from '../utils/format';
import help from '../utils/help';

const licenses = window.attrLicenses || [];

class AttributionModal extends Component {
	constructor( props ) {
		super( props );

		this.handleChange = this.handleChange.bind( this );
		this.handleSubmit = this.handleSubmit.bind( this );
		this.discardChanges = this.discardChanges.bind( this );

		this.state = {
			editedContent: false,
			adaptedTitle: '',
			adaptedAuthor: '',
			adaptedLicense: '',
			content: '',
			...props.item,
		};
	}

	handleChange( event ) {
		const target = event.target;
		const value = target.value;
		const name = target.name;

		this.setState( {
			[ name ]: value,
		} );
	}

	handleSubmit( event ) {
		event.preventDefault();

		// Handle updating or adding item.
		if ( this.props.modalType === 'add' ) {
			this.props.addItem( { ...this.state } );
		} else {
			this.props.updateItem( { ...this.state } );
		}

		this.props.onClose();

		// Reset state.
		this.setState( this.props.item );
	}

	discardChanges() {
		this.setState( {
			editedContent: false,
			content: '',
		} );
	}

	render() {
		if ( ! this.props.isOpen ) {
			return null;
		}

		const { onClose, modalType } = this.props;

		const title =
			modalType === 'add' ? __( 'Add Attribution', 'openlab-attributions' ) : __( 'Update Attribution', 'openlab-attributions' );

		const isEdited = this.state.editedContent || this.state.content;
		const preview = formatAttribution( { ...this.state } );

		return (
			<Modal
				shouldCloseOnEsc={ false }
				shouldCloseOnClickOutside={ false }
				title={ title }
				onRequestClose={ onClose }
				className="component-attributions-modal"
			>
				<form onSubmit={ this.handleSubmit }>
					<div className="form-row">
						<div className="col">
							<TextControl
								label={ __( "Title", 'openlab-attributions' ) }
								id="title"
								name="title"
								value={ this.state.title }
								help={ help.title }
								onChange={ this.handleChange }
								placeholder={ __( "Item Title", 'openlab-attributions' ) }
								required={ !! this.state.titleUrl }
							/>
							<TextControl
								label={ __( "URL", 'openlab-attributions' ) }
								id="titleUrl"
								name="titleUrl"
								value={ this.state.titleUrl }
								onChange={ this.handleChange }
								placeholder={ __( "URL of the item", 'openlab-attributions' ) }
								isInline
							/>
						</div>
						<div className="col">
							<SelectControl
								label={ __( "License", 'openlab-attributions' ) }
								id="license"
								name="license"
								value={ this.state.license }
								help={ help.license }
								options={ licenses }
								onChange={ this.handleChange }
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label={ __( "Author Name", 'openlab-attributions' ) }
								id="authorName"
								name="authorName"
								value={ this.state.authorName }
								help={ help.authorName }
								onChange={ this.handleChange }
								placeholder={ __( "Author Name", 'openlab-attributions' ) }
								required={ !! this.state.authorUrl }
							/>
							<TextControl
								label={ __( "URL", 'openlab-attributions' ) }
								id="authorUrl"
								name="authorUrl"
								value={ this.state.authorUrl }
								onChange={ this.handleChange }
								placeholder={ __( "URL of the author", 'openlab-attributions' ) }
								isInline
							/>
						</div>
						<div className="col adapted-from">
							<TextControl
								label={ __( "Adapted From", 'openlab-attributions' ) }
								id="derivative"
								name="derivative"
								value={ this.state.derivative }
								help={ help.derivative }
								onChange={ this.handleChange }
								placeholder={ __( "URL of original work", 'openlab-attributions' ) }
							/>
							<TextControl
								label={ __( "Title", 'openlab-attributions' ) }
								id="adaptedTitle"
								name="adaptedTitle"
								value={ this.state.adaptedTitle }
								onChange={ this.handleChange }
								placeholder={ __( "Item Title", 'openlab-attributions' ) }
								required={ !! this.state.derivative }
								isInline
							/>
							<TextControl
								label={ __( "Author", 'openlab-attributions' ) }
								id="adaptedAuthor"
								name="adaptedAuthor"
								value={ this.state.adaptedAuthor }
								onChange={ this.handleChange }
								placeholder={ __( "Author Name", 'openlab-attributions' ) }
								isInline
							/>
							<SelectControl
								label={ __( "License", 'openlab-attributions' ) }
								id="adaptedLicense"
								name="adaptedLicense"
								value={ this.state.adaptedLicense }
								options={ licenses }
								onChange={ this.handleChange }
								required={
									!! this.state.derivative ||
									this.state.adaptedTitle
								}
								isInline
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label={ __( "Organization / Publisher", 'openlab-attributions' ) }
								id="publisher"
								name="publisher"
								value={ this.state.publisher }
								help={ help.publisher }
								onChange={ this.handleChange }
								placeholder={ __( "Name of organization or publisher", 'openlab-attributions' ) }
								required={ !! this.state.publisherUrl }
							/>
							<TextControl
								label={ __( "URL", 'openlab-attributions' ) }
								id="publisherUrl"
								name="publisherUrl"
								value={ this.state.publisherUrl }
								onChange={ this.handleChange }
								placeholder={ __( "URL of the organization or publisher", 'openlab-attributions' ) }
								isInline
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label={ __( "Project", 'openlab-attributions' ) }
								id="project"
								name="project"
								value={ this.state.project }
								help={ help.project }
								onChange={ this.handleChange }
								placeholder={ __( "Name of project", 'openlab-attributions' ) }
								required={ !! this.state.projectUrl }
							/>
							<TextControl
								label={ __( "URL", 'openlab-attributions' ) }
								id="projectUrl"
								name="projectUrl"
								value={ this.state.projectUrl }
								onChange={ this.handleChange }
								placeholder={ __( "URL of the project", 'openlab-attributions' ) }
								isInline
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label={ __( "Date Published", 'openlab-attributions' ) }
								id="datePublished"
								name="datePublished"
								value={ this.state.datePublished }
								help={ help.datePublished }
								onChange={ this.handleChange }
								placeholder={ __( "Date item was published", 'openlab-attributions' ) }
							/>
						</div>
					</div>

					<span className="attribution-preview__title">
						Attribution Preview
					</span>
					<ContentEditable
						className="attribution-preview__body"
						tagName="div"
						html={ isEdited ? this.state.content : preview }
						onChange={ ( event ) => {
							this.setState( {
								editedContent: true,
								content: event.target.value,
							} );
						} }
					/>
					{ isEdited && (
						<Notice
							className="attribution-preview__notice"
							status="warning"
							isDismissible={ false }
						>
							{ __( 'You have edited this text. You can no longer make changes using the fields above. All additional changes must be made manually.', 'openlab-attributions' ) }
							<Button isLink onClick={ this.discardChanges }>
								{ __( 'Discard changes and revert to suggested attribution.', 'openlab-attributions' ) }
							</Button>
						</Notice>
					) }
					<div className="component-modal__footer">
						<Button isDestructive isLink onClick={ onClose }>
							{ __( 'Cancel', 'openlab-attributions' ) }
						</Button>
						<Button isPrimary type="submit">
							{ title }
						</Button>
					</div>
					<PluginAttribution />
				</form>
			</Modal>
		);
	}
}

export default AttributionModal;
