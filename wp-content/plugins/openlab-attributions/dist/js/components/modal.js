/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { Button, Modal, Notice } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';

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

	render() {
		if ( ! this.props.isOpen ) {
			return null;
		}

		const { onClose, modalType } = this.props;

		const title =
			modalType === 'add' ? 'Add Attribution' : 'Update Attribution';

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
								label="Title"
								id="title"
								name="title"
								value={ this.state.title }
								help={ help.title }
								onChange={ this.handleChange }
								placeholder="Item Title"
								required={ !! this.state.titleUrl }
							/>
							<TextControl
								label="URL"
								id="titleUrl"
								name="titleUrl"
								value={ this.state.titleUrl }
								onChange={ this.handleChange }
								placeholder="URL of the item"
								isInline
							/>
						</div>
						<div className="col">
							<SelectControl
								label="License"
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
								label="Author Name"
								id="authorName"
								name="authorName"
								value={ this.state.authorName }
								help={ help.authorName }
								onChange={ this.handleChange }
								placeholder="Author Name"
								required={ !! this.state.authorUrl }
							/>
							<TextControl
								label="URL"
								id="authorUrl"
								name="authorUrl"
								value={ this.state.authorUrl }
								onChange={ this.handleChange }
								placeholder="URL of the author"
								isInline
							/>
						</div>
						<div className="col adapted-from">
							<TextControl
								label="Adapted From"
								id="derivative"
								name="derivative"
								value={ this.state.derivative }
								help={ help.derivative }
								onChange={ this.handleChange }
								placeholder="URL of original work"
							/>
							<TextControl
								label="Title"
								id="adaptedTitle"
								name="adaptedTitle"
								value={ this.state.adaptedTitle }
								onChange={ this.handleChange }
								placeholder="Item Title"
								required={ !! this.state.derivative }
								isInline
							/>
							<TextControl
								label="Author"
								id="adaptedAuthor"
								name="adaptedAuthor"
								value={ this.state.adaptedAuthor }
								onChange={ this.handleChange }
								placeholder="Author Name"
								isInline
							/>
							<SelectControl
								label="License"
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
								label="Organization / Publisher"
								id="publisher"
								name="publisher"
								value={ this.state.publisher }
								help={ help.publisher }
								onChange={ this.handleChange }
								placeholder="Name of organization or publisher"
								required={ !! this.state.publisherUrl }
							/>
							<TextControl
								label="URL"
								id="publisherUrl"
								name="publisherUrl"
								value={ this.state.publisherUrl }
								onChange={ this.handleChange }
								placeholder="URL of the organization or publisher"
								isInline
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label="Project"
								id="project"
								name="project"
								value={ this.state.project }
								help={ help.project }
								onChange={ this.handleChange }
								placeholder="Name of project"
								required={ !! this.state.projectUrl }
							/>
							<TextControl
								label="URL"
								id="projectUrl"
								name="projectUrl"
								value={ this.state.projectUrl }
								onChange={ this.handleChange }
								placeholder="URL of the project"
								isInline
							/>
						</div>
					</div>
					<div className="form-row">
						<div className="col">
							<TextControl
								label="Date Published"
								id="datePublished"
								name="datePublished"
								value={ this.state.datePublished }
								help={ help.datePublished }
								onChange={ this.handleChange }
								placeholder="Date item was published"
							/>
						</div>
					</div>

					<span className="attribution-preview__title">
						Attribution Preview
					</span>
					<RichText
						className="attribution-preview__body"
						tagName="div"
						value={ isEdited ? this.state.content : preview }
						allowedFormats={ [] }
						onChange={ ( content ) => {
							this.setState( { editedContent: true, content } );
						} }
					/>
					{ isEdited && (
						<Notice
							className="attribution-preview__notice"
							status="warning"
							isDismissible={ false }
						>
							You have edited this text. You can no longer make
							changes using the fields above. All additional
							changes must be made manually.
							<Button
								isLink
								onClick={ () =>
									this.setState( {
										editedContent: false,
										content: '',
									} )
								}
							>
								Discard changes and revert to suggested
								attribution.
							</Button>
						</Notice>
					) }
					<PluginAttribution />
					<div className="component-modal__footer">
						<Button isDestructive isLink onClick={ onClose }>
							Cancel
						</Button>
						<Button isPrimary type="submit">
							{ title }
						</Button>
					</div>
				</form>
			</Modal>
		);
	}
}

export default AttributionModal;
