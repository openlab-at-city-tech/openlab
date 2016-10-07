<?php function weblizar_scripts()
        {      //google font style
				wp_enqueue_style('OpenSans', 'https://fonts.googleapis.com/css?family=Rock+Salt|Neucha|Sans+Serif|Indie+Flower|Shadows+Into+Light|Dancing+Script|Kaushan+Script|Tangerine|Pinyon+Script|Great+Vibes|Bad+Script|Calligraffitti|Homemade+Apple|Allura|Megrim|Nothing+You+Could+Do|Fredericka+the+Great|Rochester|Arizonia|Astloch|Bilbo|Cedarville+Cursive|Clicker+Script|Dawning+of+a+New+Day|Ewert|Felipa|Give+You+Glory|Italianno|Jim+Nightshade|Kristi|La+Belle+Aurore|Meddon|Montez|Mr+Bedfort|Over+the+Rainbow|Princess+Sofia|Reenie+Beanie|Ruthie|Sacramento|Seaweed+Script|Stalemate|Trade+Winds|UnifrakturMaguntia|Waiting+for+the+Sunrise|Yesteryear|Zeyada|Warnes|Abril+Fatface|Advent+Pro|Aldrich|Alex+Brush|Amatic+SC|Antic+Slab|Candal');
				
                wp_enqueue_style('bootstrap', get_template_directory_uri() .'/css/bootstrap.css');
                wp_enqueue_style('default', get_template_directory_uri() . '/css/default.css');
                wp_enqueue_style('enigma-theme', get_template_directory_uri() . '/css/enigma-theme.css');
                wp_enqueue_style('media-responsive', get_template_directory_uri() . '/css/media-responsive.css');
                wp_enqueue_style('animations', get_template_directory_uri() . '/css/animations.css');
                wp_enqueue_style('theme-animtae', get_template_directory_uri() . '/css/theme-animtae.css');
                wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome-4.3.0/css/font-awesome.css');              
                wp_enqueue_style('OpenSansRegular','//fonts.googleapis.com/css?family=Open+Sans');
                wp_enqueue_style('OpenSansBold','//fonts.googleapis.com/css?family=Open+Sans:700');
                wp_enqueue_style('OpenSansSemiBold','//fonts.googleapis.com/css?family=Open+Sans:600');
                wp_enqueue_style('RobotoRegular','//fonts.googleapis.com/css?family=Roboto');
                wp_enqueue_style('RobotoBold','//fonts.googleapis.com/css?family=Roboto:700');
                wp_enqueue_style('RalewaySemiBold','//fonts.googleapis.com/css?family=Raleway:600');
                wp_enqueue_style('Courgette','//fonts.googleapis.com/css?family=Courgette');
                
                // Js
                wp_enqueue_script('menu', get_template_directory_uri() .'/js/menu.js', array('jquery'));
                wp_enqueue_script('bootstrap-js', get_template_directory_uri() .'/js/bootstrap.js');
                wp_enqueue_script('enigma-theme-script', get_template_directory_uri() .'/js/enigma_theme_script.js');
                if(is_front_page()){
                /*Carofredsul Slides*/
                wp_enqueue_script('jquery.carouFredSel', get_template_directory_uri() .'/js/carouFredSel-6.2.1/jquery.carouFredSel-6.2.1.js');
                wp_enqueue_script('carouFredSel-element', get_template_directory_uri() .'/js/carouFredSel-6.2.1/caroufredsel-element.js');
                /*PhotoBox JS*/
                wp_enqueue_script('photobox-js', get_template_directory_uri() .'/js/jquery.photobox.js');
                wp_enqueue_style('photobox', get_template_directory_uri() . '/css/photobox.css');
                //Footer JS//
				wp_enqueue_script('enigma-footer-script', get_template_directory_uri() .'/js/enigma-footer-script.js','','',true);
				wp_enqueue_script('waypoints', get_template_directory_uri() .'/js/waypoints.js','','',true);				
				wp_enqueue_script('scroll', get_template_directory_uri() .'/js/scroll.js','','',true);
				}
                if ( is_singular() ) wp_enqueue_script( "comment-reply" );
        }
        add_action('wp_enqueue_scripts', 'weblizar_scripts');       
?>