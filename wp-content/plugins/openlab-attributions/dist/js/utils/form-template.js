const formTmpl = ( selected, licenses ) => {
	return `
		<form id="attribution-builder" name="attrShortcode">
			<div class="form-row">
				<div class="form-group col">
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" id="title" name="title">
						<label for="titleUrl">URL</label>
						<input type="text" class="form-control" id="titleUrl" name="titleUrl">
					</div>
					<div class="form-group">
						<label for="author">Author Name</label>
						<input type="text" class="form-control" id="author" name="author">
						<label for="authorUrl">URL</label>
						<input type="text" class="form-control" id="authorUrl" name="authorUrl">
					</div>
				</div>
				<div class="form-group col">
					<label for="selectedText">Selected</label>
					<textarea class="form-control" id="selectedText" rows="9" disabled>${ selected }</textarea>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col">
					<label for="publisher">Organization / Publisher</label>
					<input type="text" class="form-control" id="publisher" name="publisher">
					<label for="publisherUrl">URL</label>
					<input type="text" class="form-control" id="publisherUrl" name="publisherUrl">
				</div>
				<div class="form-group col">
					<label for="annotation">Annotation <em>(Added to bibliography)</em></label>
					<textarea class="form-control" id="annotation" name="annotation" rows="4"></textarea>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col">
					<label for="project">Project</label>
					<input type="text" class="form-control" id="project" name="project">
					<label for="projectUrl">URL</label>
					<input type="text" class="form-control" id="projectUrl" name="projectUrl">
				</div>
				<div class="form-group col">
					<label for="license">License</label>
					<select class="form-control" id="license" name="license">
						<option value="">Choose...</option>
						${ licenses.map( ( license ) => `<option>${ license.label }</option>` ).join( '' ) }
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col">
					<label for="year">Date Published</label>
					<input type="text" class="form-control" id="year" name="year">
				</div>
				<div class="form-group col">
					<label for="derivative">Derivative Work</label>
					<input type="text" class="form-control" id="derivative" name="derivative" placeholder="URL of original work">
				</div>
			</div>
			<span class="attribution-preview__title">Attribution Preview</span>
			<div id="attribution-preview" class="attribution-preview__body"></div>
		</form>
	`;
};

export default formTmpl;
