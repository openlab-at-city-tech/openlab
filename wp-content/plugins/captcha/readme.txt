=== Captcha ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=10&product_id=13
Tags: captcha, match captcha, text captcha, spam, antispam, login, registration, comment, lost password, capcha, catcha, captha
Requires at least: 2.9
Tested up to: 3.5.2
Stable tag: 3.7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to implement super security captcha form into web forms.

== Description ==

The Captcha plugin allows you to protect your website from spam by means of math logic and you can use this captcha for login, registration, password recovery, comments forms. The Russian, German and Dutch languages are added.

<a href="http://wordpress.org/extend/plugins/captcha/faq/" target="_blank">FAQ</a>
<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Features =

* Display: You can use letters and numbers in captcha or just one of these two things - either letters or numbers.
* Actions: The basic math actions are used - add, subtract, multiply.
* Label: You can add a label to display captcha in the form.

= Translation =

* Arabic (ar_AR) (thanks to Albayan Design Hani Aladoli)
* Bangla (bn_BD) (thanks to <a href="mailto:mehdi.akram@gmail.com">SM Mehdi Akram</a>, www.shamokaldarpon.com)
* Brazilian Portuguese (pt_BR) (thanks to <a href="mailto:brenojac@gmail.com">Breno Jacinto</a>, www.iconis.org.br)
* Bulgarian (bg_BG) (thanks to <a href="mailto:paharaman@gmail.com">Nick</a>)
* Chinese (zh_CN) (thanks to Billy Jeans)
* Czech (cs_CZ) (thanks to Tomas Vesely, <a href="mailto:crysman@seznam.cz">Crysman</a>)
* Danish (da_DK) (thanks to Byrial Ole Jensed)
* Dutch (nl_NL) (thanks to <a href="mailto:byrial@vip.cybercity.dk">Bart Duineveld</a>)
* Estonian (et) (thanks to <a href="mailto:ahto2@moonsoftware.com">Ahto Tanner</a>)
* Greek (el) (thanks to Aris, www.paraxeno.net)
* Farsi/Persian (fa_IR) (thanks to <a href="mailto:info[at]mpspace[dot]zio[dot]ir">Meysam Parvizi</a>, www.mpspace.zio.ir)
* Finnish (fi) (thanks to Mikko Sederholm)
* French (fr_FR) (thanks to Martel Benjamin, <a href="mailto:lcapronnier@yahoo.com">Capronnier luc</a>)
* German (de_DE) (thanks to Thomas Hartung)
* Hebrew (he_IL) (thanks to Sagive SEO)
* Hindi (hi_IN) (thanks to <a href="mailto:ash.pr@outshinesolutions.com">Outshine Solutions</a>, www.outshinesolutions.com)
* Hungarian (hu_HU) (thanks to Bőm Tamás)
* Japanese (ja) (thanks to Foken)
* Italian (it_IT) (thanks to Gianluca Di Carlo)
* Latvian (lv) (thanks to <a href="mailto:juris.o@gmail.com">Juris O</a>)
* Lithuanian (lt_LT) (thanks to <a href="mailto:arnas.metal@gmail.com">Arnas</a>)
* Norwegian (nb_NO) (thanks to Tore Hjartland)
* Polish (pl_PL) (thanks to Krzysztof Opuchlik)
* Romanian (ro_RO) (thanks to Ciprian)
* Russian (ru_RU)
* Serbian (sr_RS) (thanks to Radovan Georgijevic)
* Slovak (sk_SK) (thanks to Branco Radenovich)
* Spain (es_ES) (thanks to Iván García Cubero)
* Swedish (sv_SE) (thanks to Christer Rönningborg, <a href="mailto:blittan@xbmc.org">Blittan</a>)
* Turkish (tr_TR) (thanks to Can Atasever, www.candanblog.com)
* Ukrainian (uk_UA) (thanks to Oleg Bondarenko)
* Vietnamese (vi_VN) (thanks to NDT Solutions)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugins, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.
Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then. 
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload the `captcha` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in 'Settings', 'Captcha'.

