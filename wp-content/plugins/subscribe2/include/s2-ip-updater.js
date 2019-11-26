/* exported getip */
// Version 1.0 - original version
// Version 1.1 - eslinted and fixed for Widget form name change

function getip( json ) {
	var ip, i, l;
	if ( true === document.body.contains( document.forms.s2form ) || true === document.body.contains( document.forms.s2formwidget ) ) {
		ip = document.getElementsByName( 'ip' );
		l  = ip.length;
		for ( i = 0; i < l; i++ ) {
			if ( 's2form' === ip[i].parentElement.name ) {
				ip[i].value = json.ip;
			}
		}
	}
}
