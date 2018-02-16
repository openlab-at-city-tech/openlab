<div class="cptch-dash-inner-area">

			<h2  class="cptch-dash-nav-tab-wrapper">

			<ul  class="cptch-dash-nav">

                <li><div class = "simple_img"><img src="<?php echo plugins_url( 'captcha/logo/simply.png' ); ?>" /></div></li>

                <li><a class="<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=cptc_dashboard"><?php _e( 'Dashboard', 'captcha' ); ?></a></li>

				<li><a class=" <?php if ( isset( $_GET['action'] ) && 'settings' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=captcha.php" title="<?php _e( 'Captcha Settings', 'captcha' ); ?>"><?php _e( 'Captcha Settings', 'captcha' ); ?></a>

                </li>



				<li><a class="<?php if ( isset( $_GET['action'] ) && 'custom_requests' == $_GET['action'] ) echo 'nav-tab-active'; ?>" target="_blank"  href="https://mysimplewp.com/contact-us/"><?php _e( 'Custom Requests', 'captcha' ); ?></a>



                </li>

             </ul>

			</h2>

            <?php if(isset($_GET['page']) and ($_GET['page'] == 'cptc_dashboard') and !isset($_GET['action']) ){ ?>

            <div class="cptch_dash_welcome_message"><p>Welcome to Mysimplewp Captcha Anti Spam Security Plugin protected by our patented Simply Secure Service.<br /> As a customer you have free access to our other plugins (more to come)<br /> feel free to pick from the bottom of the page or visit</br> <a target="_blank" href="http://www.mysimplewp.com">www.mysimplewp.com</a></p></div>

            <?php } ?>

       </div>