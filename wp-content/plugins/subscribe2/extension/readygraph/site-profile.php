<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   ReadyGraph
 * @author    dan@readygraph.com
 * @license   GPL-2.0+
 * @link      http://www.readygraph.com
 * @copyright 2014 Your Name or Company Name
 */
 
include("header.php");

	if (!get_option('readygraph_access_token') || strlen(get_option('readygraph_access_token')) <= 0) {
	//redirect to main page
	$current_url = explode("&", $_SERVER['REQUEST_URI']); 
	echo '<script>window.location.replace("'.$current_url[0].'");</script>';
	}
	else {
	if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
	if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
	if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
	if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);
	if (isset($_POST["sitedesceditor"])) update_option('readygraph_site_description', $_POST["sitedesceditor"]);
	if (isset($_POST["site_profile_name"])) update_option('readygraph_site_name', $_POST["site_profile_name"]);
	if (isset($_POST["site_profile_url"])) update_option('readygraph_site_url', $_POST["site_profile_url"]);
	if (isset($_POST["site_category"])) update_option('readygraph_site_category', $_POST["site_category"]);
	if (isset($_POST["site_language"])) update_option('readygraph_site_language', $_POST["site_language"]);
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		s2_siteprofile_sync();
	}
	}
	if(isset($_GET["readygraph_plan"]) && $_GET["readygraph_plan"] != ""){update_option('readygraph_plan',$_GET["readygraph_plan"]);}
	
 ?>	


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo plugins_url( 'assets/js/jquery.dependent-selects.js', __FILE__ ) ?>"></script>
<script type="text/javascript" charset="utf-8">
$(function(){
  jQuery('.dependent-demo2').dependentSelects({
    separator: '||',
	placeholderOption: '-- Please Select --'
  });        
})
</script>
<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">
<input type="hidden" name="readygraph_site_category" value="<?php echo get_option('readygraph_site_category', '') ?>">
<input type="hidden" name="readygraph_site_language" value="<?php echo get_option('readygraph_site_language', '') ?>">
<div><div><a href="#">Basic Settings</a> > Site Profile</div>
	<?php if(get_option('readygraph_upgrade_notice') && get_option('readygraph_upgrade_notice') == "true") { ?><div class="upgrade-notice"><div class="aa_close"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&readygraph_upgrade_notice=dismiss"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/dialog_close.png"></a></div>
	<div class="upgrade-notice-text">Want to grow your users even faster? Try <a href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', ''); ?>" target="_blank">ReadyGraph Premium</a> for free.</div>
	</div>
	<?php } ?>
			<h3 style="font-weight: normal; text-align: center;">Be sure your site profile is accurate!</h3>
			<h4 style="font-weight: normal; text-align: center;">This content is used in your site's features and emails</h4>
			<div style="margin: 0 5%;">
			<!--<div style="display: block; margin: 10px;"><label for="site_profile_image_upload" style="width: 120px">Site Icon: </label> <input type="file" name="site_profile_image_upload" id="site_profile_image_upload"  multiple="false" style="display: inline; margin: 0 0 0 120px" /></div>-->
			<div style="display: block; margin: 10px;"><label for="site_profile_name" style="width: 120px">Site Name:</label><input type="text" name="site_profile_name" id="site_profile_name" value="<?php echo get_option('readygraph_site_name');?>" style="display: inline; margin: 0 0 0 120px" /></div>
			<div style="display: block; margin: 10px;"><label for="site_profile_url" style="width: 120px">Site URL:</label><input type="text" name="site_profile_url" id="site_profile_url" value="<?php echo get_option('readygraph_site_url');?>" style="display: inline; margin: 0 0 0 120px" /></div>
			<div class="site_category" style="display: block; margin: 10px;"><label for="site_category" style="width: 120px">Site Category:</label>
				<select name="site_category" class="dependent-demo2" style="display: inline; margin: 0 0 0 116px" >
					<option></option>
					<option value="animation-comics">Arts and Entertainment||animation and comics</option>
					<option value="architecture">Arts and Entertainment||architecture</option>
					<option value="awards">Arts and Entertainment||awards</option>
					<option value="celebritities-entertainment">Arts and Entertainment||celebritities and entertainment</option>
					<option value="fashion-modeling">Arts and Entertainment||fashion and modeling</option>
					<option value="humor">Arts and Entertainment||humor</option>
					<option value="movies">Arts and Entertainment||movies</option>
					<option value="music-audio">Arts and Entertainment||music and audio</option>
					<option value="performing-arts">Arts and Entertainment||performing arts</option>
					<option value="photography">Arts and Entertainment||photography</option>
					<option value="TV-video">Arts and Entertainment||TV and video</option>
					<option value="web-design">Arts and Entertainment||web design</option>

					<option value="automotive-industry">Autos and Vehicles||automotive industry</option>
					<option value="automotive-news">Autos and Vehicles||automotive news</option>
					<option value="aviation">Autos and Vehicles||aviation</option>
					<option value="boating">Autos and Vehicles||boating</option>
					<option value="car-buying">Autos and Vehicles||car buying</option>
					<option value="car-rentals">Autos and Vehicles||car rentals</option>
					<option value="makes-model">Autos and Vehicles||makes and models</option>
					<option value="motorcycles">Autos and Vehicles||motorcycles</option>
					<option value="motorsports">Autos and Vehicles||motorsports</option>
					<option value="trains-railroads">Autos and Vehicles||trains and railroads</option>

					<option value="beauty">Beauty and Fitness||Beauty</option>
					<option value="bodyart">Beauty and Fitness||bodyart</option>
					<option value="cosmetics">Beauty and Fitness||cosmetics</option>
					<option value="fitness">Beauty and Fitness||fitness</option>
					<option value="hair">Beauty and Fitness||hair</option>
					<option value="skin-care">Beauty and Fitness||skin care</option>
					<option value="weight-loss">Beauty and Fitness||weight loss</option>

					<option value="book-retailers">Books and Literature||book retailers</option>
					<option value="E-books">Books and Literature||E books</option>
					<option value="folklore">Books and Literature||folklore</option>
					<option value="guides-reviews">Books and Literature||guides and reviews</option>
	
					<option value="accounting">Business and Industry||accounting</option>
					<option value="aerospace-defense">Business and Industry||aerospace and defense</option>
					<option value="agriculture-forestry">Business and Industry||agriculture and forestry</option>
					<option value="associations">Business and Industry||associations</option>
					<option value="biotechnology-pharmaceutical">Business and Industry||biotechnology and pharmaceutical</option>
					<option value="business-services">Business and Industry||business services</option>
					<option value="chemicals">Business and Industry||chemicals</option>
					<option value="e-commerce">Business and Industry||E commerce</option>
					<option value="energy">Business and Industry||energy</option>
					<option value="industrial-goods-services">Business and Industry||industrial goods and services</option>
					<option value="marketing-advertising">Business and Industry||marketing and advertising</option>
					<option value="metals-mining">Business and Industry||metals and mining</option>
					<option value="publishing-printing">Business and Industry||publishing and printing</option>
					<option value="real-estate">Business and Industry||real estate</option>
					<option value="textiles-nonwovens">Business and Industry||textiles and nonwovens</option>
					<option value="transportation-logistics">Business and Industry||transportation and logistics</option>
					<option value="wholesale-trade">Business and Industry||wholesale trade</option>
					
					<option value="business-training">Career and Education||business training</option>
					<option value="education">Career and Education||education</option>
					<option value="human-resources">Career and Education||human resources</option>
					<option value="jobs-employment">Career and Education||jobs and employment</option>
					<option value="universities-colleges">Career and Education||universities and colleges</option>
					
					<option value="computer hardware">Computer and Electronics||computer hardware</option>
					<option value="computer security">Computer and Electronics||computer security</option>
					<option value="consumer electronics">Computer and Electronics||consumer electronics</option>
					<option value="graphics and multimedia tools">Computer and Electronics||graphics and multimedia tools</option>
					<option value="mobile computing">Computer and Electronics||mobile computing</option>
					<option value="networking">Computer and Electronics||networking</option>
					<option value="programming">Computer and Electronics||programming</option>
					<option value="software">Computer and Electronics||software</option>
		
					<option value="banking">Finance||banking</option>
					<option value="credit-loans-mortgages">Finance||credit, loans and mortgages</option>
					<option value="financial-management">Finance||financial management</option>
					<option value="grants-scholarships">Finance||grants and scholarships</option>
					<option value="insurance">Finance||insurance</option>
					<option value="investing">Finance||investing</option>
			
					<option value="beverages">Food and Beverage||beverages</option>
					<option value="catering">Food and Beverage||catering</option>
					<option value="cooking-recipes">Food and Beverage||cooking and recipes</option>
					<option value="food-grocery-retailers">Food and Beverage||food and grocery retailers</option>
					<option value="vegitarian-vegan">Food and Beverage||vegetarian and vegan</option>
					
					<option value="bingo">Gambling||bingo</option>
					<option value="casinos">Gambling||casinos</option>
					<option value="lottery">Gambling||lottery</option>
					<option value="poker">Gambling||poker</option>
					<option value="regulation-organizations">Gambling||regulation and organizations</option>
					<option value="sports">Gambling||sports</option>
				
					<option value="board-card-games">Games||board and card games</option>
					<option value="miniatures">Games||miniatures</option>
					<option value="online">Games||online</option>
					<option value="puzzles-brainteasers">Games||puzzles and brainteasers</option>
					<option value="roleplaying">Games||roleplaying</option>
					<option value="video-games">Games||video games</option>
				
					<option value="addictions">Health||addictions</option>
					<option value="alternative-natural-medicine">Health||alternative natural medicine</option>
					<option value="child-health">Health||child health</option>
					<option value="conditions-diseases">Health||conditions and diseases</option>
					<option value="education-resources">Health||education and resources</option>
					<option value="healthcare-industry">Health||healthcare industry</option>
					<option value="medicine">Health||medicine</option>
					<option value="men-health">Health||men's health</option>
					<option value="mental-health">Health||mental health</option>
					<option value="nutrition">Health||nutrition</option>
					<option value="pharmacy">Health||pharmacy</option>
					<option value="products-shopping">Health||products and shopping</option>
					<option value="public-health-safety">Health||public health and safety</option>
					<option value="reproductive-health">Health||reproductive health</option>

					<option value="gardening">Home and Garden||gardening</option>
					<option value="home-improvement">Home and Garden||home improvement</option>
					<option value="interior-decor">Home and Garden||interior décor</option>
					<option value="moving-relocating">Home and Garden||moving and relocating</option>
					<option value="nursery-playroom">Home and Garden||nursery and playroom</option>

					<option value="ad-network">Internet and Telecom||ad network</option>
					<option value="chat-forums">Internet and Telecom||chat and forums</option>
					<option value="domain-names-register">Internet and Telecom||domain names and register</option>
					<option value="email">Internet and Telecom||email</option>
					<option value="file-sharing">Internet and Telecom||file sharing</option>
					<option value="search-engine">Internet and Telecom||search engine</option>
					<option value="social-network">Internet and Telecom||social network</option>
					<option value="telecommunications">Internet and Telecom||telecommunications</option>
					<option value="web-hosting">Internet and Telecom||web hosting</option>
					<option value="content">Internet and Telecom||content</option>

					<option value="government">Law and Government||government</option>
					<option value="immigration-visas">Law and Government||immigration and visas</option>
					<option value="law">Law and Government||law</option>
					<option value="military-defense">Law and Government||military and defense</option>
		
					<option value="news">News and Media||news</option>
					<option value="business-news">News and Media||business news</option>
					<option value="college-university-press">News and Media||college and university press</option>
					<option value="magazines-ezines">News and Media||magazines and E-zines</option>
					<option value="newspapers">News and Media||newspapers</option>
					<option value="sports-news">News and Media||sports news</option>
					<option value="technology-news">News and Media||technology news</option>
					<option value="weather">News and Media||weather</option>
	
					<option value="crime-prosecution">People and Society||crime and prosecution</option>
					<option value="death">People and Society||death</option>
					<option value="disabled and special needs">People and Society||disabled and special needs</option>
					<option value="gay-lesbian-bisexual">People and Society||gay, lesbian, and bisexual</option>
					<option value="genealogy">People and Society||genealogy</option>
					<option value="history">People and Society||history</option>
					<option value="holidays">People and Society||holidays</option>
					<option value="philanthropy">People and Society||philanthropy</option>
					<option value="philosophy">People and Society||philosophy</option>
					<option value="relationships-dating">People and Society||relationships and dating</option>
					<option value="religion-spirituality">People and Society||religion and spirituality</option>
					<option value="women-interests">People and Society||women's interests</option>
					<option value="personal-blog">People and Society||personal blog</option>
				
					<option value="animal-products-services">Pets and Animals||animal products and services</option>
					<option value="birds">Pets and Animals||birds</option>
					<option value="fish-aquaria">Pets and Animals||fish and aquaria</option>
					<option value="horses">Pets and Animals||horses</option>
					<option value="pets">Pets and Animals||pets</option>
			
					<option value="antiques">Recreation and Hobbies||antiques</option>
					<option value="camps">Recreation and Hobbies||camps</option>
					<option value="climbing">Recreation and Hobbies||climbing</option>
					<option value="collecting">Recreation and Hobbies||collecting</option>
					<option value="crafts">Recreation and Hobbies||crafts</option>
					<option value="models">Recreation and Hobbies||models</option>
					<option value="nudism">Recreation and Hobbies||nudism</option>
					<option value="outdoors">Recreation and Hobbies||outdoors</option>
					<option value="scouting">Recreation and Hobbies||scouting</option>
					<option value="theme-parks">Recreation and Hobbies||theme parks</option>
					<option value="tobacco">Recreation and Hobbies||tobacco</option>
				
					<option value="archives">Reference||archives</option>
					<option value="ask-expert">Reference||ask an expert</option>
					<option value="dictionary-encyclopaedia">Reference||dictionary and encyclopaedia</option>
					<option value="directories">Reference||directories</option>
					<option value="libraries-museums">Reference||libraries and museums</option>
					<option value="maps">Reference||maps</option>
					
					<option value="agriculture">Science||agriculture</option>
					<option value="astronomy">Science||astronomy</option>
					<option value="biology">Science||biology</option>
					<option value="chemistry">Science||chemistry</option>
					<option value="earth-sciences">Science||earth sciences</option>
					<option value="educational-resources">Science||educational resources</option>
					<option value="engineering-technology">Science||engineering and technology</option>
					<option value="environment">Science||environment</option>
					<option value="instruments-supplies">Science||instruments and supplies</option>
					<option value="math">Science||math</option>
					<option value="physics">Science||physics</option>
					<option value="social-sciences">Science||social sciences</option>
		
					<option value="antiques-collectibles">Shopping||antiques and collectibles</option>
					<option value="auctions">Shopping||auctions</option>
					<option value="children">Shopping||children</option>
					<option value="classifieds">Shopping||classifieds</option>
					<option value="clothing">Shopping||clothing</option>
					<option value="consumer-electronics">Shopping||consumer electronics</option>
					<option value="coupons">Shopping||coupons</option>
					<option value="ethnic-regional">Shopping||ethnic and regional</option>
					<option value="flowers">Shopping||flowers</option>
					<option value="furniture">Shopping||furniture</option>
					<option value="general-merchandise">Shopping||general merchandise</option>
					<option value="gifts">Shopping||gifts</option>
					<option value="home-garden">Shopping||home and garden</option>
					<option value="jewellery">Shopping||jewellery</option>
					<option value="music">Shopping||music</option>
					<option value="office-products">Shopping||office products</option>
					<option value="publications">Shopping||publications</option>
					<option value="sports">Shopping||sports</option>
					<option value="weddings">Shopping||weddings</option>

					<option value="baseball">Sports||baseball</option>
					<option value="basketball">Sports||basketball</option>
					<option value="coxing">Sports||coxing</option>
					<option value="cycling-biking">Sports||cycling and biking</option>
					<option value="equestrian">Sports||equestrian</option>
					<option value="extreme-sports">Sports||extreme sports</option>
					<option value="fantasy-sports">Sports||fantasy sports</option>					
					<option value="fishing">Sports||fishing</option>
					<option value="football">Sports||football</option>
					<option value="golf">Sports||golf</option>
					<option value="martial-arts">Sports||martial arts</option>
					<option value="rugby">Sports||rugby</option>					
					<option value="running">Sports||running</option>
					<option value="soccer">Sports||soccer</option>
					<option value="tennis">Sports||tennis</option>
					<option value="volleyball">Sports||volleyball</option>
					<option value="water-sports">Sports||water sports</option>				
					<option value="winter-sports">Sports||winter sports</option>
		
					<option value="accommodation-hotels">Travel||accommodation and hotels</option>
					<option value="airlines-airports">Travel||airlines and airports</option>
					<option value="roads-highways">Travel||roads and highways</option>
					<option value="tourism">Travel||tourism</option>
					
					<option value="adult">Adult</option>
					
					
					
				</select></div>
			<div class="site_language" style="display: block; margin: 10px;"><label for="site_language" style="width: 120px">Choose your Site language:</label>
