( function () {
	var email = document.getElementById( 'email' );
	if ( ! email ) {
		return;
	}

	email.readOnly = true;

	var description = document.getElementById( 'email-description' );
	if ( description ) {
		description.remove();
	}

	var note = document.createElement( 'span' );
	note.className = 'description';
	note.id = 'email-description';
	note.textContent = 'Emails cannot be changed.';

	email.setAttribute( 'aria-describedby', 'email-description' );
	email.insertAdjacentElement( 'afterend', note );
	email.insertAdjacentText( 'afterend', ' ' );
} )();
