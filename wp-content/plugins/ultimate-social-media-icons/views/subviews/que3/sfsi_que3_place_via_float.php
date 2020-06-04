<?php 

$option9['sfsi_icons_float']              = (isset($option9['sfsi_icons_float']))               ? sanitize_text_field($option9['sfsi_icons_float']): 'no';

$option9['sfsi_icons_floatPosition']      = (isset($option9['sfsi_icons_floatPosition']))       ? sanitize_text_field($option9['sfsi_icons_floatPosition']) :'center-right';

$option9['sfsi_icons_floatMargin_top']    = (isset($option9['sfsi_icons_floatMargin_top']))     ? intval($option9['sfsi_icons_floatMargin_top']) : '';

$option9['sfsi_icons_floatMargin_bottom'] = (isset($option9['sfsi_icons_floatMargin_bottom']))  ? intval($option9['sfsi_icons_floatMargin_bottom']) : '';

$option9['sfsi_icons_floatMargin_left']   = (isset($option9['sfsi_icons_floatMargin_left']))    ? intval($option9['sfsi_icons_floatMargin_left']) : '';

$option9['sfsi_icons_floatMargin_right']  = (isset($option9['sfsi_icons_floatMargin_right']))   ? intval($option9['sfsi_icons_floatMargin_right']) : '';

$option9['sfsi_disable_floaticons']       = (isset($option9['sfsi_disable_floaticons']))        ? sanitize_text_field($option9['sfsi_disable_floaticons']): 'no';

$style                                    =  ($option9['sfsi_icons_float'] == "yes")            ? 'display: block;' : "display: none;";

