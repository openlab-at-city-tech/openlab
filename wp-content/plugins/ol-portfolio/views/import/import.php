<?php
use const OpenLab\Portfolio\ROOT_FILE;

$args = [
	'action' => 'ol-portfolio-import',
	'id'     => $this->id,
];

$script_data = [
	'url' => add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php' ) ),
	'strings' => [
		'complete' => 'Import complete!',
	],
];

$url = plugins_url( 'assets/js/import.js', ROOT_FILE );
wp_enqueue_script( 'ol-portfolio-import', $url, [ 'jquery' ], '20190606', true );
wp_localize_script( 'ol-portfolio-import', 'ImportData', $script_data );
?>
<div class="wrap nosubsub">
	<h1>Import Progress</h1>
	<div id="import-status-message" class="notice notice-info"><p>Now importing.</p></div>
	<table id="import-log" class="widefat">
		<thead>
			<tr>
				<th>Type</th>
				<th>Message</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
