// Version 1.0 - original version

function getip( json ) {
	if ( true === document.body.contains( document.forms['s2form'] ) ) {
		var ip = document.getElementsByName( 'ip' );
		for ( i = 0; i < ip.length; i++ ) {
			if ( 's2form' === ip[i].parentElement.name ) {
				ip[i].value = json.ip;
			}
		}
	}
}