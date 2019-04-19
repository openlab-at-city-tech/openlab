<?php
/**
 * Field system by YotuWP
 */
class YotuFields{
    
    public function __construct()
    {

    }

    public function render_field($data ) {

		ob_start();
		
		$data = apply_filters('yotuwp_before_render_field', $data );

		?>
		<div class="yotu-field yotu-field-type-<?php echo $data['type']; echo ( isset($data['pro']) )? ' yotu-field-pro' :''?>" id="yotuwp-field-<?php echo $data['name'];?>">
			<?php if( isset( $data['label'] ) ):?>
			<label for="yotu-<?php echo esc_attr($data['group']) . '-'. esc_attr($data['name']);?>"><?php echo esc_attr( $data['label'] );?></label>
			<?php endif;?>
			<div class="yotu-field-input">

				<?php call_user_func_array(array($this, $data['type']), array($data));?>
				<?php do_action('yotuwp_after_render_field', $data );?>
				<label class="yotu-field-description" for="yotu-<?php echo esc_html($data['group']) . '-'. esc_attr($data['name']);?>"><?php echo $data['description'];?></label>
			</div>
			
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function intro( $data ) {
		?>
<p>Hi there,</p>

<p>It is Anthony Pham here…</p>

<p>I am founder of YotuWP.</p>

<p>We’ve been working hard on a lot of new features for YotuWP.</p>

<p>I hope you've had a good week too.</p>

<p>Anyway I just wanted to say I hope you have a GREAT weekend and if you have a spare 7 minutes click on the link below and check out a new  tutorial on 'Boost Website Speed with WP Fastest Cache & Cloudflare In 7 Mins' Once you've had a chance to have a look at it hit reply to contact form on my website and let me know what you think.</p>
<p><a target="_blank" href="https://www.yotuwp.com/boost-website-speed-with-wp-fastest-cache-cloudflare-in-7-mins/?utm_source=intro">https://www.yotuwp.com/boost-website-speed-with-wp-fastest-cache-cloudflare-in-7-mins/</a>
</p>

<p>As I mentioned it only goes for 7 minutes and is a neat setting.</p>

<p>&nbsp;</p>

<p>Finally, I actually need your help. (Please keep reading...)</p>

<p>I do really. You see I've been working very hard, I mean really hard lot's of late nights and early mornings for the past few months!! (my wife will testify to that!!)</p>

<p>Why? you might ask! Well you see I've been working on something really special that is going to help you increase user interaction and makeup your website with videos.</p>

<p>It's the most complete and extensive features on 'YotuWP Video Gallery' that you'll ever see.</p>

<p>You can check demo Advanced version of plugin from <a target="_blank" href="https://www.yotuwp.com/advanced-demos/?utm_source=intro">https://www.yotuwp.com/advanced-demos/</a></p>

<p>So, all I'm asking is for you to email me (just send me message via contact form at <a target="_blank" href="https://www.yotuwp.com/contact/?utm_source=intro">https://www.yotuwp.com/contact</a>) with your Requests as to what it will need to have for you to buy it.</p>

<p>Take care and have a GREAT week, and I look forward to hearing from you over the weekend.</p>

<p>Cheers

<p>Anthony<br/>
Founder, YotuWp.com
</p>
		<?php
	}

    public function color( $data ) {
		$preview_css = isset($data['preview_css'])? $data['preview_css'] : $data['css'];
    ?>
        <input type="text" id="yotu-<?php echo esc_attr( $data['group']) . '-'. esc_attr($data['name']);?>" class="yotu-param yotu-colorpicker" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" data-css="<?php echo $preview_css;?>" value="<?php echo (isset( $data['value'] ) ? $data['value'] : $data['default']);?>" />
    <?php
	}

    public function text( $data ) {
    ?>
        <input type="text" id="yotu-<?php echo esc_attr( $data['group']) . '-'. esc_attr($data['name']);?>" class="yotu-param" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" value="<?php echo (isset( $data['value'] ) ? $data['value'] : $data['default']);?>" />
    <?php
	}

	public function pro( $data ) {
		echo '<i>i</i>';
		echo '<span class="ytpro">Only in Premium version.</span>';
		if ( isset( $data['img'] ) ) echo '<img src="'. esc_url( $data['img'] ).'" class="yotuwp-pro-img"/>';
	}

    public function license($data ) {
		global $yotupro;
    ?>
        <input type="text" id="yotu-license-key" class="yotu-param" name="yotu-license-key" value="<?php echo ($yotupro->valid)? '***************' . substr($yotupro->updater->get('package_license'), -9):'';?>" />
		<span class="yotu-license-verified <?php echo ($yotupro->valid == 1)? 'yotu-license-activated':'';?>">Verified</span>
		<a href="#" id="yotuwp-license-action" data-func="<?php echo ( $yotupro->valid == 1? 'deactivate':'activate');?>"><?php echo ( $yotupro->valid == 1 ? 'Deactivate':'Activate');?></a>
		<div class="yotu-license-status">
		<?php
		
		if ( $yotupro->valid == 0 || $yotupro->valid == -1):
			$diff_time = 604800 - (time() - get_option( 'yotuwp_pro_install_date', time() ));
			if( $diff_time >= 0 ) {
				echo sprintf( _('You are using YotuWP Pro trial license. You have %s days left to active license before all advance features leave to default settings.'), (int)($diff_time/86400));
			} else {
				echo sprintf( _('Your trial license for YotuWP is expired. Please purchase a license for keeping your customize styling. You can purchase a license <a target="_blank" href="%s">from here</a>'), 'https://www.yotuwp.com/pricing/');
			}
			
		endif;
		?>
		</div>
		<?php
	}

	public function select($data ) {
		$value = (isset($data['value']) && !empty($data['value'])) ? $data['value'] : $data['default'];
	?>
    <select id="yotu-<?php echo esc_attr($data['group']) . '-'. esc_attr($data['name']);?>" class="yotu-param" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]">
        <?php
            foreach ($data['options'] as $key => $val) {
            ?>
            <option value="<?php echo $key;?>"<?php echo ($value == $key)? ' selected="selected"' : '';?>><?php echo $val;?></option>
            <?php
            }
        ?>
	</select>
	<?php 
		if (isset($data['extbtn']) && $data['extbtn'] != '') {
			echo $data['extbtn'];
		}
	
	}