== Frequently Asked Questions ==

= How to change a captcha label =

You should go to the Settings page and change the value in the 'CAPTCHA label in the form' field.

= During the settings saving I get the error: 'Please select one point in the blocks "Math actions" and "Complexity Level"'. What is this? =

For stable work of the Captcha plugin you should select at least one item in the 'Math actions' block and select 'Complexity Level' on the Settings page, because math expression should consist of at least 1 math sign and parts of math expression should be displayed like words or numbers or both of them.

= Missing CAPTCHA on the comment form? = 

You might have a theme where comments.php is not coded properly. 

Wopdpress version matters. 

(WP2 series) Your theme must have a tag `<?php do_action('comment_form', $post->ID); ?>` inside the file `/wp-content/themes/[your_theme]/comments.php`. 
Most WP2 themes already have it. The best place to put this tag is before the comment textarea, you can move it up if it is below the comment textarea.

(WP3 series) WP3 has a new function comment_form inside of `/wp-includes/comment-template.php`. 
Your theme is probably not up-to-date to call that function from comments.php.
WP3 theme does not need the code line `do_action('comment_form'`... inside of `/wp-content/themes/[your_theme]/comments.php`.
Instead it uses a new function call inside of comments.php: `<?php comment_form(); ?>`
If you have WP3 and captcha is still missing, make sure your theme has `<?php comment_form(); ?>`
inside of `/wp-content/themes/[your_theme]/comments.php` (please check the Twenty Ten theme's comments.php for proper example)

= How to use the other language files with CAPTCHA? = 

Here is an example for German language files.

1. In order to use another language for WordPress it is necessary to set a WordPress version to the required language and in the configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` you should enter `define('WPLANG', 'de_DE');`. If everything is done properly the admin panel will be in German.

2. Make sure the files `de_DE.po` and `de_DE.mo` are present in the plugin (the folder "Languages" in the plugin root).

3. If there are no such files you should copy the other files from this folder (for example, for Russian or Italian) and rename them (you should write `de_DE` instead of `ru_RU` in both files).

4. The files can be edited with the help of the program Poedit - http://www.poedit.net/download.php - please download this program, install it, open the file using this program (the required language file) and for each line in English you should write translation in German.

5. If everything is done properly all lines will be in German in the admin panel and in the front-end.

= I would like to add Captcha to the custom form on my website. How can I do this? =

1. Install the Captcha plugin and activate it.
2. Open the file with the form (where you would like to add captcha to).
3. Find a place to insert the code for the captcha output.
4. Insert the necessary lines: 

`if( function_exists( 'cptch_display_captcha_custom' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha_custom() } ;`

If the form is HTML you should insert the line with the PHP tags:

`<?php if( function_exists( 'cptch_display_captcha_custom' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha_custom(); } ?>`

5. Then you should add the lines to the function of the entered data checking  

`if( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) echo "Please complete the CAPTCHA."`
or
`<?php if( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) echo "Please complete the CAPTCHA." ?>`
You could add this line to the variable and display this variable in the required place instead of `echo "Please complete the CAPTCHA."`. If there is a variable (responsible for the errors output) in the check function, this phrase can be added to this variable. If the function returns 'true', it means that you have entered captcha properly. In all other cases the function will return 'false'.

== Screenshots ==

1. Captcha Settings page.
2. Comments form with Captcha.
3. Registration form with Captcha.
4. Lost password form with Captcha.
5. Login form with Captcha.

== Changelog ==

= V3.7.4 - 24.07.2013 =
* Bugfix : Added html-blocks and attributes in captcha displaying.
* Update : The Czech language file is updated in the plugin.
* Update : The Brazilian Portuguese language file is updated in the plugin.
* Update : The Swedish language file is updated in the plugin.

= V3.7.3 - 18.07.2013 =
* NEW : Added an ability to view and send system information by mail.

= V3.7.2 - 09.07.2013 =
* NEW : The Bangla language file is added to the plugin.
* Update : The French language file is updated in the plugin.
* Update : We updated all functionality for wordpress 3.5.2.

= V3.7.1 - 27.06.2013 =
* NEW : The Latvian language file is added to the plugin.

= V3.7 - 21.06.2013 =
* NEW : Ability to use Captcha with Contact Form Pro.

= V3.6 - 03.06.2013 =
* Update : BWS plugins section is updated.

= V3.5 - 07.05.2013 =
* Update : The Bulgarian language file is updated in the plugin. 
* Update : The Brazilian Portuguese language file is updated in the plugin.

= V3.4 - 18.04.2013 =
* Update : The French language file is updated in the plugin.

= V3.3 - 08.04.2013 =
* Update : The English language is updated in the plugin.

= V3.2 - 22.03.2013 =
* Bugfix : The bug related to add Captcha in Contact Form for multisiting is fixed.

= V3.1 - 25.02.2013 =
* NEW : The Bulgarian language file is added to the plugin.

= V3.0 - 08.01.2013 =
* Bugfix : Display bug is fixed.

= V2.4.4 - 31.01.2013 =
* Bugfix : The admin menu bugs are fixed.

= V2.4.3 - 30.01.2013 =
* NEW : The Estonian language file is added to the plugin.

= V2.4.2 - 28.01.2013 =
* NEW : The Lithuanian language file is added to the plugin.
* Update : We updated all functionality for wordpress 3.5.1.

= V2.4.1 - 02.01.2013 =
* Bugfix : call_user_func_array() bug is fixed. 

= V2.4 - 21.12.2012 =
* NEW : Romanian and Serbian and Slovak language files are added to the plugin.
* Update : We updated the coding logic of Captcha.
* Update : We updated all functionality for wordpress 3.5.

= V2.34 - 08.10.2012 =
* NEW : Chinese and Greek language files are added to the plugin.

= V2.33 - 25.07.2012 =
* Bugfix : Change settings bug was fixed. 

= V2.32 - 24.07.2012 =
* NEW : Arabic, Hungarian, Japanese language files are added to the plugin.
* Bugfix : Cross Site Request Forgery bug was fixed. 

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

= V3.7.4 =
Added html-blocks and attributes in captcha displaying. The Czech language file is updated in the plugin. The Brazilian Portuguese language file is updated in the plugin. The Swedish language file is updated in the plugin.

= V3.7.3 =
Added an ability to view and send system information by mail.

= V3.7.2 =
The Bangla language file is added to the plugin. The French language file is updated in the plugin. We updated all functionality for wordpress 3.5.2

= V3.7.1 =
The Latvian language file is added to the plugin.

= V3.7 =
Ability to use Captcha with Contact Form Pro

= V3.6 =
BWS plugins section is updated.

= V3.5 =
The Bulgarian language file is updated in the plugin. The Brazilian Portuguese language file is updated in the plugin.

= V3.4 =
The French language file is updated in the plugin.

= V3.3 =
The English language is updated in the plugin.

= V3.2 =
The bug related to add Captcha in Contact Form for multisiting was fixed.

= V3.1 =
The Bulgarian language file ix added to the plugin.

= V3.0 =
Display bug was fixed.

= V2.4.4 =
Bugs in admin menu is fixed.

= V2.4.3 =
The Estonian language file is added to the plugin.

= V2.4.2 =
The Lithuanian language file was is to the plugin. We updated all functionality for wordpress 3.5.1.

= V2.4.1 =
call_user_func_array() bug was fixed. 

= V2.4 =
Romanian and Serbian and Slovak language files are added to the plugin. We updated the coding logic of Captcha. We updated all functionality for wordpress 3.5.

= V2.34 =
Chinese and Greek language files are added to the plugin.

= V2.33 =
Change settings bug was fixed. 

= V2.32 =
Arabic, Hungarian, Japanese language files are added to the plugin. Cross Site Request Forgery bug was fixed. 

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
