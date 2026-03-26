<div class="sbi-fb-source-ctn sb-fs-boss sbi-fb-center-boss" v-if="viewsActive.tempLoginAboutPopup !== false">
	<div class="sbi-fb-source-popup sbi-fb-tempuser-popup sbi-fb-popup-inside sbi-narrower-modal">
		<div class="sbi-fb-popup-cls" @click.prevent.default="activateView('tempLoginAboutPopup')">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z"
					  fill="#141B38"/>
			</svg>
		</div>
		<div class="sbi-fb-fs">
			<h3><?php echo __('Temporary Login Links', 'instgram-feed') ?></h3>

			<div class="sbi-fb-tempuser-icon-ctn sbi-fb-fs">
				<div class="sbi-fb-tempuser-icon">
					<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
						<mask id="mask0_4585_2431" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
							  width="30" height="30">
							<rect width="30" height="30" fill="#D9D9D9"/>
						</mask>
						<g mask="url(#mask0_4585_2431)">
							<path d="M8.74994 18.75C9.79161 18.75 10.677 18.3854 11.4062 17.6562C12.1354 16.9271 12.4999 16.0417 12.4999 15C12.4999 13.9583 12.1354 13.0729 11.4062 12.3438C10.677 11.6146 9.79161 11.25 8.74994 11.25C7.70827 11.25 6.82286 11.6146 6.09369 12.3438C5.36452 13.0729 4.99994 13.9583 4.99994 15C4.99994 16.0417 5.36452 16.9271 6.09369 17.6562C6.82286 18.3854 7.70827 18.75 8.74994 18.75ZM8.74994 22.5C6.66661 22.5 4.89577 21.7708 3.43744 20.3125C1.97911 18.8542 1.24994 17.0833 1.24994 15C1.24994 12.9167 1.97911 11.1458 3.43744 9.6875C4.89577 8.22917 6.66661 7.5 8.74994 7.5C10.4374 7.5 11.9116 7.97917 13.1724 8.9375C14.4324 9.89583 15.3124 11.0833 15.8124 12.5H25.7499C25.9166 12.5 26.0783 12.5312 26.2349 12.5938C26.3908 12.6562 26.5208 12.7396 26.6249 12.8437L27.8437 14.0625C27.9687 14.1875 28.0624 14.3279 28.1249 14.4837C28.1874 14.6404 28.2187 14.8021 28.2187 14.9688C28.2187 15.1354 28.1929 15.2917 28.1412 15.4375C28.0887 15.5833 27.9999 15.7188 27.8749 15.8438L24.6249 19.0938C24.4999 19.2188 24.3645 19.3125 24.2187 19.375C24.0729 19.4375 23.9166 19.4688 23.7499 19.4688C23.5833 19.4688 23.427 19.4425 23.2812 19.39C23.1354 19.3383 22.9999 19.25 22.8749 19.125L21.2499 17.5L19.6249 19.125C19.4999 19.25 19.3645 19.3383 19.2187 19.39C19.0729 19.4425 18.9166 19.4688 18.7499 19.4688C18.5833 19.4688 18.427 19.4425 18.2812 19.39C18.1354 19.3383 17.9999 19.25 17.8749 19.125L16.2499 17.5H15.8124C15.2916 19 14.3854 20.2083 13.0937 21.125C11.802 22.0417 10.3541 22.5 8.74994 22.5Z"
								  fill="#0068A0"/>
						</g>
					</svg>
				</div>
			</div>

			<div class="sbi-fb-tempuser-content-list sbi-fb-fs">

				<div class="sbi-fb-tempuser-content-item sbi-fb-fs">
					<div class="sbi-fb-tempuser-item-num">1</div>
					<div class="sbi-fb-tempuser-item-text">
						<strong class="sbi-fb-fs"><?php echo __('What are they used for?', 'instgram-feed') ?></strong>
						<p class="sbi-fb-fs"><?php echo __('Solving an issue in your plugin might sometime require testing API access but with your setup. We do not want to expose your API keys over support messages and hence we use a temporary login link system to securely access it.', 'instgram-feed') ?>
						<p>
					</div>
				</div>
				<div class="sbi-fb-tempuser-content-item sbi-fb-fs">
					<div class="sbi-fb-tempuser-item-num">2</div>
					<div class="sbi-fb-tempuser-item-text">
						<strong class="sbi-fb-fs"><?php echo __('What can a support executive access?', 'instgram-feed') ?></strong>
						<p class="sbi-fb-fs"><?php echo __('A support team member can only access Smash Balloon plugin to make API requests. They can NOT access any other plugins, create posts or in any way modify your WordPress website.', 'instgram-feed') ?>
						<p>
					</div>
				</div>
				<div class="sbi-fb-tempuser-content-item sbi-fb-fs">
					<div class="sbi-fb-tempuser-item-num">3</div>
					<div class="sbi-fb-tempuser-item-text">
						<strong class="sbi-fb-fs"><?php echo __('Can I disable or delete the temporary login link?', 'instgram-feed') ?></strong>
						<p class="sbi-fb-fs"><?php echo __('The login link is auto-destroyed in 14 days. You can also manually delete it any time you want.', 'instgram-feed') ?>
						<p>
					</div>
				</div>
			</div>

			<div class="sbi-fb-tempuser-footer-btn sbi-fb-fs">
				<button class="sb-btn sb-btn-blue" @click.prevent.default="activateView('tempLoginAboutPopup')">
					<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M9.66671 1.27325L8.72671 0.333252L5.00004 4.05992L1.27337 0.333252L0.333374 1.27325L4.06004 4.99992L0.333374 8.72659L1.27337 9.66659L5.00004 5.93992L8.72671 9.66659L9.66671 8.72659L5.94004 4.99992L9.66671 1.27325Z"
							  fill="white"/>
					</svg>
					<strong><?php echo __('Dismiss', 'instgram-feed') ?></strong>
				</button>
			</div>

		</div>
	</div>
</div>