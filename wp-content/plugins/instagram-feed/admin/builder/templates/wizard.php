<div id="sbi-builder-app" class="sbi-fb-fs sbi-builder-app"
	 :class="dismissLite == false ? 'sbi-builder-app-lite-dismiss' : '' "
	 :data-app-loaded="appLoaded ? 'true' : 'false'">
	<?php
	$icons = function ($icon) {
		return InstagramFeed\Builder\SBI_Feed_Builder::builder_svg_icons($icon);
	};

	include_once SBI_BUILDER_DIR . 'templates/sections/header.php';
	include_once SBI_BUILDER_DIR . 'templates/sections/footer.php';
	// Onboarding Wizard
	include_once SBI_BUILDER_DIR . 'templates/screens/onboarding-wizard.php';
	?>
	<div class="sb-control-elem-tltp-content" v-show="tooltip.hover"
		 @mouseover.prevent.default="hoverTooltip(true, 'inside')"
		 @mouseleave.prevent.default="hoverTooltip(false, 'outside')">
		<div class="sb-control-elem-tltp-txt" v-html="tooltip.text" :data-align="tooltip.align"></div>
	</div>
</div>
