<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin/partials
 */

$quiz_page_url = sprintf('?page=%s', 'quiz-maker');
$add_new_url = sprintf('?page=%s&action=%s', 'quiz-maker', 'add');
$questions_page_url = sprintf('?page=%s', 'quiz-maker-questions');
$new_questions_page_url = sprintf('?page=%s&action=%s', 'quiz-maker-questions', 'add');

?>
<div class="wrap">
    <div class="ays-quiz-maker-wrapper" style="position:relative;">
        <h1><?php echo __(esc_html(get_admin_page_title()),$this->plugin_name); ?> <i class="ays_fa ays_fa_heart_o animated"></i></h1>
    </div>
    <div class="ays-quiz-faq-main">
        <h2>
            <?php echo __("How to create a simple quiz in 4 steps with the help of the", $this->plugin_name ) .
            ' <strong>'. __("Quiz Maker", $this->plugin_name ) .'</strong> '.
            __("plugin.", $this->plugin_name ); ?>

        </h2>
        <fieldset>
            <div class="ays-quiz-ol-container">
                <ol>
                    <li>
                        <?php echo __("Go to the", $this->plugin_name ) . " <a href='". $questions_page_url ."' target='_blank'>" . __("Questions", $this->plugin_name )."</a> " .  __( "page and create", $this->plugin_name ) . " <a href='". $new_questions_page_url ."' target='_blank'>" . __("new questions", $this->plugin_name )."</a>"; ?>,
                    </li>
                    <li>
                        <?php echo __( "Then, go to the", $this->plugin_name ) . ' <a href="'. $quiz_page_url .'" target="_blank">'. __( "Quizzes" , $this->plugin_name ) .'</a> ' .  __( "page and build your first quiz by clicking on the", $this->plugin_name ) . ' <a href="'. $add_new_url .'" target="_blank">'. __( "Add New" , $this->plugin_name ) .'</a> ' .  __( "button", $this->plugin_name ); ?>,
                    </li>
                    <li>
                        <?php echo __( "Fill out the information by adding a title, previously created questions and so on.", $this->plugin_name ); ?>
                    </li>
                    <li>
                        <?php echo __( "Copy the", $this->plugin_name ) . ' <strong>'. __( "shortcode" , $this->plugin_name ) .'</strong> ' .  __( "of the quiz and paste it into any post․", $this->plugin_name ); ?>
                    </li>
                </ol>
            </div>
            <div class="ays-quiz-p-container">
                <p><?php echo __("Congrats! You have already created your first quiz." , $this->plugin_name); ?></p>
            </div>
        </fieldset>
    </div>

    <br>

    <div class="ays-quiz-community-wrap">
        <div class="ays-quiz-community-title">
            <h4><?php echo __( "Community", $this->plugin_name ); ?></h4>
        </div>
        <div class="ays-quiz-community-container">
            <div class="ays-quiz-community-item">
                <a href="https://www.youtube.com/channel/UC-1vioc90xaKjE7stq30wmA" target="_blank" class="ays-quiz-community-item-cover" style="color: #ff0000;background-color: rgba(253, 38, 38, 0.1);">
                    <i class="ays-quiz-community-item-img ays_fa ays_fa_youtube_play"></i>
                </a>
                <h3 class="ays-quiz-community-item-title"><?php echo __( "YouTube community", $this->plugin_name ); ?></h3>
                <p class="ays-quiz-community-item-desc"></p>
                <div class="ays-quiz-community-item-footer">
                    <a href="https://www.youtube.com/channel/UC-1vioc90xaKjE7stq30wmA" target="_blank" class="button"><?php echo __( "Subscribe", $this->plugin_name ); ?></a>
                </div>
            </div>
            <div class="ays-quiz-community-item">
                <a href="https://wordpress.org/support/plugin/quiz-maker/" target="_blank" class="ays-quiz-community-item-cover" style="color: #0073aa;background-color: rgb(220, 220, 220);">
                    <i class="ays-quiz-community-item-img ays_fa ays_fa_wordpress"></i>
                </a>
                <h3 class="ays-quiz-community-item-title"><?php echo __( "Free support", $this->plugin_name ); ?></h3>
                <p class="ays-quiz-community-item-desc"></p>
                <div class="ays-quiz-community-item-footer">
                    <a href="https://wordpress.org/support/plugin/quiz-maker/" target="_blank" class="button"><?php echo __( "Join", $this->plugin_name ); ?></a>
                </div>
            </div>
            <div class="ays-quiz-community-item">
                <a href="https://ays-pro.com/contact" target="_blank" class="ays-quiz-community-item-cover" style="color: #ff0000;background-color: rgba(0, 115, 170, 0.22);">
                    <img class="ays-quiz-community-item-img" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/logo_final.png">
                </a>
                <h3 class="ays-quiz-community-item-title"><?php echo __( "Premium support", $this->plugin_name ); ?></h3>
                <p class="ays-quiz-community-item-desc"></p>
                <div class="ays-quiz-community-item-footer">
                    <a href="https://ays-pro.com/contact" target="_blank" class="button"><?php echo __( "Contact", $this->plugin_name ); ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="ays-quiz-faq-main">
        <div class="ays-quiz-asked-questions">
            <h4><?php echo __("FAQs" , $this->plugin_name); ?></h4>
            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __( "Will I lose the data after the upgrade?", $this->plugin_name ); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo '<strong>'. __( "Nope!" , $this->plugin_name ) .'</strong> ' .
                        __( "All your content and assigned settings of the plugin will remain unchanged even after switching to the Pro version. You don’t need to redo what you have already built with the free version. For the detailed instruction, please take a look at our", $this->plugin_name ) .
                        ' <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual#frag_upgrade" target="_blank">'. __( "upgrade guide" , $this->plugin_name ) .'</a>. '?>
                    </p>
                </div>
            </div>
            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __("How do I change the design of the quiz?" , $this->plugin_name); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo __( "To do that, please go to the", $this->plugin_name ) .
                        ' <strong>'. __( "Styles" , $this->plugin_name ) .'</strong> ' .
                        __( "tab of the given quiz, which allows you to take full advantage of the various options it offers. The plugin provides 8 awesome ready-to-use themes. After choosing your preferred theme, you can customize it with 35+ style options to create appealing quizzes that people love to take, including", $this->plugin_name ) .
                        ' <strong>'. __( "main color, background image, right/wrong answer icons, progress bar, answer styles" , $this->plugin_name ) .'</strong> ' .
                        __("and etc. Moreover, you can use the" , $this->plugin_name) .
                        ' <strong>'. __( "Custom CSS" , $this->plugin_name ) .'</strong> ' .
                        __( "written field to fully match the design of your website and brand." , $this->plugin_name ); ?>
                    </p>
                </div>
            </div>
            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __( "How do I limit access to the quiz?", $this->plugin_name ); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo __( "To do that, please go to the", $this->plugin_name ) .
                        ' <strong>'. __( "Limitation" , $this->plugin_name ) .'</strong> ' .
                        __( "tab of the given quiz. The plugin suggests two methods to manage and detect the number of attempts from the same person. Those are", $this->plugin_name ) .
                        ' <strong>'. __( "by IP" , $this->plugin_name ) .'</strong> ' .
                        __("or" , $this->plugin_name) .
                        ' <strong>'. __( "by User ID." , $this->plugin_name ) .'</strong> ' .
                        __("One of the awesome functionalities that the plugin suggests is the" , $this->plugin_name) .
                        ' <strong>'. __( "Only for logged-in users" , $this->plugin_name ) .'</strong> ' .
                        __("option, which gives access to the quiz to those, who are logged-in users on your website. This option will allow you to precisely target your quiz takers, and not receive unnecessary data from the guests. Moreover, with the help of the" , $this->plugin_name) .
                        ' <strong>'. __( "Generated passwords (PRO)" , $this->plugin_name ) .'</strong> ' .
                        __( "option, you can give unique one-time access codes to each participant individually for accessing the quiz. You can use those access codes as promo codes, discounted codes, coupon codes, and so on." , $this->plugin_name ); ?>
                    </p>
                </div>
            </div>
            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __( "Can I know more about my respondents?", $this->plugin_name ); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo '<strong>'. __( "You are in a right place!" , $this->plugin_name ) .'</strong> ' .
                        __( "You just need to enable the", $this->plugin_name ) .
                        ' <strong>'. __( "Information Form" , $this->plugin_name ) .'</strong> ' .
                        __("from the" , $this->plugin_name) .
                        ' <strong>'. __( "User Data" , $this->plugin_name ) .'</strong> ' .
                        __("tab of the given quiz, create your preferred" , $this->plugin_name) .
                        ' <strong>'. __( "custom fields" , $this->plugin_name ) .'</strong> ' .
                        __( "in the" , $this->plugin_name ) .
                        ' <strong>'. __( "Custom Fields (PRO)" , $this->plugin_name ) .'</strong> ' .
                        __("page from the plugin left navbar, and come up with a clear picture of who your quiz participants are, where they live, what their lifestyle and personality are like, etc." , $this->plugin_name); ?>
                    </p>
                </div>
            </div>
            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __( "Will I get notified every time a quiz is submitted? (PRO)", $this->plugin_name ); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo '<strong>'. __( "You will!" , $this->plugin_name ) .'</strong> ' .
                        __( "To enable it, please go to the", $this->plugin_name ) .
                        ' <strong>'. __( "Email/Certificate" , $this->plugin_name ) .'</strong> ' .
                        __("tab of the given quiz. There you will find the" , $this->plugin_name) .
                        ' <strong>'. __( "Send mail to admin" , $this->plugin_name ) .'</strong> ' .
                        __("option. After enabling the option, the admin (and/or your provided additional email(s)) will receive an email notification about results at each time." , $this->plugin_name); ?>
                    </p>
                </div>
            </div>

            <div class="ays-quiz-asked-question">
                <div class="ays-quiz-asked-question__header">
                    <div class="ays-quiz-asked-question__title">
                        <h4><strong><?php echo __( "Can I send certificates to those users who have passed the quiz? (PRO)" , $this->plugin_name ); ?></strong></h4>
                    </div>
                    <div class="ays-quiz-asked-question__arrow"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="ays-quiz-asked-question__body">
                    <p>
                        <?php echo '<strong>'. __( "Yes!" , $this->plugin_name ) .'</strong> ' .
                        __( "To enable it, please go to the", $this->plugin_name ) .
                        ' <strong>'. __( "Email/Certificate" , $this->plugin_name ) .'</strong> ' .
                        __("tab of the given quiz. There you will find the" , $this->plugin_name) .
                        ' <strong>'. __( "Send certificate to user" , $this->plugin_name ) .'</strong> ' .
                        __("option. After enabling the option, you need to configure the settings of it such as" , $this->plugin_name) .
                        ' <strong>'. __( "Certificate pass score, Title" , $this->plugin_name ) .'</strong> ' .
                        __( "and" , $this->plugin_name ) .
                        ' <strong>'. __( "Body" , $this->plugin_name ) .'</strong>. ' .
                        __("Moreover, you can choose the orientation of the certificate, add a " , $this->plugin_name) .
                        ' <strong>'. __( "Background image" , $this->plugin_name ) .'</strong> ' .
                        __("(for instance the logo of your company) and select your preferred frame." , $this->plugin_name); ?>
                    </p>
                </div>
            </div>
        </div>
        <p class="ays-quiz-faq-footer">
            <?php echo __( "For more advanced needs, please take a look at our" , $this->plugin_name ); ?>
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __( "Quiz Maker plugin User Manual." , $this->plugin_name ); ?></a>
            <br>
            <?php echo __( "If none of these guides help you, ask your question by contacting our" , $this->plugin_name ); ?>
            <a href="https://ays-pro.com/contact" target="_blank"><?php echo __( "support specialists." , $this->plugin_name ); ?></a>
            <?php echo __( "and get a reply within a day." , $this->plugin_name ); ?>
        </p>
    </div>
</div>
<script>
    var acc = document.getElementsByClassName("ays-quiz-asked-question__header");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {

        var panel = this.nextElementSibling;


        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
          this.children[1].children[0].style.transform="rotate(0deg)";
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
          this.children[1].children[0].style.transform="rotate(180deg)";
        }
      });
    }
</script>
