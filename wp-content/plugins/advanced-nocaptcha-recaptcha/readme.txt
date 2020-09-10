=== Advanced noCaptcha & invisible Captcha (v2 & v3) ===
Contributors: shamim51
Tags: recaptcha,nocaptcha,invisible,bot,spam
Donate link: https://www.shamimsplugins.com/products/advanced-nocaptcha-and-invisible-captcha-pro/
Requires at least: 4.4
Tested up to: 5.5
Stable tag: 6.1.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show noCaptcha or invisible captcha in Comment (after Comment textarea before submit button), CF7, bbpress, BuddyPress, woocommerce, Login, Register, Lost & Reset Password.

== Description ==

Show noCaptcha or invisible captcha in Comment Form (after Comment textarea before submit button), Contact Form 7, bbPress, BuddyPress, woocommerce, Login, Register, Lost Password, Reset Password. Also can implement in any other form easily.

* **Allow multiple captcha in same page.**
* **Allow conditional login captcha** (you can set after how many failed login attempts login captcha will show)

> [For **Advanced noCaptcha & invisible Captcha PRO** click here](https://www.shamimsplugins.com/products/advanced-nocaptcha-and-invisible-captcha-pro/?utm_campaign=wordpress&utm_source=readme_pro&utm_medium=description)

= Show noCaptcha on =

* Comment Form (after Comment textarea before submit button)
* WooCommerce
* Login
* Register
* Multisite User Signup
* Lost Password
* Reset Password
* Contact Form 7
* FEP Contact Form
* bbPress New topic
* bbPress reply to topic
* BuddyPress register

= Options =

* You can select which version of reCaptcha will be used (v2 I'm not robot checkbox, v2 invisible or v3)
* Language can be changed
* Error message can be changed
* For v2 I'm not robot: Theme, Size can be changed.
* For v2 Invisible: Theme, badge location can be changed.
* For v3: Score and when to load script can be changed
* Option to show/hide captcha for logged in users
* Captcha will show if javascript disabled also (optional)

= Privacy Notices =

* This plugin send IP address go Google for captcha verification. Please read [Google Privacy Policy](https://policies.google.com/).
* If you set "Show login Captcha after how many failed attempts" to more than 0(zero) then user hash from ip address will be stored in database. After successful login, hash of that ip address will be deleted. 

== Installation ==
1. Upload "advanced-nocaptcha-recaptcha" to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Go to plugin settings page for setup.


== Frequently Asked Questions ==

= Can i use this plugin to my language? =
Yes. this plugin is translate ready. But If your language is not available you can make one. If you want to help us to translate this plugin to your language you are welcome.

= Can i show multiple captcha in same page? =
Yes. You can show unlimited number of captcha in same page.

= How to load reCaptcha v3 script only when there is form in that page? =
Loading v3 script in All Pages help google for analytics. If you want to load script only when there is form in that page please go to Dashboard > Settings > Advanced noCaptcha & invisible Captcha > v3 Script Load and set to "Form Pages".
If you are not using v3 then script will only load when there is form in that page. no settings required.

= How to set captcha in contact form 7? =
To show noCaptcha use [anr_nocaptcha g-recaptcha-response]

= How to set captcha in WooCommerce? =
If Login Form, Registration Form, Lost Password Form, Reset Password Form is selected in SETTINGS page of this plugin, they will show and verify Captcha in WooCommerce respective forms as well.

= How to login if i am locked out? =
You can access your file via FTP or file manager and rename "advanced-nocaptcha-recaptcha" folder to something else. Then login as normal. Then rename back this folder.

== Screenshots ==

1. Captcha in comment form
2. Captcha in Contact Form 7
3. Captcha in WooCommerce (multiple in same page)
4. Captcha in Login Form
5. Captcha in Register Form
6. Captcha in Lost Password Form
7. Advanced noCaptcha reCaptcha Settings
8. Advanced noCaptcha reCaptcha Setup Instruction

== Changelog ==

= 6.1.1 =

* recaptcha domain can now be changed from settings.
* footer script hook priority changed.
* use same settings if network activated.
* for cf7, use this plugins captcha instead of cf7 captcha.

= 5.7.1 =

* Minor bug fixed.

= 5.7 =

* IP whitelist feature added.
* Captcha V3 timeout issue fixed.
* UM login issue fixed.

= 5.6 =

* Return last verify incase of duplicate checking.
* Add google scripts src filters.
* Custom hook and captcha shortcode now support logged in setup.

= 5.5 =

* Fix: Multisite site signup during registration failed due to double verification.
* Fix: Comment reply failed from back-end.

= 5.4 =

* Use js for loop instead of php for loop
* Use number_formate_i18n to translate float
* Tested upto updated.

= 5.3 =

* Fix: Compatibility issue with reCaptcha v3 and CF7 version 5.1 & 5.1.1

= 5.2 =

* Now support reCaptcha v3 also
* Fix: invisible captcha sometimes was not working
* anr_verify_captcha filter added

= 4.4 =

* PRO version released
* anr_verify_captcha_pre filter added
* anr_get_option filter added

= 4.3 =

* Reset captcha if CF7 validation error occur
* Changed Tested up to

= 4.2 =

* BuddyPress mentioned in readme
* WooCommerce checkout captcha sometimes did not verify
* Reset captcha if WooCommerce checkout error occur
* If WordPress version is 4.9.0 or greater then pre_comment_approved filter used for comment which we can now return WP_Error

= 4.1 =

* Settings page redesigned.
* anr_is_form_enabled function added
* Captcha error show first before username password error. So if captcha is not validated then username password error is not shown.
* enqueue login css only if normal captcha is shown
* Enabled forms stored as an array in db. array key is enabled_forms
* Add class ANR_Settings, removed class anr_admin_class
* BuddyPress register captcha added

= 3.1 =

* Sometimes fatal error if is_admin return true in front-end.
* Do not show captcha in checkout if not checked for checkout.

= 2.8 =

* Now show captcha when use wp_login_form() function to create login form.

= 2.7 =

* Fix: Settings page checkbox uncheck was not working.

= 2.6 =

* New: Show captcha after set failed login attempts (may not work if you use ajax based login form, fall back to show always).
* Fix: contact form 7 deprecated function use.

= 2.5 =

* New: Invisible captcha feature added.
* Fix: Show captcha error when login form loaded
* Move this plugin settings page under Settings

= 2.4 =

* Bug fix: WooCommerce lostpassword corrupted link

= 2.3 =

* Comment form captcha issue fixed.
* Captcha now wraped in anr_captcha_field div class.
* Comment form captcha p tag removed.

= 2.2 =

* Security update.
* WooCommerce checkout form issue fixed.

= 2.1 =

* Captcha in WooCommerce added (WooCommerce Login, Registration, Lost password, Reset password forms).
* Allow multiple captcha in same page.
* Text domain changed.
* Some minor bug fixed.

= 1.3 =

* New filter 'anr_same_settings_for_all_sites' added, Now same settings can be used for all sites in Multisite.
* Multisite User Signup Form added.
* Some bug fixed.

= 1.2 =

* Now captcha size can be changed.
* bbPress New topic added
* bbPress reply to topic added
* XMLRPC_REQUEST Check
* Some bug fixed.

= 1.1 =

* Initial release.

== Upgrade Notice ==

= 2.7 =

* Fix: Settings page checkbox uncheck was not working.

= 2.6 =

* New: Show captcha after set failed login attempts (may not work if you use ajax based login form, fall back to show always).
* Fix: contact form 7 deprecated function use.
