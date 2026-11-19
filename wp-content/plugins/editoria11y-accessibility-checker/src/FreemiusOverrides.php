<?php
/**
 * Centralized i18n overrides for the bundled Freemius SDK.
 *
 * The SDK ships English copy with notice titles like "Yee-haw!" and
 * "Hmm..." that don't fit Editoria11y's plain, professional voice. It
 * also uses generic "Pro / plan / premium" terminology where we want
 * "Editoria11y CSA" specifically. We intercept those strings via the
 * SDK's documented `fs_override_i18n()` API so we don't fork vendor/.
 *
 * Each override is keyed by the SDK's internal string key (the third
 * arg to `*_text_x_inline` / second arg to `*_text_inline`) — NOT by
 * the English text. The SDK looks up `$fs_text_overrides[$slug][$key]`
 * before falling back to its inline default, so our replacements take
 * effect regardless of the active locale.
 *
 * The `entries()` method is the source of truth: each entry pins the
 * `original` English we reviewed against, the `replacement` we chose,
 * and a `note` explaining the surrounding pairing. The audit script at
 * `scripts/audit-freemius-strings.php` reads `entries()` and diffs the
 * `original` field against the live SDK source so a Freemius bump
 * surfaces drift before it ships to users.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Registers replacement strings against the SDK's text-override
 * registry.
 */
final class FreemiusOverrides {

	/**
	 * Apply our overrides against the running Freemius instance.
	 *
	 * Uses the SDK's instance method `override_i18n()` so the
	 * registration lands under whichever slug the SDK is currently
	 * resolving (`slug` or `premium_slug` depending on build). This
	 * is the documented seam — see Freemius::override_i18n() and
	 * fs_override_i18n() in fs-core-functions.php.
	 *
	 * Safe to call once per request after fs_dynamic_init() returns;
	 * sticky notices read the title at the time of `add_sticky()`, so
	 * applying the overrides in the same bootstrap as fs_dynamic_init
	 * is sufficient — by the time a user-facing notice renders on a
	 * later admin request, the override map is already populated.
	 *
	 * @param \Freemius $ed11ycsa Live SDK instance returned by
	 *                            fs_dynamic_init().
	 */
	public static function apply( \Freemius $ed11ycsa ): void {
		$ed11ycsa->override_i18n( self::map() );
	}

	/**
	 * Return the override map keyed for `fs_override_i18n()`.
	 *
	 * Plain `key => replacement` shape derived from `entries()`.
	 *
	 * @return array<string, string>
	 */
	public static function map(): array {
		$out = array();
		foreach ( self::entries() as $key => $entry ) {
			$out[ $key ] = $entry['replacement'];
		}
		return $out;
	}

