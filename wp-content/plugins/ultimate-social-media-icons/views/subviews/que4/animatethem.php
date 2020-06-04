		<!--icon Animation section start -->

		<div class="sub_row stand sec_new" style="margin-left: 0px;">

			<h4>Animate them</h4>

            <div id="animationSection" class="radio_section tab_3_option">

                <input name="sfsi_mouseOver" <?php echo ( $option3['sfsi_mouseOver']=='yes') ?  'checked="true"' : '' ;?> type="checkbox" value="yes" class="styled"  />

                <label>

                    Mouse-Over effects

                </label>

                <div class="col-md-12 rowmarginleft45 mouse-over-effects <?php echo ( $option3['sfsi_mouseOver']=='yes') ?  'show' : 'hide' ;?>">

                    <div class="row">

                        <input value="same_icons" name="sfsi_mouseOver_effect_type" <?php echo ( $option3['sfsi_mouseOver_effect_type']=='same_icons') ?  'checked=checked' : '' ;?> type="radio" class="styled"/>

                        <label>Same-icon effects</label>

                    </div><!-- row closes -->

                    <div class="row rowpadding10 same_icons_effects <?php echo ( $option3['sfsi_mouseOver_effect_type']=='same_icons') ?  'show' : 'hide' ;?>">

                        <div class="effectContainer bottommargin30">

                            <div class="effectName">

                                

                                <input class="styled" type="radio" name="sfsi_same_icons_mouseOver_effect" value="fade_in" <?php echo ( $option3['sfsi_mouseOver_effect']=='fade_in') ?  'checked="true"' : '' ;?>>

                                

                                <label>

                                    <span>Fade In</span>

                                    <span>(Icons turn from shadow to full color)</span>

                                </label>

                            </div>

                            <div class="effectName">

                                

                                <input class="styled" type="radio" name="sfsi_same_icons_mouseOver_effect" value="scale" <?php echo ( $option3['sfsi_mouseOver_effect']=='scale') ?  'checked="true"' : '' ;?>>

                                <label>

                                    <span> Scale</span>

                                    <span>(Icons become bigger)</span>

                                </label>

                            </div>

                        </div>

                        <div class="effectContainer">

                            <div class="effectName">

                                

                                <input class="styled" type="radio" name="sfsi_same_icons_mouseOver_effect" value="combo" <?php echo ( $option3['sfsi_mouseOver_effect']=='combo') ?  'checked="true"' : '' ;?>>

                                

                                <label>

                                    <span>Combo</span>

                                    <span>(Both fade in and scale effects)</span>

                                </label>

                            </div>

                            <div disabled class="effectName inactiveSection">

                                <input class="styled" type="radio" name="sfsi_same_icons_mouseOver_effect" value="fade_out" <?php echo ( $option3['sfsi_mouseOver_effect']=='fade_out') ?  'checked="true"' : '' ;?>>

                                

                                <label> 

                                    <span>Fade Out</span>

                                    <span>(Icons turn from full color to shadow)</span>

                                </label>

                            </div>

                        </div>

                        

                        <div class="row rowmarginleft45 mouseover-premium-notice">

                            <label>Greyed-out options are available in the</label>

                            <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=same_icon_effects&utm_medium=link">Premium Plugin</a>

                        </div>

                    </div><!-- row closes -->

                    <div class="row zerobottompadding other_icons_effects">

                        <input value="other_icons" name="sfsi_mouseOver_effect_type" <?php echo ( $option3['sfsi_mouseOver_effect_type']=='other_icons') ?  'checked=checked' : '' ;?> type="radio" class="styled"/>

                        <label>Show other icons on mouse-over (Only applied for Desktop Icons)</label>

                    </div><!-- row closes -->

                    <div class="row rowpadding10 rowmarginleft35 other_icons_effects_options <?php echo ( $option3['sfsi_mouseOver_effect_type']=='other_icons') ?  'show' : 'hide' ;?>">

                        

                        <div disabled class="col-md-12 inactiveSection other_icons_effects_options_container">

                                                                    

                            <?php 

                                $arrDefaultIcons        = unserialize(SFSI_ALLICONS);

                                $arrActiveStdDesktopIcons    = sfsi_get_displayed_std_desktop_icons($option1);

                                $arrActiveCustomDesktopicons = sfsi_get_displayed_custom_desktop_icons($option1);

                                foreach ($arrDefaultIcons as $key => $iconName):

                                    sfsi_icon_generate_other_icon_effect_admin_html($iconName,$arrActiveStdDesktopIcons);                                     

                                endforeach;

                                if(isset($arrActiveCustomDesktopicons) && !empty($arrActiveCustomDesktopicons) && is_array($arrActiveCustomDesktopicons))

                                {

                                    $i = 1;

                                    foreach ($arrActiveCustomDesktopicons as $index => $imgUrl) {

                                        if(!empty($imgUrl)){

                                            sfsi_icon_generate_other_icon_effect_admin_html("custom",$arrActiveCustomDesktopicons,$index, $imgUrl,$i);

                                            $i++;

                                        }

                                    }

                                }

                            ?>

                        </div>

                        <div disabled class="col-md-12 inactiveSection rowmarginleft15 topmargin10">

                            

                            <label>Transition effect to those icons</label>

                            <select name="mouseover_other_icons_transition_effect">

                                

                                <option <?php echo 'noeffect'== $mouseover_other_icons_transition_effect? "selected=selected" : ""; ?> value="noeffect">No effect</option>

                                <option <?php echo 'flip'== $mouseover_other_icons_transition_effect? "selected=selected" : ""; ?> value="flip">Flip</option>

                            </select>

                        </div>

                        <div class="row mouseover-premium-notice rowmarginleft25">

                            <label>Above options are available in the</label>

                            <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=different_icon_mouseover&utm_medium=link">Premium Plugin</a>

                        </div>

                    </div><!-- row closes -->

                </div><!-- col-md-12 closes -->

            </div><!-- #animationSection closes -->

            <div class="Shuffle_auto"><p class="radio_section tab_3_option">

                <input name="sfsi_shuffle_icons" <?php echo ( $option3['sfsi_shuffle_icons']=='yes') ?  'checked="true"' : '' ;?>  type="checkbox" value="yes" class="styled"  />

                <label>Shuffle them automatically</label>

                <div class="sub_sub_box shuffle_sub"  >

                    <p class="radio_section tab_3_option">

                        <input name="sfsi_shuffle_Firstload" <?php echo ( $option3['sfsi_shuffle_Firstload']=='yes') ?  'checked="true"' : '' ;?>  type="checkbox" value="yes" class="styled"  />

                        <label>When the site is first loaded</label>

                    </p>

                    <p class="radio_section tab_3_option">

                        <input name="sfsi_shuffle_interval" <?php echo ( $option3['sfsi_shuffle_interval']=='yes') ?  'checked="true"' : '' ;?>  type="checkbox" value="yes" class="styled"  />

                        <label>Every</label>

                        <input class="smal_inpt" type="text" name="sfsi_shuffle_intervalTime" value="<?php echo ( $option3['sfsi_shuffle_intervalTime']!='') ?   $option3['sfsi_shuffle_intervalTime'] : '' ;?>"><label>seconds</label>

                    </p>

                </div>

    	   	</div>

		</div>

        <!--END icon Animation section   start -->