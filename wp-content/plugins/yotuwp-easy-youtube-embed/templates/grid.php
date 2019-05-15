<?php
global $yotuwp;

$settings = $yotuwp->data['settings'];
$data = $yotuwp->data['data'];
$classes = apply_filters( 'yotu_classes', array("yotu-videos yotu-mode-grid"), $settings);
$video_classes = apply_filters( 'yotu_video_classes', array("yotu-video"), $settings);
?>
<div class="<?php echo implode(" ", $classes);?>">
	<ul>
		<?php
		$total = count($data->items);
		if (is_object($data) && $total >0):

			$count = 0;

			foreach($data->items as $video){
				if ( $yotuwp->is_private($video) ) continue;
				
				$thumb = yotuwp_video_thumb($video);
				$videoId = $yotuwp->getVideoId($video);

				$video_title = yotuwp_video_title($video);
				$video->settings = $settings;
			?>
			<li class="<?php echo $count==0?' yotu-first':''; echo ($count+1)==$total?' yotu-last':'';?>">
				<?php do_action('yotuwp_before_link', $videoId, $video);?>
				<a href="#<?php echo $videoId;?>" class="<?php echo implode(" ", $video_classes);?>" data-videoid="<?php echo $videoId;?>" data-title="<?php echo $yotuwp->encode($video_title);?>" title="<?php echo $video_title;?>">
					<div class="yotu-video-thumb-wrp">
						<div>
							<?php do_action('yotuwp_before_thumbnail', $videoId, $video, $settings);?>
							<img class="yotu-video-thumb" src="<?php echo $thumb;?>" alt="<?php echo $video_title;?>">	
							<?php do_action('yotuwp_after_thumbnail', $videoId, $video);?>
						</div>
					</div>
					<?php if( isset($settings['title']) && $settings['title'] == 'on' ):?>
						<h3 class="yotu-video-title"><?php echo $video_title;?></h3>
					<?php endif;?>
					<?php do_action('yotuwp_after_title', $videoId, $video);?>
					<?php if(isset($settings['description']) && $settings['description'] == 'on'):?>
						<div class="yotu-video-description"><?php echo yotuwp_video_description($video);?></div>
					<?php endif;?>
				</a>
				<?php do_action('yotuwp_after_link', $videoId, $video);?>
			</li>
				
			<?php
			$count++;
			}
		endif;	
		?>
	</ul>
</div>