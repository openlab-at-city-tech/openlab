(function(){
	var createCookie = function(name, value, days) {
			var expires;
		if (days) {
				var date = new Date();
				date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
				expires = "; expires=" + date.toGMTString();
		} else {
				expires = "";
		}
			document.cookie = name + "=" + value + expires + "; path=/";
	}

	var getCookie = function(c_name) {
		if (document.cookie.length > 0) {
				c_start = document.cookie.indexOf( c_name + "=" );
			if (c_start != -1) {
					c_start = c_start + c_name.length + 1;
					c_end   = document.cookie.indexOf( ";", c_start );
				if (c_end == -1) {
					c_end = document.cookie.length;
				}
					return unescape( document.cookie.substring( c_start, c_end ) );
			}
		}
			return "";
	}

	document.addEventListener(
		'DOMContentLoaded',
		function(event){
			var qs      = document.location.search;
			var isLogin = -1 !== document.body.className.indexOf( 'login' );

			// Setter
			if ( isLogin && -1 !== qs.indexOf( 'redirect_to' ) ) {
				var matches = qs.match( /redirect_to=([^\&]+)/ );
				var problemId
				if ( matches.length ) {
					var parts = matches[1].split( '%3A' );
					parts.forEach(
						function(part){
							if ( 0 === part.indexOf( 'problemId' ) ) {
								var decoded          = decodeURIComponent( part );
								var problemIdMatches = decoded.match( /problemId=([^\:]+)/ )
								if ( problemIdMatches.length ) {
									problemId = problemIdMatches[1]
								}
							}
						}
					)

					// If there's a hash, store it.
					if ( 0 === document.location.hash.indexOf( '#:problemId=' ) ) {
						createCookie( 'webwork-redirect-to', document.location.hash, 1 );
					}
				}

				if ( problemId ) {
					createCookie( 'webwork-problem-id', problemId, 1 );
				}
			}

			// Redirector
			if ( -1 !== document.location.search.indexOf( 'post_data_key' ) && -1 === document.body.className.indexOf( 'login' ) ) {
				var storedProblemId = getCookie( 'webwork-problem-id' );
				if ( storedProblemId ) {
					createCookie( 'webwork-problem-id', '', -1 );
					var newLocation        = document.location.href;
					newLocation           += '#:problemId=' + storedProblemId;
					document.location.href = newLocation
				}
			}

			if ( -1 === document.body.className.indexOf( 'login' ) ) {
				var storedHash = getCookie( 'webwork-redirect-to' );
				if ( storedHash ) {
					createCookie( 'webwork-redirect-to', '', -1 );
					var newLocation        = document.location.href;
					newLocation           += storedHash
					document.location.href = newLocation
					document.location.reload()
				}
			}
		}
	);

})();
