/**
 * Customizes the taxonomy editor, to set the new Lesson label.
 *
 * This used to be customized in the block editor, but that became unstable.  It is easier to
 * set the default label to what we want in the block editor, then customize the taxonomy admin page.
 * Relies on the markup of the taxonomy editor page.
 */
document.addEventListener('DOMContentLoaded', function () {
	// The block editor uses the native "new tag" label, so we reset it here to a more general meaning.
	var labelText = 'Create Lesson';

	// Rename the form label.
	var taxForm = document.querySelector("#addtag");
	var labelElement = taxForm.parentElement.getElementsByTagName('h2')[0];
	labelElement.innerHTML = labelText;

	// Rename the submit button.
	document.querySelector("#addtag #submit").value = labelText;

}, false);