?>

		<li class="sfsiLocationli">

            <div class="radio_section tb_4_ck cstmfltonpgstck" onclick="sfsi_toggleflotpage_que3(this);">

                <input name="sfsi_icons_float" <?php echo ($option9['sfsi_icons_float']=='yes') ?  'checked="true"' : '' ;?>  type="checkbox" value="yes" class="styled" />

                <p><span class="sfsi_toglepstpgspn">Floating over your website's pages</span></p>
            </div>

			<div class="sfsi_right_info" <?php echo 'style="'.$style.'"';?>>
                <p><span style="margin-left: 31px;">Define the location:</span></p>

                <div class="sfsi_tab_3_icns">

					

                    <ul class="sfsi_tab_3_icns flthmonpg">

                        

                        <div class="sfsi_position_divider">

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='top-left') ?  'checked="true"' : '' ;?> type="radio" value="top-left" class="styled"  />

                                <span class="sfsi_flicnsoptn3 sfsioptntl">Top left</span>

                                <label><img src="<?php echo SFSI_PLUGURL;?>images/top_left.png" alt='error'/></label>

                            </li>

                            

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='center-top') ?  'checked="true"' : '' ;?> type="radio" value="center-top" class="styled"  />

                                

                                <span class="sfsi_flicnsoptn3 sfsioptncl">Center top</span>

                                <label class="sfsi_float_position_icon_label"><img src="<?php echo SFSI_PLUGURL;?>images/float_position_icon.png" alt='error'/></label>

                            </li>                        

                            

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='top-right') ?  'checked="true"' : '' ;?> type="radio" value="top-right" class="styled"  />

                                <span class="sfsi_flicnsoptn3 sfsioptntr">Top right</span>

                                <label><img src="<?php echo SFSI_PLUGURL;?>images/top_right.png" alt='error' /></label>

                            </li>

                        </div>

                        <div class="sfsi_position_divider">

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='center-left') ?  'checked="true"' : '' ;?> type="radio" value="center-left" class="styled"  />

                                

                                <span class="sfsi_flicnsoptn3 sfsioptncl">Center left</span>

                                <label><img src="<?php echo SFSI_PLUGURL;?>images/center_left.png" alt='error'/></label>

                            </li>

                            <li></li>

                            

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='center-right') ?  'checked="true"' : '' ;?> type="radio" value="center-right" class="styled"  />

                                

                                <span class="sfsi_flicnsoptn3 sfsioptncr">Center right</span>

                                <label><img src="<?php echo SFSI_PLUGURL;?>images/center_right.png" alt='error'/></label>

                            </li>

                        

                        </div>

                        <div class="sfsi_position_divider">

                            <li>

                                <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='bottom-left') ?  'checked="true"' : '' ;?> type="radio" value="bottom-left" class="styled"  />

                                <span class="sfsi_flicnsoptn3 sfsioptnbl">Bottom left</span>

                                <label><img src="<?php echo SFSI_PLUGURL;?>images/bottom_left.png" alt='error'alt='error'/></label>

                            </li>

                    

                        <li>

                            <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='center-bottom') ?  'checked="true"' : '' ;?> type="radio" value="center-bottom" class="styled"  />

                            

                            <span class="sfsi_flicnsoptn3 sfsioptncr">Center bottom</span>

                            <label class="sfsi_float_position_icon_label sfsi_center_botttom"><img class="sfsi_img_center_bottom" src="<?php echo SFSI_PLUGURL;?>images/float_position_icon.png" alt='error'/></label>

                        </li>

                        <li>

                            <input name="sfsi_icons_floatPosition" <?php echo ( $option9['sfsi_icons_floatPosition']=='bottom-right') ?  'checked="true"' : '' ;?> type="radio" value="bottom-right" class="styled"  />

                            

                            <span class="sfsi_flicnsoptn3 sfsioptnbr">Bottom right</span>

                            <label><img src="<?php echo SFSI_PLUGURL;?>images/bottom_right.png" alt='error'/></label>

                        </li>

                        </div>

                    </ul>

                    

                    <div style="width: 88%; float: left; margin:25px 0 0 25px">

                    	

                        <h4>Margin From: </h4>

                        <ul class="sfsi_floaticon_margin_sec">

                            

                            <li>

                                <label>Top:</label>                                

                                <input name="sfsi_icons_floatMargin_top" type="text" value="<?php echo ($option9['sfsi_icons_floatMargin_top']!='') ?  $option9['sfsi_icons_floatMargin_top'] : '' ;?>" />

                                <ins>Pixels</ins>

                            </li>

                            

                            <li>

                                <label>Bottom:</label>

                                <input name="sfsi_icons_floatMargin_bottom" type="text" value="<?php echo ($option9['sfsi_icons_floatMargin_bottom'] != '') ?  $option9['sfsi_icons_floatMargin_bottom'] : '' ;?>" />

                                <ins>Pixels</ins>

                            </li>

                       

                            <li>

                                <label>Left:</label>

                                <input name="sfsi_icons_floatMargin_left" type="text" value="<?php echo ($option9['sfsi_icons_floatMargin_left']!='') ?  $option9['sfsi_icons_floatMargin_left'] : '' ;?>" />

                               <ins>Pixels</ins>

                            </li>

                            

                            <li>

                                <label>Right:</label>

                                <input name="sfsi_icons_floatMargin_right" type="text" value="<?php echo ($option9['sfsi_icons_floatMargin_right']!='') ?  $option9['sfsi_icons_floatMargin_right'] : '' ;?>" />

                                <ins>Pixels</ins>

                            </li>

                        </ul>

                    </div>

                    

                    <div style="width: 88%; float: left; margin:25px 0 0 7px">

                        <p style="line-height: 34px;">

                            The icons will be floating on your page. If you want them <b>"sticky"</b>, please check out the <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/"><b>Premium Plugin</b></a>. Also in the Premium Plugin you can show the icons <b>vertically</b>, and give them <b>different settings for mobile.</b> 

                        </p>

                    </div>

                    <div class="sfsi_disable_floatingicons_mobile">

                        

                        <h4>Want to disable the floating icons on mobile?</h4>

                        <ul class="sfsi_make_icons sfsi_plus_mobile_float">

                            <li>

                                <input name="sfsi_disable_floaticons" <?php echo ( $option9['sfsi_disable_floaticons']=='yes') ?  'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />

                                <span class="sfsi_flicnsoptn3">Yes</span>

                            </li>

                            <li>

                                <input name="sfsi_disable_floaticons" <?php echo ( $option9['sfsi_disable_floaticons']=='no') ?  'checked="true"' : '' ;?> type="radio" value="no" class="styled"/>

                                <span class="sfsi_flicnsoptn3">No</span>

                            </li>

                        </ul>

                    </div>

                </div>

			</div>

		</li>