<div class="sbi-fb-full-wrapper sbi-fb-fs">
	<?php

	/**
	 * SBI Admin Notices
	 *
	 * @since 4.0
	 */

	do_action('sbi_admin_notices');
	?>
	<div class="sbi-sb-container">
		<div class="sbi-section-header">
			<h2>{{genericText.title}}</h2>
			<div class="sbi-search-doc">
				<div :href="links.doc" target="_blank" class="sbi-search-doc-field">
					<span class="sb-btn-icon" @click="goToSearchDocumentation()" v-html="icons.magnify"></span>
					<input class="sb-btn-input" id="sbi-search-doc-input" v-model="searchKeywords"
						   v-on:keyup="searchDoc" v-on:paste="searchDocStrings" :placeholder="buttons.searchDoc">
				</div>
			</div>
		</div>

		<div class="sbi-support-blocks clearfix">
			<div class="sbi-support-block">
				<div class="sb-block-header">
					<img :src="icons.rocket" :alt="genericText.gettingStarted">
				</div>
				<h3>{{genericText.gettingStarted}}</h3>
				<p>{{genericText.someHelpful}}</p>
				<div class="sb-articles-list">
					<ul>
						<li v-for="article in articles.gettingStarted">
							<a :href="article.link">
								{{article.title}}
								<span class="sb-list-icon" v-html="icons.rightAngle"></span>
							</a>
						</li>
					</ul>
				</div>
				<div class="sbi-sb-button">
					<a :href="links.gettingStarted" target="_blank">
						{{buttons.moreHelp}}
						<span class="sb-btn-icon" v-html="icons.rightAngle"></span>
					</a>
				</div>
			</div>
			<div class="sbi-support-block">
				<div class="sb-block-header">
					<img :src="icons.book" :alt="genericText.docsN">
				</div>
				<h3>{{genericText.docsN}}</h3>
				<p>{{genericText.runInto}}</p>
				<div class="sb-articles-list">
					<ul>
						<li v-for="article in articles.docs">
							<a :href="article.link">
								{{article.title}}
								<span class="sb-list-icon" v-html="icons.rightAngle"></span>
							</a>
						</li>
					</ul>
				</div>
				<div class="sbi-sb-button">
					<a :href="links.doc" target="_blank">
						{{buttons.viewDoc}}
						<span class="sb-btn-icon" v-html="icons.rightAngle"></span>
					</a>
				</div>
			</div>
			<div class="sbi-support-block">
				<div class="sb-block-header">
					<img :src="icons.save" :alt="genericText.additionalR">
				</div>
				<h3>{{genericText.additionalR}}</h3>
				<p>{{genericText.toHelp}}</p>
				<div class="sb-articles-list">
					<ul>
						<li v-for="article in articles.resources">
							<a :href="article.link">
								{{article.title}}
								<span class="sb-list-icon" v-html="icons.rightAngle"></span>
							</a>
						</li>
					</ul>
				</div>
				<div class="sbi-sb-button">
					<a :href="links.blog" target="_blank">
						{{buttons.viewBlog}}
						<span class="sb-btn-icon" v-html="icons.rightAngle"></span>
					</a>
				</div>
			</div>
		</div>

		<div class="sbi-support-contact-block clearfix">
			<div class="sb-contact-block-left">
				<div class="sb-cb-icon">
					<span v-html="icons.forum"></span>
				</div>
				<div class="sb-cb-content">
					<h3>{{genericText.needMore}}</h3>
					<a :href="supportUrl" target="_blank" class="sb-cb-btn">
						{{buttons.submitTicket}}
						<span v-html="icons.rightAngle"></span>
					</a>
				</div>
			</div>
			<div class="sb-contact-block-right">
				<div>
					<img :src="images.supportMembers">
				</div>
				<p>{{genericText.ourFast}}</p>
			</div>
		</div>

		<div class="sbi-system-info-section">
			<div class="sbi-system-header">
				<h3>{{genericText.systemInfo}}</h3>
				<button class="sbi-copy-btn" @click.stop.prevent="copySystemInfo">
					<span v-html="icons.copy"></span>
					<span v-html="buttons.copy"></span>
				</button>
			</div>
			<div class="sbi-system-info">
				<div v-html="system_info" id="system_info" class="system_info" :class="systemInfoBtnStatus"></div>
				<button class="sbi-expand-btn" @click="expandSystemInfo">
					<span v-html="icons.downAngle"></span>
					<span v-html="expandBtnText()"></span>
				</button>
			</div>
		</div>

		<div class="sbi-tempuser-settings-section clearfix" v-if="tempUser === null">
			<div class="sbi-tempuser-left">
				<h3>{{genericText.newTempHeading}}</h3>
				<p>{{genericText.newTempDesc}}</p>
			</div>
			<div class="sbi-tempuser-right">
				<button class="sb-btn sb-btn-blue" @click.prevent.default="createTempUser"
						:disabled="createStatus !== null">
					<svg v-if="createStatus !== null" version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg"
						 x="0px" y="0px" width="20px" height="20px"
						 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path
								fill="#fff"
								d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h6.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
							<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25"
											  to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform>
						</path></svg>
					<svg v-if="createStatus === null" width="16" height="16" viewBox="0 0 16 16" fill="none"
						 xmlns="http://www.w3.org/2000/svg">
						<mask id="mask0_4615_22" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
							  width="16" height="16">
							<rect width="16" height="16" fill="#D9D9D9"/>
						</mask>
						<g mask="url(#mask0_4615_22)">
							<path d="M4.66675 10C5.2223 10 5.69453 9.80556 6.08342 9.41667C6.4723 9.02778 6.66675 8.55556 6.66675 8C6.66675 7.44444 6.4723 6.97222 6.08342 6.58333C5.69453 6.19444 5.2223 6 4.66675 6C4.11119 6 3.63897 6.19444 3.25008 6.58333C2.86119 6.97222 2.66675 7.44444 2.66675 8C2.66675 8.55556 2.86119 9.02778 3.25008 9.41667C3.63897 9.80556 4.11119 10 4.66675 10ZM4.66675 12C3.55564 12 2.61119 11.6111 1.83341 10.8333C1.05564 10.0556 0.666748 9.11111 0.666748 8C0.666748 6.88889 1.05564 5.94444 1.83341 5.16667C2.61119 4.38889 3.55564 4 4.66675 4C5.56675 4 6.35297 4.25556 7.02541 4.76667C7.69741 5.27778 8.16675 5.91111 8.43341 6.66667H13.7334C13.8223 6.66667 13.9085 6.68333 13.9921 6.71667C14.0752 6.75 14.1445 6.79444 14.2001 6.85L14.8501 7.5C14.9167 7.56667 14.9667 7.64156 15.0001 7.72467C15.0334 7.80822 15.0501 7.89444 15.0501 7.98333C15.0501 8.07222 15.0363 8.15556 15.0087 8.23333C14.9807 8.31111 14.9334 8.38333 14.8667 8.45L13.1334 10.1833C13.0667 10.25 12.9945 10.3 12.9167 10.3333C12.839 10.3667 12.7556 10.3833 12.6667 10.3833C12.5779 10.3833 12.4945 10.3693 12.4167 10.3413C12.339 10.3138 12.2667 10.2667 12.2001 10.2L11.3334 9.33333L10.4667 10.2C10.4001 10.2667 10.3279 10.3138 10.2501 10.3413C10.1723 10.3693 10.089 10.3833 10.0001 10.3833C9.91119 10.3833 9.82786 10.3693 9.75008 10.3413C9.6723 10.3138 9.60008 10.2667 9.53341 10.2L8.66675 9.33333H8.43341C8.15564 10.1333 7.6723 10.7778 6.98342 11.2667C6.29453 11.7556 5.5223 12 4.66675 12Z"
								  fill="white"/>
						</g>
					</svg>
					<strong>{{genericText.newTempButton}}</strong>
				</button>
				<button class="sb-btn sb-btn-grey" @click.prevent.default="activateView('tempLoginAboutPopup')">
					<strong>{{genericText.learnMore}}</strong>
				</button>
			</div>
		</div>

		<div class="sbi-tempuser-settings-section sbi-templogin-settings-section clearfix" v-if="tempUser !== null">
			<div class="sbi-tempuser-left">
				<h3>{{genericText.tempLoginHeading}}</h3>
				<p>{{genericText.tempLoginDesc}}</p>
			</div>
			<table class="sbi-tempuser-list" :aria-describedby="genericText.tempLoginDesc">
				<tr>
					<th>{{genericText.link}}</th>
					<th>{{genericText.expires}}</th>
					<th></th>
				</tr>
				<tr>
					<td>
						<span class="sb-tempuser-link">{{tempUser.url}}</span>
					</td>
					<td>
						<span class="sb-tempuser-expires">{{tempUser.expires_date + ' ' + ( parseInt(tempUser.expires_date) <= 1 ? genericText.day : genericText.days)}}</span>
					</td>
					<td class="sb-tempuser-btns">
						<button class="sb-btn sb-btn-red" @click.prevent.default="deleteTempUser">
							<svg v-if="deleteStatus !== null" version="1.1" id="loader-1"
								 xmlns="http://www.w3.org/2000/svg" x="0px"
								 y="0px" width="20px" height="20px" viewBox="0 0 50 50"
								 style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff"
																									 d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h6.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
									<animateTransform attributeType="xml" attributeName="transform" type="rotate"
													  from="0 25 25" to="360 25 25" dur="0.6s"
													  repeatCount="indefinite"></animateTransform>
								</path></svg>
							<strong>{{genericText.delete}}</strong>
						</button>
						<button class="sb-btn sb-btn-grey" @click.prevent.default="copyToClipBoard(tempUser.url)">
							<svg width="16" height="17" viewBox="0 0 16 17" fill="none"
								 xmlns="http://www.w3.org/2000/svg">
								<path d="M12 1.83325H6C5.26667 1.83325 4.66667 2.43325 4.66667 3.16659V11.1666C4.66667 11.8999 5.26667 12.4999 6 12.4999H12C12.7333 12.4999 13.3333 11.8999 13.3333 11.1666V3.16659C13.3333 2.43325 12.7333 1.83325 12 1.83325ZM12 11.1666H6V3.16659H12V11.1666ZM2 10.4999V9.16659H3.33333V10.4999H2ZM2 6.83325H3.33333V8.16659H2V6.83325ZM6.66667 13.8333H8V15.1666H6.66667V13.8333ZM2 12.8333V11.4999H3.33333V12.8333H2ZM3.33333 15.1666C2.6 15.1666 2 14.5666 2 13.8333H3.33333V15.1666ZM5.66667 15.1666H4.33333V13.8333H5.66667V15.1666ZM9 15.1666V13.8333H10.3333C10.3333 14.5666 9.73333 15.1666 9 15.1666ZM3.33333 4.49992V5.83325H2C2 5.09992 2.6 4.49992 3.33333 4.49992Z"
									  fill="#141B38"/>
							</svg>
							<strong>{{genericText.copyLink}}</strong>
						</button>
						</th>
				</tr>
			</table>
		</div>

	</div>
</div>
<div class="sb-notification-ctn" :data-active="notificationElement.shown" :data-type="notificationElement.type">
	<div class="sb-notification-icon" v-html="svgIcons[notificationElement.type+'Notification']"></div>
	<span class="sb-notification-text" v-html="notificationElement.text"></span>
</div>
<?php
include_once SBI_BUILDER_DIR . 'templates/sections/popup/temp-login-about.php';
?>