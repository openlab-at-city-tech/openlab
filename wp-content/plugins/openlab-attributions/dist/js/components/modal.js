/**
 * WordPress dependencies
 */
import { Component, RawHTML } from '@wordpress/element';
import { Button, Modal } from '@wordpress/components';

/**
 * Internal dependencies
 */
import TextControl from './text-control';
import SelectControl from './select-control';
import PluginAttribution from './plugin-attribution';
import formatAttribution from '../utils/format-attribution';
import help from '../utils/help';

const licenses = window.attrLicenses || [];

class AttributionModal extends Component {
	constructor( props ) {
		super( props );

		this.handleChange = this.handleChange.bind( this );
		this.handleSubmit = this.handleSubmit.bind( this );

		this.state = props.item;
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

		const title = ( modalType === 'add' ) ? 'Add Attribution' : 'Update Attribution';

		return (
			<Modal
				shouldCloseOnEsc={ false }
				shouldCloseOnClickOutside={ false }
				title={ title }
				onRequestClose={ onClose }
				className="component-attributions-modal"
			>
				<form onSubmit={ this.handleSubmit } >
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
							/>
							<TextControl
								label="URL"
								id="titleUrl"
								name="titleUrl"
								className="inline"
								value={ this.state.titleUrl }
								onChange={ this.handleChange }
								placeholder="URL of the item title"
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
							/>
							<TextControl
								label="URL"
								id="authorUrl"
								name="authorUrl"
								className="inline"
								value={ this.state.authorUrl }
								onChange={ this.handleChange }
								placeholder="URL of the author page"
							/>
						</div>
						<div className="col">
							<TextControl
								label="Derivative Work"
								id="derivative"
								name="derivative"
								value={ this.state.derivative }
								help={ help.derivative }
								onChange={ this.handleChange }
								placeholder="URL of original work"
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
							/>
							<TextControl
								label="URL"
								id="publisherUrl"
								name="publisherUrl"
								className="inline"
								value={ this.state.publisherUrl }
								onChange={ this.handleChange }
								placeholder="URL of the organization or publisher"
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
							/>
							<TextControl
								label="URL"
								id="projectUrl"
								name="projectUrl"
								className="inline"
								value={ this.state.projectUrl }
								onChange={ this.handleChange }
								placeholder="URL of the project"
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

					<span className="attribution-preview__title">Attribution Preview</span>
					<div className="attribution-preview__body">
						<RawHTML>{ formatAttribution( { ...this.state }, licenses ) }</RawHTML>
					</div>
					<PluginAttribution />
					<div className="component-modal__footer">
						<Button isDestructive isLink onClick={ onClose }>Cancel</Button>
						<Button isLarge isPrimary type="submit">{ title }</Button>
					</div>
				</form>
			</Modal>
		);
	}
}

export default AttributionModal;
