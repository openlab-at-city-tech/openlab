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
<h2 style="color: blue;">Add Exciting Features to Weaver
with the Weaver Plus Premium Plugin!</h2>
<br />
<p>
<a title="Weaver Plus Premium Plugin for Weaver" href="http://weaverplus.info" target="_blank">
<img class="alignleft" style="padding-right:20px;" title="Weaver Plus" src="<?php echo $img; ?>" alt="Weaver Plus" width="171" height="50" /></a>
<span style="color:blue; font-weight:bold;">Get Weaver Plus Now!</span>
<br /></br />
<strong>Visit <a title="Weaver Plus Premium Plugin for Weaver" href="http://weaverplus.info" target="_blank">WeaverPlus.info</a>
now to get your copy of <em>Weaver Plus</em>.</strong>
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
<h3>Why  Weaver Plus?</h3>
<p>The <em>Weaver</em> Word Press theme is one of the most flexible and easy to use free themes available on WordPress.org.
I've spent countless hours perfecting <em>Weaver</em>, and remain committed to keeping the core Weaver theme up to date, and full featured.</p>

<p><em>Weaver Plus</em> represents the next step. I've designed and added some extra features that will greatly enhance <em>Weaver's</em>
capabilities. <em>Weaver Plus</em> contains features normally found only in premium themes, and is available for a reasonable cost.</p>

<p>I hope that <em>Weaver</em> users will find the features useful enough to warrant the small expense. My goal is to get a small return
for all my development efforts, yet continue to provide a great free core theme.</p>

<p>Thanks so much for all the past support. I hope you find <em>Weaver Plus</em> to be a useful addition to your web design toolkit.</p>

<p><em>Bruce Wampler</em>, WP Weaver</p>
<p>
<a title="Weaver Plus Premium Plugin for Weaver" href="http://weaverplus.info" target="_blank">
<img class="alignleft" style="padding-right:20px;" title="Weaver Plus" src="<?php echo $img; ?>" alt="Weaver Plus" width="171" height="50" /></a>
<span style="color:blue; font-weight:bold;">Get Weaver Plus Now!</span>
<br /></br />
<strong>Visit <a title="Weaver Plus Premium Plugin for Weaver" href="http://weaverplus.info" target="_blank">WeaverPlus.info</a>
now to get your copy of <em>Weaver Plus</em>.</strong>
</p>
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
