<div id="welcome-panel" class="welcome-panel johannes-welcome-panel">
    <a href="#" class="welcome-panel-close" id="johannes_update_box_hide">Dismiss</a>
    <div class="welcome-panel-content">
        <h2>Congratulations, your website just got better!</h2>
        <p class="about-description">Johannes has been successfully updated to version
            <?php echo JOHANNES_THEME_VERSION; ?>
    </a></p>
    <div class="welcome-panel-column-container">
        <div class="welcome-panel-column">
            <h3>What's new?</h3>
            <p>We do our best to keep our themes up-to-date. Take a few moments to see what's added in the latest version.</p>
            <a href="http://mekshq.com/docs/johannes-change-log" target="_blank" class="button button-primary button-hero">View change log</a>
        </div>
        <div class="welcome-panel-column">
            <h3>We listen to your feedback</h3>
            <p>If you have ideas which might help us make Johannes even better, we would love to hear from you!</p>
            <a href="http://mekshq.com/contact" target="_blank" class="button button-primary button-hero">Get in touch</a>
        </div>
        <?php 
				$tweet_text = "I'm very happy using Johannes! Check out this great #WordPress theme by @meksHQ";
				$tweet_url = "https://mekshq.com/demo/johannes/";
				?>
        <div class="welcome-panel-column">
            <h3>Happy with Johannes?</h3>
            <p>Why not share the feeling with the world? We would really appreciate it. </p>
            <a href="javascript:void(0);" data-url="http://twitter.com/intent/tweet?url=<?php echo esc_url($tweet_url); ?>&amp;text=<?php echo urlencode($tweet_text); ?>" class="mks-twitter-share-button button button-primary button-hero">Tweet about it!</a>
        </div>
    </div>
</div>
</div>