	/**
	 * Annotated override entries — used by the audit script and tests.
	 *
	 * Categories:
	 *   - Tier 1 — interjection notice titles ("Yee-haw", "Oops",
	 *     etc.). Each is concatenated with "!" or "..." by the caller.
	 *   - Tier 2 — body strings paired with Tier 1 titles. Rewritten
	 *     to use Editoria11y CSA naming and avoid word duplication.
	 *   - Tier 3 — generic "Pro / premium / plan" copy retargeted to
	 *     Editoria11y CSA.
	 *   - Tier 4 — informal phrasing ("Thanks so much!", "Hey there",
	 *     "AWESOME") softened to plain professional voice.
	 *
	 * @return array<string, array{original:string, replacement:string, note:string}>
	 */
	public static function entries(): array {
		return array(

			/* === Tier 1 — interjection notice titles === */

			'yee-haw'                                    => array(
				'original'    => 'Yee-haw',
				'replacement' => 'Success',
				'note'        => 'Notice title shown after license purchase / plan upgrade. Pairs with body strings rewritten in Tier 2.',
			),
			'oops'                                       => array(
				'original'    => 'Oops',
				'replacement' => 'Error',
				'note'        => 'Notice title for ~10 distinct error states. Caller appends "..." → renders as "Error..."',
			),
			'right-on'                                   => array(
				'original'    => 'Right on',
				'replacement' => 'Verified',
				'note'        => 'Notice title shown after email verification.',
			),
			'hmm'                                        => array(
				'original'    => 'Hmm',
				'replacement' => 'Notice',
				'note'        => 'Notice title for ambiguous license-sync / plan-mismatch states. Caller appends "..." → "Notice..."',
			),
			'awesome'                                    => array(
				'original'    => 'Awesome',
				'replacement' => 'Success',
				'note'        => 'Notice title after successful opt-in.',
			),
			'hey'                                        => array(
				'original'    => 'Hey',
				'replacement' => 'Welcome',
				'note'        => 'Concatenated as "Hey! " before the trial promo body. With "Welcome" the resulting sentence reads naturally.',
			),

			/* === Tier 2 — paired body strings === */

			'connect-message'                            => array(
				'original'    => 'Opt in to get email notifications for security & feature updates, educational content, and occasional offers, and to share some basic WordPress environment info. This will help us make the %s more compatible with your site and better at doing what you need it to.',
				'replacement' => 'Opt in to allow Freemius to log your WordPress environment and activation status (this is only used to record and troubleshoot licensing), and to allow email notifications for security and feature updates.',
				'note'        => 'Their wording asks for more permission than we need.',
			),
			'plan-activated-message'                     => array(
				'original'    => 'Your plan was successfully activated.',
				'replacement' => 'Your Editoria11y CSA license is now active.',
				'note'        => 'Body paired with "Success!" title. Avoids repeating "Success".',
			),
			'plan-upgraded-message'                      => array(
				'original'    => 'Your plan was successfully upgraded.',
				'replacement' => 'Your Editoria11y CSA plan has been updated.',
				'note'        => 'Body paired with "Success!" title.',
			),
			'license-activated-message'                  => array(
				'original'    => 'Your license was successfully activated.',
				'replacement' => 'Your Editoria11y CSA license is now active.',
				'note'        => 'Body shown on manual license activation.',
			),
			'addon-successfully-upgraded-message'        => array(
				'original'    => 'Your %s Add-on plan was successfully upgraded.',
				'replacement' => 'Your Editoria11y CSA add-on has been updated.',
				'note'        => 'Body for add-on upgrade. Drops the %s arg — sprintf safely ignores extras.',
			),
			'plan-changed-to-x-message'                  => array(
				'original'    => 'Your plan was successfully changed to %s.',
				'replacement' => 'Your Editoria11y CSA plan was changed to %s.',
				'note'        => 'Preserves the %s plan-title placeholder.',
			),
			'premium-activated-message'                  => array(
				'original'    => 'Premium %s version was successfully activated.',
				'replacement' => 'Editoria11y CSA features are now active.',
				'note'        => 'Body shown when premium build replaces free build. %s drops harmlessly.',
			),
			'activation-with-plan-x-message'             => array(
				'original'    => 'Your account was successfully activated with the %s plan.',
				'replacement' => 'Your account was activated with the %s plan.',
				'note'        => 'Drops redundant adverb. Preserves %s.',
			),
			'you-have-x-license'                         => array(
				'original'    => 'You have a %s license.',
				'replacement' => 'Your Editoria11y CSA license has been activated.',
				'note'        => 'Sticky notice shown when a paying user is still running the free build; the SDK appends its install-the-premium-version instructions. The post-checkout usage of this key was removed in SDK 2.13.5. %s drops harmlessly.',
			),
			'email-verified-message'                     => array(
				'original'    => 'Your email has been successfully verified - you are AWESOME!',
				'replacement' => 'Your email address has been verified.',
				'note'        => 'Drops the all-caps celebratory tail.',
			),

			/* === Tier 3 — generic "Pro / premium / plan" copy retargeted === */

			'trial-x-promotion-message'                  => array(
				'original'    => 'How do you like %s so far? Test all our %s premium features with a %d-day free trial.',
				'replacement' => 'If you like %1$s, consider supporting its development; start with a %3$d-day free trial of developer tools and new features in progress.',
				'note'        => 'Drops the %s plan title. Positional specifiers are required to skip a middle arg: a bare %d would consume the plan-title string (arg 2) and render as 0.',
			),
			'license-expired-non-blocking-message'       => array(
				'original'    => 'Your license has expired. You can still continue using all the %s features, but you\'ll need to renew your license to continue getting updates and support.',
				'replacement' => 'Your Editoria11y CSA license has expired. You can keep using the features you have, but renew your license to continue receiving updates and support.',
				'note'        => 'Drops "%s features" generic phrasing. %s drops harmlessly.',
			),
			'activate-premium-version'                   => array(
				'original'    => ' The paid version of %1$s is already installed. Please activate it to start benefiting from the %2$s features. %3$s',
				'replacement' => ' The Editoria11y CSA build of %1$s is already installed. Activate it to use the CSA features. %3$s',
				'note'        => 'Replaces "paid version" / "%2$s features" with explicit CSA naming. Preserves trailing %3$s call-to-action link.',
			),
			'activate-premium-version-plugins-page'      => array(
				'original'    => ' The paid version of %1$s is already installed. Please navigate to the %2$s to activate it and start benefiting from the %3$s features.',
				'replacement' => ' The Editoria11y CSA build of %1$s is already installed. Navigate to %2$s to activate it and use the CSA features.',
				'note'        => 'Same retarget as activate-premium-version. %2$s = "plugins page" link. Drops %3$s plan-title arg (sprintf ignores extras).',
			),
			'permissions-profile_tooltip'                => array(
				'original'    => 'Never miss important updates, get security warnings before they become public knowledge, and receive notifications about special offers and awesome new features.',
				'replacement' => 'Never miss important updates or security notices, and get notifications about noteworthy new features.',
				'note'        => 'Drops "awesome".',
			),

			/* === Tier 4 — informal phrasing softened === */

			'thank-you-for-using-product'                => array(
				'original'    => 'Thank you so much for using %s!',
				'replacement' => 'Thank you for using %s.',
				'note'        => 'Drops effusive "so much" and exclamation.',
			),
			'thank-you-for-using-products'               => array(
				'original'    => 'Thank you so much for using our products!',
				'replacement' => 'Thank you for using our products.',
				'note'        => 'Multi-product variant.',
			),
			'thank-you-for-using-product-and-its-addons' => array(
				'original'    => 'Thank you so much for using %s and its add-ons!',
				'replacement' => 'Thank you for using %s and its add-ons.',
				'note'        => 'Add-on variant.',
			),
			'become-an-ambassador-admin-notice'          => array(
				'original'    => 'Hey there, did you know that %s has an affiliate program? If you like the %s you can become our ambassador and earn some cash!',
				'replacement' => '%s has an affiliate program. If you would like to refer others to %s, you can earn a commission as an ambassador.',
				'note'        => 'Drops "Hey there" and "earn some cash". Preserves both %s placeholders.',
			),
		);
	}
}
