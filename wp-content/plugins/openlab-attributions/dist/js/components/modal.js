/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import {
	Button,
	Modal,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import formatAttribution from '../utils/format-attribution';

const licenses = Object.values( window.attrLicenses );

export default class AttributionModal extends Component {
	constructor( props ) {
		super( props );

		this.handleChange = this.handleChange.bind( this );

		this.initalState = {
			title: '',
			titleUrl: '',
			author: '',
			authorUrl: '',
			publisher: '',
			publisherUrl: '',
			annotation: '',
			project: '',
			projectUrl: '',
			license: '',
			year: '',
			derivative: '',
		};
		this.state = this.initalState;
	}

	handleChange( event ) {
		const target = event.target;
		const value = target.value;
		const name = target.name;

		this.setState( {
			[ name ]: value,
		} );
	}

	render() {
		if ( ! this.props.isOpen ) {
			return null;
		}

		const { title, onClose, onSubmit } = this.props;

		const selected = window.getSelection().toString();
		const preview = {
			__html: formatAttribution( { ...this.state }, licenses ),
		};

		return (
			<Modal
				shouldCloseOnEsc={ false }
				shouldCloseOnClickOutside={ false }
				title={ title }
				onRequestClose={ onClose }
				className="ol-attributions-modal"
			>
				<form
					onSubmit={ ( e ) => {
						e.preventDefault();
						onSubmit( { ...this.state } );
						this.setState( this.initalState );
					} }
				>
					<div className="form-row">
						<div className="form-group col">
							<div className="form-group">
								<label htmlFor="title">Title</label>
								<input
									type="text"
									className="form-control"
									id="title"
									name="title"
									value={ this.state.title }
									onChange={ this.handleChange } />
								<label htmlFor="titleUrl">URL</label>
								<input
									type="text"
									className="form-control"
									id="titleUrl"
									name="titleUrl"
									value={ this.state.titleUrl }
									onChange={ this.handleChange } />
							</div>
							<div className="form-group">
								<label htmlFor="author">Author Name</label>
								<input
									type="text"
									className="form-control"
									id="author"
									name="author"
									value={ this.state.authorName }
									onChange={ this.handleChange } />
								<label htmlFor="authorUrl">URL</label>
								<input
									type="text"
									className="form-control"
									id="authorUrl"
									name="authorUrl"
									value={ this.state.authorUrl }
									onChange={ this.handleChange } />
							</div>
						</div>
						<div className="form-group col">
							<label htmlFor="selectedText">Selected</label>
							<textarea
								className="form-control"
								id="selectedText"
								rows="9"
								value={ selected }
								disabled />
						</div>
					</div>
					<div className="form-row">
						<div className="form-group col">
							<label htmlFor="publisher">Organization / Publisher</label>
							<input
								type="text"
								className="form-control"
								id="publisher"
								name="publisher"
								value={ this.state.publisher }
								onChange={ this.handleChange } />
							<label htmlFor="publisherUrl">URL</label>
							<input
								type="text"
								className="form-control"
								id="publisherUrl"
								name="publisherUrl"
								value={ this.state.publisherUrl }
								onChange={ this.handleChange } />
						</div>
						<div className="form-group col">
							<label htmlFor="annotation">Annotation <em>(Added to bibliography)</em></label>
							<textarea
								className="form-control"
								id="annotation"
								name="annotation"
								rows="4"
								value={ this.state.annotation }
								onChange={ this.handleChange } />
						</div>
					</div>
					<div className="form-row">
						<div className="form-group col">
							<label htmlFor="project">Project</label>
							<input
								type="text"
								className="form-control"
								id="project"
								name="project"
								value={ this.state.project }
								onChange={ this.handleChange } />
							<label htmlFor="projectUrl">URL</label>
							<input
								type="text"
								className="form-control"
								id="projectUrl"
								name="projectUrl"
								value={ this.state.projectUrl }
								onChange={ this.handleChange } />
						</div>
						<div className="form-group col">
							<label htmlFor="license">License</label>
							<select
								id="license"
								className="form-control"
								name="license"
								onBlur={ this.handleChange }
							>
								<option key="0" value="">Choose...</option>
								{ licenses.map( ( option, index ) =>
									<option
										key={ `${ option.label }-${ index }` }
										value={ option.label }
									>
										{ option.label }
									</option>
								) }
							</select>
						</div>
					</div>
					<div className="form-row">
						<div className="form-group col">
							<label htmlFor="year">Date Published</label>
							<input
								type="text"
								className="form-control"
								id="year"
								name="year"
								value={ this.state.year }
								onChange={ this.handleChange } />
						</div>
						<div className="form-group col">
							<label htmlFor="derivative">Derivative Work</label>
							<input
								type="text"
								className="form-control"
								id="derivative"
								name="derivative"
								placeholder="URL of original work"
								value={ this.state.derivative }
								onChange={ this.handleChange } />
						</div>
					</div>

					<span className="attribution-preview__title">Attribution Preview</span>
					<div className="attribution-preview__body" dangerouslySetInnerHTML={ preview } />
					<div className="component-modal__footer">
						<Button isDestructive isLink onClick={ onClose }>Cancel</Button>
						<Button isLarge isPrimary type="submit">Add Attribution</Button>
					</div>
				</form>
			</Modal>
		);
	}
}
