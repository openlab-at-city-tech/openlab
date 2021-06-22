<?php
// ajax calls
function watu_submit() {	
	require_once(WATU_PATH."/controllers/show_exam.php");
}

function watu_already_rated() {
	update_option('watu_rated', 1);
}