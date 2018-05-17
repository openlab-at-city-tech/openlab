<?php

if ( defined( 'OPENLAB_CACHE_ENGINE' ) ) {
	switch ( OPENLAB_CACHE_ENGINE ) {
		case 'memcached' :
			include __DIR__ . '/object-cache-memcached.php';
		break;

		case 'apc' :
			include __DIR__ . '/object-cache-apc.php';
		break;
	}
}