<select name="site_language" id="site_language" style="display: inline; margin: 0 0 0 116px" ><option value="af">Afrikaans</option> <option value="am">Amharic - ‪አማርኛ‬</option> <option value="ar">Arabic - ‫العربية‬</option> <option value="eu">Basque - ‪euskara‬</option> <option value="bn">Bengali - ‪বাংলা‬</option> <option value="bg">Bulgarian - ‪български‬</option> <option value="ca">Catalan - ‪català‬</option> <option value="zh-HK">Chinese (Hong Kong) - ‪中文（香港）‬</option> <option value="zh-CN">Chinese (Simplified) - ‪简体中文‬</option> <option value="zh-TW">Chinese (Traditional) - ‪繁體中文‬</option> <option value="hr">Croatian - ‪Hrvatski‬</option> <option value="cs">Czech - ‪Čeština‬</option> <option value="da">Danish - ‪Dansk‬</option> <option value="nl">Dutch - ‪Nederlands‬</option> <option value="en-GB">English (United Kingdom)</option> <option value="en">English (United States)</option> <option value="et">Estonian - ‪eesti‬</option> <option value="fil">Filipino</option> <option value="fi">Finnish - ‪Suomi‬</option> <option value="fr-CA">French (Canada) - ‪Français (Canada)‬</option> <option value="fr">French (France) - ‪Français (France)‬</option> <option value="gl">Galician - ‪galego‬</option> <option value="de">German - ‪Deutsch‬</option> <option value="el">Greek - ‪Ελληνικά‬</option> <option value="gu">Gujarati - ‪ગુજરાતી‬</option> <option value="iw">Hebrew - ‫עברית‬</option> <option value="hi">Hindi - ‪हिन्दी‬</option> <option value="hu">Hungarian - ‪magyar‬</option> <option value="is">Icelandic - ‪íslenska‬</option> <option value="id">Indonesian - ‪Bahasa Indonesia‬</option> <option value="it">Italian - ‪Italiano‬</option> <option value="ja">Japanese - ‪日本語‬</option> <option value="kn">Kannada - ‪ಕನ್ನಡ‬</option> <option value="ko">Korean - ‪한국어‬</option> <option value="lv">Latvian - ‪latviešu‬</option> <option value="lt">Lithuanian - ‪lietuvių‬</option> <option value="ms">Malay - ‪Bahasa Melayu‬</option> <option value="ml">Malayalam - ‪മലയാളം‬</option> <option value="mr">Marathi - ‪मराठी‬</option> <option value="no">Norwegian - ‪norsk‬</option> <option value="or">Oriya - ‪ଓଡ଼ିଆ‬</option> <option value="fa">Persian - ‫فارسی‬</option> <option value="pl">Polish - ‪polski‬</option> <option value="pt-BR">Portuguese (Brazil) - ‪Português (Brasil)‬</option> <option value="pt-PT">Portuguese (Portugal) - ‪português (Portugal)‬</option> <option value="ro">Romanian - ‪română‬</option> <option value="ru">Russian - ‪Русский‬</option> <option value="sr">Serbian - ‪српски‬</option> <option value="sk">Slovak - ‪Slovenčina‬</option> <option value="sl">Slovenian - ‪slovenščina‬</option> <option value="es-419">Spanish (Latin America) - ‪Español (Latinoamérica)‬</option> <option value="es">Spanish (Spain) - ‪Español (España)‬</option> <option value="sw">Swahili - ‪Kiswahili‬</option> <option value="sv">Swedish - ‪Svenska‬</option> <option value="ta">Tamil - ‪தமிழ்‬</option> <option value="te">Telugu - ‪తెలుగు‬</option> <option value="th">Thai - ‪ไทย‬</option> <option value="tr">Turkish - ‪Türkçe‬</option> <option value="uk">Ukrainian - ‪Українська‬</option> <option value="ur">Urdu - ‫اردو‬</option> <option value="vi">Vietnamese - ‪Tiếng Việt‬</option> <option value="zu">Zulu - ‪isiZulu‬</option></select></p>
			<div style="display: block; width:50%">
			<label for="sitedesceditor" style="display: block; width: 120px">Site Description:</label><div style="margin-left: 240px;margin-top: -20px"><?php /**
 
/**
 * Basic syntax
 */
$content = get_option('readygraph_site_description');
$editor_id = 'sitedesceditor';
$settings = array(
    'textarea_rows' => 5,
	'media_buttons' => false,
    'teeny' => true,
    'quicktags' => false
);
wp_editor( $content, $editor_id, $settings );
 ?>
 </div>
 </div>
 </div>
			<div class="save-changes"><?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-next" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=signup-popup" style="float: right;margin: 15px">Save Changes & Next</button><?php } ?>
			<button type="submit" class="btn btn-large btn-warning save" formaction="#" style="float: right;margin: 15px">Save Changes</button>
			<?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-previous" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=basic-settings&tutorial=true" style="float: right;margin: 15px">Previous</button> <?php } ?>
			</div>
	</div>
</div>
</form>

<script type="text/javascript" charset="utf-8">
var $ = jQuery;
var category = $('[name="readygraph_site_category"]').val();
if (category != ""){
	$('#site_category').val(category);
}
var language = $('[name="readygraph_site_language"]').val();
if (language != ""){
	$('#site_language').val(language);
}
</script>
<?php include("footer.php"); ?>