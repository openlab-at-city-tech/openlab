<?php
$mode = $this->get_processor()->get_shortcode_option('mode');

$classes = '';
if ('0' === $this->get_processor()->get_shortcode_option('playlistthumbnails')) {
    $classes .= 'nothumbnails ';
}

if ('0' === $this->get_processor()->get_shortcode_option('show_filedate')) {
    $classes .= 'nodate ';
}

$max_width = $this->get_processor()->get_shortcode_option('maxwidth');
$hide_playlist = $this->get_processor()->get_shortcode_option('hideplaylist');
$show_playlistonstart = $this->get_processor()->get_shortcode_option('showplaylistonstart');
$playlist_inline = $this->get_processor()->get_shortcode_option('playlistinline');
$controls = implode(',', $this->get_processor()->get_shortcode_option('mediabuttons'));
$autoplay = $this->get_processor()->get_shortcode_option('autoplay');

$ads_active = '1' === $this->get_processor()->get_shortcode_option('ads');
$ads_tag_url = OUTOFTHEBOX_ADMIN_URL.'?action=outofthebox-getads&account_id='.$this->get_processor()->get_current_account()->get_id().'&listtoken='.$this->get_processor()->get_listtoken();
$ads_can_skip = '1' === $this->get_processor()->get_shortcode_option('ads_skipable');

$shortcode_ads_skip_after_seconds = $this->get_processor()->get_shortcode_option('ads_skipable_after');
$ads_skip_after_seconds = (empty($shortcode_ads_skip_after_seconds) ? $this->get_processor()->get_setting('mediaplayer_ads_skipable_after') : $shortcode_ads_skip_after_seconds);
?><div 
  class="wpcp__main-container wpcp__loading wpcp__<?php echo$mode; ?> <?php echo $classes; ?>" 
  style="width:100%; max-width:<?php echo $max_width; ?>;"
  data-hide-playlist="<?php echo $hide_playlist; ?>" 
  data-open-playlist="<?php echo $show_playlistonstart; ?>"
  data-playlist-inline="<?php echo $playlist_inline; ?>"
  data-controls="<?php echo $controls; ?>"
  data-ads-tag-url="<?php echo ($ads_active) ? $ads_tag_url : ''; ?>"
  data-ads-skip="<?php echo ($ads_can_skip && ((int) $ads_skip_after_seconds > -1)) ? $ads_skip_after_seconds : '-1'; ?>"
  >
  <div class="loading initialize"><svg class="loader-spinner" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"></circle></svg></div>
  <<?php echo $mode; ?> <?php echo ('1' === $autoplay) ? 'autoplay' : ''; ?> preload="metadata" playsinline webkit-playsinline crossorigin="anonymous"></<?php echo$mode; ?>>
</div>