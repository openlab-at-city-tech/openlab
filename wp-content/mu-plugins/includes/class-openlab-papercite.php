<?php

/**
 * Force Papercite to use a writeable location for its local cache.
 *
 * See #1430.
 */
class OpenLab_Papercite extends Papercite {
  function getCached($url, $timeout = 3600, $sslverify = false) {
	// check if cached file exists
	$name = strtolower(preg_replace("@[/:]@","_",$url));

	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'] . '/papercite-cache';
	if ( ! file_exists( $dir ) ) {
	wp_mkdir_p( $dir );
	}
	$file = "$dir/$name.bib";

	// check if file date exceeds 60 minutes
	if (! (file_exists($file) && (filemtime($file) + $timeout > time())))  {
	  // Download URL and process
	  $req = wp_remote_get($url);
	  if (is_wp_error($req)) {
	$this->addMessage("Could not retrieve remote URL ".htmlentities($url). ": " . $req->get_error_message());
	return false;
	  }

	  $code = $req["response"]["code"];
	  if (!preg_match("#^2\d+$#", $code)) {
	$this->addMessage("Could not retrieve remote URL ".htmlentities($url). ": Page not found / {$code} error code");
	return false;
	  }

	  // Everything is OK: retrieve the body of the HTTP answer
	  $body = wp_remote_retrieve_body($req);
	  if ($body) {
	$f=fopen($file,"wb");
	fwrite($f,$body);
	fclose($f);
	  } else {
	$this->addMessage("Could not retrieve remote URL ".htmlentities($url));
	return NULL;
	  }


	  if (!$f) {
	$this->addMessage("Failed to write file " . $file . " - check directory permission according to your Web server privileges.");
	return false;
	  }
	}

	return array( $file, $dir . '/' . $name );
  }
}