	public function checkbox( $data ) {
		$value = (isset($data['value']) && !empty($data['value'])) ? $data['value'] : $data['default'];
	?>
    
        <?php
            foreach ($data['options'] as $key => $val) {
				$key_id = $data['group'] . '-'. $data['name'] .'-'. $key;
				$name = $data['name'] .'|'. $key;
			?>
			<div class="yotuwp-field-checkbox-item">
				<input type="checkbox"<?php echo (isset( $value[ $key ] ) && $value[ $key ] == 'on' )? ' checked="checked"' :'' ;?> id="yotuwp-<?php echo esc_attr( $key_id );?>" class="yotu-param" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr( $name );?>]">		
				<label for="yotuwp-<?php echo esc_attr( $key_id );?>"><?php echo $val;?></label>
			</div>
            <?php
            }
        ?>
    </select>
	<?php
	}

	public function toggle($data ) {
        global $yotuwp;
	?>
	<label class="yotu-switch">
		<input type="checkbox" id="yotu-<?php echo esc_attr($data['group']) . '-'. esc_attr($data['name']);?>" class="yotu-param" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" <?php echo ($data['value'] == 'on' ) ? 'checked="checked"' : '';?> />
		<span class="yotu-slider yotu-round"></span>
	</label>
	<?php
	}

	public function radios( $data ) {
		global $yotuwp;
		
		$value = (isset($data['value']) && !empty($data['value']) && isset($data['options'][ $data['value'] ])) ? $data['value'] : $data['default'];

	?>
	<div class="yotu-radios-img yotu-radios-img-<?php echo isset($data['class'])? $data['class']:'full';?>">
		<?php

			if ( $value != '' && isset($data['options'][ $value ]) ) {
				$temp = array( $value => $data['options'][ $value ] );
				unset( $data['options'][$value] );
				$data['options'] = $temp + $data['options'];
			}

            foreach ($data['options'] as $key => $val) {
            	$id       = 'yotu-' . esc_attr($data['group']) . '-'. esc_attr($data['name']) . '-'. $key;
            	$selected = ($value == $key)? ' yotu-field-radios-selected' : '';
            ?>
            <label class="yotu-field-radios<?php echo $selected;?>" for="<?php echo $id;?>">
				<input class="yotu-param" value="<?php echo $key;?>" type="radio"<?php echo ($value == $key) ? ' checked="checked"' : '';?> id="<?php echo $id;?>" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" />

				<?php if( !empty($val['img']) ) :
					$img_url = ( strpos($val['img'], 'http') === false )? $yotuwp->assets_url . $val['img'] : $val['img'];
				?>
					<img src="<?php echo $img_url;?>" alt="<?php echo $val['title'];?>" title="<?php echo $val['title'];?>"/><br/>
				<?php else:?>
					<div class="yotuwp-field-radios-text-option"><?php echo $val['title'] . __(' Settings', 'yotuwp-easy-youtube-embed');?></div>
				<?php endif;?>

            	<span><?php echo $val['title'];?></span>
            </label>
            <?php
            }
        ?>
	</div>
	<?php 
		if (isset($data['extbtn']) && $data['extbtn'] != '') {
			echo $data['extbtn'];
		}
	
	}

	public function buttons($data ) {
        global $yotuwp;
		$value = (isset($data['value']) && !empty($data['value'])) ? $data['value'] : $data['default'];

	?>
	<div class="yotu-radios-img-buttons yotu-radios-img yotu-radios-img-<?php echo isset($data['class'])? $data['class']:'full';?>">
		<?php
            for ($i=1; $i<=4; $i++) {
            	$id = 'yotu-' . esc_attr($data['group']) . '-'. esc_attr($data['name']) . '-'. $i;
            	$selected = ($value == $i)? ' yotu-field-radios-selected' : ''
            ?>
            <label class="yotu-field-radios<?php echo $selected;?>" for="<?php echo $id;?>">
				<input value="<?php echo $i;?>" type="radio"<?php echo ($value == $i) ? ' checked="checked"' : '';?> id="<?php echo $id;?>" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" class="yotu-param" />
				<div>
            		<a href="#" class="yotu-button-prs yotu-button-prs-<?php echo $i;?>"><?php echo __('Prev', 'yotuwp-easy-youtube-embed');?></a>
					<a href="#" class="yotu-button-prs yotu-button-prs-<?php echo $i;?>"><?php echo __('Next', 'yotuwp-easy-youtube-embed');?></a>
				</div>
                <br/>
                <span><?php echo sprintf( __('Style %s', 'yotuwp-easy-youtube-embed'), $i);?></span>
            </label>
            <?php
            }
        ?>
	</div>
	<?php
	}

	public function icons($data ) {
        global $yotuwp;
		$value = (isset($data['value']) && !empty($data['value'])) ? $data['value'] : $data['default'];
	?>
	<div class="yotu-radios-img-buttons yotu-radios-img yotu-radios-img-<?php echo isset($data['class'])? $data['class']:'full';?>">
		<?php
            foreach ( $data['options'] as $key => $val ) {
            	$id = 'yotu-' . esc_attr($data['group']) . '-'. esc_attr($data['name']) . '-'. $key;
            	$selected = ($value == $key)? ' yotu-field-radios-selected' : ''
            ?>
            <label class="yotu-field-radios<?php echo $selected;?>" for="<?php echo $id;?>">
				<input value="<?php echo $key;?>" type="radio"<?php echo ($value == $key) ? ' checked="checked"' : '';?> id="<?php echo $id;?>" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" class="yotu-param" />
				<div>
            		<i class="yotu-video-thumb-wrp yotuicon-<?php echo $key;?>"></i>
				</div>
                <br/>
                <span><?php echo sprintf( __('%s', 'yotuwp-easy-youtube-embed'), $val);?></span>
            </label>
            <?php
            }
        ?>
	</div>
	<?php
	}

	public function button($data) {
		?>
		<a href="#" class="yotu-button yotu-button-s" data-func="<?php echo $data['func'];?>"><?php echo $data['btn-label'];?></a>
		<?php
	}

	public function effects($data ) {
		$value = (isset($data['value']) && !empty($data['value'])) ? $data['value'] : $data['default'];
		$effects = array(
			array('', 'None'),
			array('ytef-grow', 'grow'),
			array('ytef-float', 'float'),
			array('ytef-rotate', 'Rotate'),
			array('ytef-shadow-radial', 'shadow radial')
		);
	?>
	<div class="yotu-effects">
		<?php
            foreach ($effects as $eff) {
				$selected = ($eff[0] == $value)? true : false;
				$id       = 'yotu-' . esc_attr($data['group']) . '-'. esc_attr($data['name']) . '-'. $eff[0];
			?>
				<label class="yotu-field-effects<?php echo $selected?' yotu-field-effects-selected':'';?>" for="<?php echo $id;?>">
					<span class="<?php echo $eff[0];?>"><?php echo $eff[1];?></span>
					<input class="yotu-param" value="<?php echo $eff[0];?>" type="radio"<?php echo ($selected) ? ' checked="checked"' : '';?> id="<?php echo $id;?>" name="yotu-<?php echo esc_attr($data['group']);?>[<?php echo esc_attr($data['name']);?>]" />
					
				</label>
            <?php
            }
        ?>
	</div>
	<a href="https://www.yotuwp.com/pro/" target="_blank" class="extra-btn"><span class="dashicons dashicons-arrow-right-alt"></span>Get More Effects</a>
	<?php
	}
}