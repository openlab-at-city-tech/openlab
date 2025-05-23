ClassicEditor
	.create( document.querySelector( '.editor' ), {
		// Editor configuration.
	} )
	.then( editor => {
		window.editor = editor;
	} )
	.catch( handleSampleError );

function handleSampleError( error ) {
	const issueUrl = 'https://github.com/ckeditor/ckeditor5/issues';

	const message = [
		'Oops, something went wrong!',
<<<<<<< HEAD
		`Please, report the following error on ${ issueUrl } with the build id "9e9snf1zg4kp-d2cnyyo44cbm" and the error stack trace:`
=======
		`Please, report the following error on ${ issueUrl } with the build id "6qddn4k8urt0-nohdljl880ze" and the error stack trace:`
>>>>>>> JI-5157-CKEditor-in-wordpress
	].join( '\n' );

	console.error( message );
	console.error( error );
}
