<?php
$links = [
	[
		'title' => 'The OpenLab for Students',
		'url'   => 'https://openlab.citytech.cuny.edu/openlabforstudents/',
	],
	[
		'title' => 'Getting Started',
		'url'   => 'https://openlab.citytech.cuny.edu/blog/help/help-category/getting-started/',
	],
	[
		'title' => 'Adding a Comment',
		'url'   => 'https://openlab.citytech.cuny.edu/blog/help/commenting-on-a-site/',
	],
	[
		'title' => 'Writing a Post',
		'url'   => 'https://openlab.citytech.cuny.edu/blog/help/writing-a-post-block-editor/',
	],
];
?>

<div class="openlab-block-openlab-help-content">
	<ul>
		<?php foreach ( $links as $link ) : ?>
			<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
