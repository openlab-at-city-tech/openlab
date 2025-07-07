<div class="ekit-onboard-main-header">
	<h1 class="ekit-onboard-main-header--title"><strong><?php echo esc_html__('Great! You’re All Set!', 'elementskit-lite'); ?></strong></h1>
	<div class="ekit-onboard-main-header--description-wrapper">
		<p class="ekit-onboard-main-header--description">
			<?php echo esc_html__('Here’s an overview of everything that is setup.', 'elementskit-lite'); ?>
		</p>
		<span class="ekit-onboard-main-header--progress-percentage">0%</span>
	</div>
	<div class="ekit-onboard-main-header--progress-bar">
		<div class="ekit-onboard-main-header--progress"></div>
	</div>
</div>

<div class="configure-features" id="configure-ekit-onboard"></div>

<div class="go-to-dashboard">
	<a class="ekit-onboard-btn" href="<?php echo esc_url(admin_url('admin.php?page=elementskit')); ?>">
		<?php echo esc_html__( 'Go to WP Dashboard', 'elementskit-lite' ); ?>
	</a>
</div>

<script>
	const target = document.getElementById('configure-ekit-onboard');

	const observer = new IntersectionObserver(entries => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				// Create and dispatch a custom event
				const event = new CustomEvent('configureEkitOnboard', {
					detail: { message: 'configuring ekit onboard' }
				});

				// Dispatch the event from the element or window
				window.dispatchEvent(event);
			}
		});
	}, {
		threshold: 0.1 // Adjust as needed
	});

	observer.observe(target);
</script>