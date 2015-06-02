<?php 

if (isset($_GET['target']) && preg_match('/^(.*)\?.*npg=(\d+)/', $_GET['target'], $m)) {
	$path = $m[1];
	$npg = $m[2];
} else {
	echo "ERROR: invalid target parameter";
	exit();
}

if (!headers_sent()) {
	setcookie('tfk_page_size', $npg, null, $path);
	echo "OK";
} else {
	echo "ERROR: cannot set cookie, headers already sent !";
}

?>
