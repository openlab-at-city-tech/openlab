=== Captcha ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=10&product_id=13
Tags: captcha, math captcha, text captcha, spam, antispam, login, registration, comment, lost password, capcha, catcha, captha
Requires at least: 2.9
Tested up to: 3.4.1
Stable tag: 2.31

This plugin allows you to implement super security captcha form into web forms.

== Description ==

Captcha plugin allows you to protect your website from spam using math logic which can be used for login, registration, reseting password, comments forms. Russian, German and Dutch languages are added.

<a href="http://wordpress.org/extend/plugins/captcha/faq/" target="_blank">FAQ</a>
<a href="http://bestwebsoft.com/plugin/captcha-plugin/" target="_blank">Support</a>

= Features =

* Display: It is possible to use letters and numbers in the captcha or just one of these two things - either letters or numbers.
* Actions: The basic mathematical operations are used - add, subtract, multiply.
* Label: There is a possibility to add a label when displaying captcha on the form.

= Translation =

* Brazilian Portuguese (pt_BR) (thanks <a href="mailto:brenojac@gmail.com">Breno Jacinto</a>, www.iconis.org.br)
* Czech (cs_CZ) (thanks to Tomas Vesely)
* Danish (da_DK) (thanks to Byrial Ole Jensed)
* Dutch (nl_NL) (thanks to <a href="mailto:byrial@vip.cybercity.dk">Bart Duineveld</a>)
* Greek (el) (thanks to Aris, <a href="http://paraxeno.net">paraxeno.net</a>)
* Farsi/Persian (fa_IR) (thanks to <a href="mailto:info[at]mpspace[dot]zio[dot]ir">Meysam Parvizi</a>, <a href="http://mpspace.zio.ir">mpspace.zio.ir</a>)
* Finnish (fi) (thanks to Mikko Sederholm)
* French (fr_FR) (thanks to Martel Benjamin)
* German (de_DE) (thanks to Thomas Hartung)
* Hebrew (he_IL) (thanks to Sagive SEO)
* Hindi (hi_IN) (thanks to <a href="mailto:ash.pr@outshinesolutions.com">Outshine Solutions</a>)
* Italian (it_IT) (thanks to Gianluca Di Carlo)
* Norwegian (nb_NO)	(thanks to Tore Hjartland)
* Polish (pl_PL) (thanks to Krzysztof Opuchlik)
* Russian (ru_RU)
* Spain (es_ES) (thanks to Iván García Cubero)
* Swedish (sv_SE) (thanks to Christer Rönningborg)
* Turkish (tr_TR) (thanks to Can Atasever, <a href="http://www.candanblog.com">candanblog.com</a>)
* Ukrainian (uk_UA) (thanks to Oleg Bondarenko)
* Vietnamese (vi_VN) (thanks to NDT Solutions)

If you create your own language pack or update an existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://bestwebsoft.com/" target="_blank">BWS</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or propositions regarding functionality of our plugins (current options, new options, current issues) please feel free to contact us. Please note that we accept requests in English language only. All messages on another languages wouldn't be accepted. 

Also, emails which are reporting about plugin's bugs are accepted for investigation and fixing. Your request must contain URL of the website, issues description and WordPress admin panel access. Plugin customization based on your Wordpress theme is a paid service (standard price is $10, but it could be higer and depends on the complexity of requested changes). We will analize existing issue and make necessary changes after 100% pre-payment.All these paid changes and modifications could be included to the next version of plugin and will be shared for all users like an integral part of the plugin. Free fixing services will be provided for user who send translation on their native language (this should be a new translation of a certain plugin, and you can check available translations on the official plugin page).

== Installation ==

1. Upload `captcha` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Plugin settings are located in 'Settings', 'Captcha'.

== Frequently Asked Questions ==

= How to change captcha label =

Go to the Settings page and change value for the 'Label for CAPTCHA in form' field.

= During saving of settings I got an error: 'Please select one point in the blocks Arithmetic actions and Difficulty for CAPTCHA'. What is this? =

For correct work of Captcha plugin you need to choose at least one item from the 'Arithmetic actions' block and choose 'Difficulty' via Settings page, because math expression should be consisted minimum of 1 mathematical sign and parts of mathematical expression should be displayed like words or like numbers or both of them.

= Missing CAPTCHA on comment form? = 

You may have a theme that has not properly coded comments.php. 

The version of WP makes a difference...

(WP2 series) Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your `/wp-content/themes/[your_theme]/comments.php` file. 
Most WP2 themes already do. The best place to locate the tag is before the comment textarea, you may want to move it up if it is below the comment textarea.

