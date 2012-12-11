=== Genesis Connect for BuddyPress ===
Contributors: wpmuguru
Tags: genesis, buddypress, theme, template
Requires at least: 3.4
Tested up to: 3.4.2
Stable tag: 1.2

BuddyPress Theme Support for the Genesis Framework.

== Description ==

GenesisConnect for BuddyPress is designed to adds support for BuddyPress to any [Genesis](http://my.studiopress.com/themes/genesis/) child theme. Requires Genesis 1.8.1 or greater and BuddyPress 1.6 or greater. 

If used with 

- [Genesis Simple Sidebars](http://wordpress.org/extend/plugins/genesis-simple-sidebars/) you can set custom sidebars for both visitors & logged in users in your BuddyPress content areas
- [Genesis Simple Menus](http://wordpress.org/extend/plugins/genesis-simple-menus/) you can set custom secondary menu for both visitors & logged in users in your BuddyPress content areas

Includes a CSS support pack for the following official child themes

- [Agency](http://www.studiopress.com/themes/agency)
- [AgentPress](http://www.studiopress.com/themes/agentpress)
- [Amped](http://www.studiopress.com/themes/amped)
- [Balance](http://www.studiopress.com/themes/balance)
- [Bee Crafty](http://www.studiopress.com/themes/beecrafty)
- [Blissful](http://www.studiopress.com/themes/blissful)
- [Corporate](http://www.studiopress.com/themes/corporate)
- [Crystal](http://www.studiopress.com/themes/crystal)
- [Delicious](http://www.studiopress.com/themes/delicious)
- [Eleven40](http://www.studiopress.com/themes/eleven40)
- [Enterprise](http://www.studiopress.com/themes/enterprise)
- [Executive](http://www.studiopress.com/themes/executive)
- [Expose](http://www.studiopress.com/themes/expose)
- [Fabric](http://www.studiopress.com/themes/fabric)
- [Family Tree](http://www.studiopress.com/themes/familytree)
- [Focus](http://www.studiopress.com/themes/focus)
- [Freelance](http://www.studiopress.com/themes/freelance)
- [Going Green](http://www.studiopress.com/themes/goinggreen)
- [Landscape](http://www.studiopress.com/themes/landscape)
- [Lifestyle](http://www.studiopress.com/themes/lifestyle)
- [Magazine](http://www.studiopress.com/themes/magazine)
- [Manhattan](http://www.studiopress.com/themes/manhattan)
- [Midnight](http://www.studiopress.com/themes/midnight)
- [Metric](http://www.studiopress.com/themes/metric)
- [Minimum](http://www.studiopress.com/themes/minimum)
- [Mocha](http://www.studiopress.com/themes/mocha)
- [News](http://www.studiopress.com/themes/news)
- [Outreach](http://www.studiopress.com/themes/outreach)
- [Platinum](http://www.studiopress.com/themes/platinum)
- [Pretty Young Thing](http://www.studiopress.com/themes/pretty)
- [Prose](http://www.studiopress.com/themes/prose)
- [Serenity](http://www.studiopress.com/themes/serenity)
- [Sleek](http://www.studiopress.com/themes/sleek)
- [Streamline](http://www.studiopress.com/themes/streamline)

== Installation ==

If you installed the [BuddyPress Template Pack](http://wordpress.org/extend/plugins/bp-template-pack/):

1. Deactivate the BP Template Pack plugin & remove it from your install.
1. Remve the template pack templates from your child theme using FTP of your web host control panel. You will find the templates in folders corresponding to the BuddyPress components.

Installation:

1. Upload the entire `genesis-connect` folder to the `/wp-content/plugins/` directory
1. DO NOT change the name of the `genesis-connect` folder
1. Activate the plugin through the 'Plugins' menu in WordPress on your BuddyPress site - In a WordPress network, DO NOT network activate GenesisConnct for BuddyPress.

== CSS Customization ==

For all child themes with a GenesisConnect for BuddyPress CSS pack except Prose

1. Copy `genesis-connect/child-theme/your-child-theme/buddypress.css` to your child theme folder.
1. Add your customizations to the copy located in your child theme. This way you will not lose your customizations when you upgrade.

For Prose

1. GenesisConnect for BuddyPress automatically generates CSS for the BuddyPress content areas based on your designer settings.
1. If you have already completed your designer settings before activating GenesisConnect for BuddyPress, toggle you minify setting and save your designer settings. This will generate a new set of CSS files for your site including the CSS for your BuddyPress content areas.
1. If you want to customize outside of those settings use the Custom Code screen included in Prose.

For child themes without a GenesisConnect for BuddyPress CSS pack

1. Copy `genesis-connect/child-theme/genesis/buddypress.css` to your child theme folder.
1. This is a shell CSS file with all of the selectors you will need for most customization situations.
1. Add your customizations to the copy located in your child theme. This way you will not lose your customizations when you upgrade.

== Template Customization ==

1. All BuddyPress content theme templates have a corresponding template in the `genesis-connect/templates` folder.
1. To customize any template copy the template to the corresponding location in your child theme.
1. For example, to customize 

`genesis-connect/templates/activity/index.php`

1. Copy the template to `themes/your-child-theme/activity/index.php`.
1. Edit the copy of the template located in your child theme.

== Changelog ==

= 1.2 =
* Original version for BP 1.6.X.
