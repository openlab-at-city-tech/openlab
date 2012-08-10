<?php
/*
    ========================= Weaver Admin Tab - Plus ==============
    Stub for Plus Promo
*/

if ( ! function_exists( 'weaver_plus_plugin' ) ) {
function weaver_pluspromo_admin() {

    $tdir = get_template_directory_uri();
    $img = $tdir . '/images/weaver/weaver-plus-logo.jpg';
    ?>
<div style="width:640px">
<h2 style="color: blue;">Important Notice - Check out the new version of Weaver: Weaver II</h2>
<p>
    Weaver 2.2.x is now even better! A totally new version, Weaver II, has been released. Weaver II has even more features,
    and includes complete automatic support for mobile devices. It also has a Pro version that has all the features of Weaver Plus,
    and many more. You can download <strong><a href="http://wordpress.org/extend/themes/weaver-ii" target="_blank">Weaver II at WordPress.org</a></strong>, or visit <strong><a href="http://weavertheme.com" target="_blank">WeaverTheme.com</a></strong>
    for more details.
</p>
<p>
    You should seriously consider switching
    to Weaver II - it is in active development, and includes many new, more up to date features that will make your site
    better. Because Weaver II requires a small conversion process, support for Weaver 2.2.x will continue, although no
    new features are planned.
</p>

<h3 style="color: blue;">Still available: the Weaver Plus Plugin!</h3>
<br />
<p>
<a title="Weaver Plus / Weaver II Pro Premium Upgrade for Weaver" href="http://pro.weavertheme.com" target="_blank">
<img class="alignleft" style="padding-right:20px;" title="Weaver Plus/Weaver II Pro" src="<?php echo $img; ?>" alt="Weaver Plus/Weaver II Pro" width="171" height="50" /></a>
<span style="color:blue; font-weight:bold;">Get Weaver Plus or Weaver II Pro Now!</span>
<br />
<strong>Visit <a title="Weaver Plus Premium Plugin for Weaver" href="http://pro.weavertheme.com" target="_blank">pro.WeaverTheme.com</a>
now for <em>Weaver Plus</em> for Weaver 2.2.x (still available), or learn about <em>Weaver II Pro</em> for the new version of Weaver (highly recommended).</strong>
</p>
<br />
<h2>Weaver Plus Feature List</h2>
<ol>
	<li><strong>Header Options</strong> - fine tune header settings; additional menu options</li>
	<li><strong>More Options</strong> - advanced customization options for posts and other site options</li>
	<li><strong>Font Control</strong> - use standard or Google fonts for any site element</li>
	<li><strong>Total CSS</strong> - customize the CSS of virtually every site element (advanced user feature)</li>
	<li><strong>Slider Menu</strong> - add sliding image menus to header, sidebars, or anywhere</li>
	<li><strong>Extra Menus</strong> - add extra text menus anywhere, including vertical sidebar menu widget</li>
	<li><strong>Link Buttons</strong> - add image link buttons anywhere with shortcode</li>
	<li><strong>Social Buttons</strong> - add social link buttons anywhere, including menu, pages, posts, and widget area</li>
	<li><strong>Header Gadgets</strong> - easily place images, text, links anywhere on your header</li>
	<li><strong>Widget Areas</strong> - add new widget areas anywhere - pages, posts, or advanced html areas</li>
	<li><strong>Search Form</strong> - add good looking HTML5 search form anywhere</li>
	<li><strong>Show Feed</strong> - include feeds from other sites formatted to match your site's own post styling</li>
	<li><strong>Popup Link</strong> - place a link that will popup a new window</li>
	<li><strong>Show/Hide Text</strong> - include content that the user can show or hide</li>
	<li><strong>Comment Policy</strong> - add comment policy statement after posts - or other content, including shortcodes</li>
	<li><strong>Shortcoder</strong> - define your own shortcodes - define any text, plus advanced HTML, scripts, even other shortcodes</li>
	<li><strong>PHP</strong> - include PHP code where needed</li>
	<li><strong>Plus Admin</strong> - selectively disable Weaver Plus features</li>
</ol>
<strong>Weaver II Pro includes these features, and more.</strong>

    </div>
<?php
}


function weaver_add_plus_tab($before, $after) {
    // Show the Plus Admin Panel

    echo($before."\n");
    weaver_pluspromo_admin();
    echo($after."\n");
 }

function weaver_add_plus_tab_title($before, $after) {
    // Add Admin Tab Title
    echo($before);
    echo('Weaver Plus');
    echo($after);
 }

// hook plugin
add_action('wvrx_add_plus_tab_title', 'weaver_add_plus_tab_title',10,2);
add_action('wvrx_add_plus_tab', 'weaver_add_plus_tab',10,2); /* plus option admin tab */
}
?>