(WP3 series) Since WP3 there is new function comment_form inside `/wp-includes/comment-template.php`. 
Your theme is probably not up to current code to call that function from inside comments.php.
WP3 theme does not need the `do_action('comment_form'`... code line inside `/wp-content/themes/[your_theme]/comments.php`.
Instead, it uses a new function call inside comments.php: `<?php comment_form(); ?>`
If you have WP3 and still have captcha missing, make sure your theme has `<?php comment_form(); ?>`
inside `/wp-content/themes/[your_theme]/comments.php`. (look inside the Twenty Ten theme's comments.php for proper example)

= How to use the other language files with the CAPTCHA? = 

Here is an example for German language files.

1. In order to use another language for WordPress it is necessary to set the WP version to the required language and in configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` write `define('WPLANG', 'de_DE');`. If everything is done properly the admin panel will be in German.

2. Make sure that there are files `de_DE.po` and `de_DE.mo` in the plugin (the folder languages in the root of the plugin).

3. If there are no such files it will be necessary to copy other files from this folder (for example, for Russian or Italian language) and rename them (you should write `de_DE` instead of `ru_RU` in the both files).

4. The files are edited with the help of the program Poedit - http://www.poedit.net/download.php - please load this program, install it, open the file with the help of this program (the required language file) and for each line in English you should write translation in German.

5. If everything is done properly all lines will be in German in the admin panel and on frontend.

= I would like to add Captcha to custom form on my website. How can I do this? =

1. Install Captcha plugin, activate it.
2. Open file with the form (where it is necessary to implement captcha).
3. Find the place where it is necessary to insert code to display captcha.
4. Insert lines to display captcha

`if( function_exists( 'cptch_display_captcha_custom' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha_custom() } ;`

If the form is html it will be necessary to insert the line with tags php

`<?php if( function_exists( 'cptch_display_captcha_custom' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha_custom(); } ?>`

5. It is necessary to add the lines in the function of check of the entered data (where it is checked what the user enters and if everything is correct the mail will be sent) 

`if( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) echo "Please complete the CAPTCHA."`
or
`<?php if( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) echo "Please complete the CAPTCHA." ?>`
It is possible to enter this line in variable and display this variable in required place instead of `echo "Please complete the CAPTCHA."`. If there is a variable (which is answered for the displaying of the errors) in the function of check so this phrase can be added to this variable. If the function returned true then you have entered captcha properly. In other cases the function will return false.

== Screenshots ==

1. Captcha Settings page.
2. Comments form with Captcha.
3. Registration form with Captcha.
4. Lost password form with Captcha.
5. Login form with Captcha.

== Changelog ==

= V2.31 - 10.07.2012 =
* NEW : Vietnamese language file is added to the plugin.
* Update : We updated Hebrew language file.
* Update : We updated all functionality for wordpress 3.4.1.

= V2.30 - 04.07.2012 =
* Bugfix: The bug related to the forced double login in the admin panel was fixed.

= V2.29 - 27.06.2012 =
* NEW : Hebrew language file is added to the plugin.
* Update : We updated all functionality for wordpress 3.4.

= V2.28 - 11.06.2012 =
* NEW : Greek and Hindi language files are added to the plugin.

= V2.27 - 20.03.2012 =
* NEW : Swedish language file is added to the plugin.

= V2.26 - 12.03.2012 =
* NEW : Turkish language file is added to the plugin.

= V2.25 - 02.03.2012 =
* NEW : Finnish language file is added to the plugin.

= V2.24 - 24.02.2012 =
* Change : Code that is used to connect styles and scripts is added to the plugin for correct SSL verification.

= V2.23 - 17.02.2012 =
* NEW : Norwegian language file is added to the plugin.

= V2.22 - 14.02.2012 =
* Bugfix: Danish language files are edited in the plugin.

= V2.21 - 07.02.2012 =
* NEW : Czech language file is added to the plugin.

= V2.20 - 31.01.2012 =
* NEW : Ukrainian language file is added to the plugin.

= V2.19 - 18.01.2012 =
* Bugfix : Sintax errors were fixed.

= V2.18 - 18.01.2012 =
* NEW : Farsi/Persian, Italian language files are added to the plugin.
* Bugfix : Session errors and 'undefined index' error were fixed.

= V2.17 - 12.01.2012 =
* NEW : Spain language file is added to the plugin.

= V2.16 - 11.01.2012 =
* NEW : Polish language file is added to the plugin.

= V2.15 - 05.01.2012 =
* NEW : Brazilian Portuguese and French language files are added to the plugin.

= V2.14 - 04.01.2012 =
* NEW : German language file is added to the plugin.

= V2.13 - 03.01.2012 =
* Bugfix : Impossible math operation bug was fixed.

= V2.12 - 29.12.2011 =
* Changed : BWS plugins section. 
* Bugfix : Displaying of numerals was fixed in the Dutch language

= V2.11 - 27.12.2011 =
* NEW : Danish language files are added to the plugin. 
* Changed : All words were added to language file. 

= V2.10 - 07.12.2011 =
* Bugfix : The bug of the captcha label section is fixed in this version. 

= V2.09 - 07.12.2011 =
* Changed : +, -, * are changed to HTML Entity.

= V2.08 - 01.11.2011 =
* NEW : Dutch language files are added to the plugin.

= V2.07 - 31.10.2011 =
* NEW : Language files are added to the plugin.

= V2.06 - 22.08.2011 =
* Changed : BWS Plugin's menu section was fixed and right now it is consisted of 3 parts: activated, installed and recommended plugins. 
* Bugfix : Positioning bug in admin menu is fixed. 

= V2.05 =
* Changed : BWS Plugin's menu section was fixed and right now it is consisted from 2 parts: installed and recommended plugins. 
* Bugfix : Icons displaying is fixed. 
* Bugfix : Misalignment of math transaction is fixed.

= V2.04 =
* In this version of the plugin a bug of CAPTCHa displaying (before and after the comment form) was fixed. Please upgrade Captcha plugin immediately. Thank you. For more detailed information please see FAQ

= V2.03 =
* In this version of the plugin a bug of CAPTCHa displaying was fixed in some of the themes for release of WordPress 3.0 and above. Please upgrade Captcha plugin immediately. Thank you

= V2.02 =
* The bug of captcha settings page link is fixed in this version. Please upgrade Captcha plugin immediately. Thank you

= V2.01 =
* Usability at the settings page of the plugin was improved.

= V1.04 =
* The bug of the captcha output is fixed in this version. Please upgrade Captcha plugin immediately. Thank you.

= V1.03 =
* Ability to add BestWebSoft Contact Form plugin to Captcha plugin from wp-admin via Settings panel is added.

= V1.02 =
* "Settings", "FAQ", "Support" links are added to the plugin action page.
* Links on the plugins page are added.

= V1.01 =
* Select functionality of mathematical actions and level of their difficulty are implemented.

== Upgrade Notice ==

= V2.31 =
Vietnamese language file is added to the plugin. We updated Hebrew language file. We updated all functionality for wordpress 3.4.1.

= V2.30 =
The bug related to the forced double login in the admin panel was fixed. We updated all functionality for wordpress 3.4.1.

= V2.29 =
Hebrew language file is added to the plugin. We updated all functionality for wordpress 3.4.

= V2.28 =
Greek and Hindi language files are added to the plugin.

= V2.27 =
Swedish language file is added to the plugin.

= V2.26 =
Turkish language file is added to the plugin.

= V2.25 =
Finnish language file is added to the plugin.

= V2.24 =
Code that is used to connect styles and scripts is added to the plugin for correct SSL verification.

= V2.23 =
Norwegian language file is added to the plugin.

= V2.22 =
Danish language files are added to the plugin.

= V2.21 =
Czech language file is added to the plugin.

= V2.20 =
Ukrainian language file is added to the plugin.

= V2.19 =
Sintax errors were fixed. Please upgrade Captcha plugin immediately. Thank you

= V2.18 =
Farsi/Persian, Italian language files are added to the plugin. Session errors and 'undefined index' error were fixed.

= V2.17 =
Spain language files are added to the plugin.

= V2.16=
Polish language files are added to the plugin.

= V2.15 =
Brazilian Portuguese and French language files are added to the plugin.

= V2.14 =
German language files are added to the plugin.

= V2.13 =
Impossible math operation bug was fixed. Please upgrade the Captcha plugin immediately. Thank you

= V2.12 =
BWS plugin's menu section is fixed. The displaying of numerals was fixed in the Dutch language. Please upgrade the Captcha plugin. Thank you

= V2.11 =
Added Danish language files for plugin. Added all words in language file. Please upgrade the Captcha plugin immediately. Thank you

= V2.10 =
The bug of the captcha label section is fixed in this version. Please upgrade the Captcha plugin immediately. Thank you

= V2.09 =
+, -, * changed to HTML Entity.

= V2.08 =
Added Dutch language files for plugin.

= V2.07 =
Added language files for plugin.

= V2.06 =
BWS Plugin's menu section was fixed and right now it is consisted of 3 parts: activated, installed and recommended plugins.  The bug of position in the admin menu is fixed.

= V2.05 =
BWS Plugin's menu section was fixed and right now it is consisted of 2 parts: installed and recommended plugins. Icons displaying is fixed. Misalignment of math transaction is fixed.

= V2.04 =
In this version of plugin a bug of CAPTCHa reflection (before and after the comment form) was fixed. Please upgrade Captcha plugin immediately. Thank you. For more details information please see the FAQ

= V2.03 =
In this version of plugin a bug of CAPTCHa reflection was fixed in some of the themes for release of WordPress 3.0 and above. Please upgrade Captcha plugin immediately. Thank you

= V2.02 =
The bug of the captcha setting page link is fixed in this version. Please upgrade the Captcha plugin immediately. Thank you

= V2.01 =
Usability at the settings page of plugin was improved.

= V1.04 =
The bug of the captcha output is fixed in this version. Please upgrade the Captcha plugin immediately. Thank you

= V1.03 =
Ability to add BestWebSoft Contact Form plugin into a Captcha plugin from wp-admin via Settings panel.

= V1.02 =
Added "Settings", "FAQ", "Support" links to the plugin action page. Added links on the plugins page.

= V1.01 =
Mathematical actions choosing functionality and level of difficulty was implemented.
