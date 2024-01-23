<?php
/**
 * @var string $message
 */
// The following CSS *should* be placed in a shared CSS file, but that will require making several more updates
// to ensure it loads on "new" and legacy pages; it's just far easier to embed it here.
?>
<style>
	#ngg-admin-marketing-header-banner {
		border: none;
		background: #9fbb1a;
		color: white;
		margin-left: -20px;
		display: flex;
		align-content: center;
		justify-content: center;
	}

	#ngg-admin-marketing-header-banner p {
		font-size: 1.2em;
		margin: 10px 20px 10px 20px;
	}

	#ngg-admin-marketing-header-banner a {
		font-weight: bold;
		color: white;
	}

	@media only screen and (max-width: 740px) {
		#ngg-admin-marketing-header-banner {
			margin-left: -10px;
		}
	}

	@media only screen and (max-width: 600px) {
		#ngg-admin-marketing-header-banner {
			padding-top: 50px;
		}
	}
</style>
<div id="ngg-admin-marketing-header-banner">
	<p><?php print $message; ?></p>
</div>