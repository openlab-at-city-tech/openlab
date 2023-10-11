<div id="tab2" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab2') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('Quiz Styles',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row">
        <div class="col-sm-2">
            <label>
                <?php echo __('Theme', $this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose your preferred template and customize it with options below Elegant Dark, Elegant Light, Classic Dark, Classic Light, Rect Dark, Rect Light, Modern Light and Modern Dark',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <div class="form-group row ays_themes_images_main_div">
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'elegant_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_elegant_dark" class="ays-quiz-theme-item">
                        <p><?php echo __('Elegant Dark',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/elegant_dark.JPG' ?>" alt="Elegant Dark">
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'elegant_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_elegant_light" class="ays-quiz-theme-item">
                        <p><?php echo __('Elegant Light',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/elegant_light.JPG' ?>" alt="Elegant Light">
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'classic_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_classic_dark" class="ays-quiz-theme-item">
                        <p><?php echo __('Classic Dark',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/classic_dark.jpg' ?>" alt="Classic Dark">
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'classic_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_classic_light" class="ays-quiz-theme-item">
                        <p><?php echo __('Classic Light',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/classic_light.jpg' ?>" alt="Classic Light">
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'rect_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_rect_dark" class="ays-quiz-theme-item">
                        <p><?php echo __('Rect Dark',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/rect_dark.JPG' ?>" alt="Rect Dark" >
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'rect_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_rect_light" class="ays-quiz-theme-item">
                        <p><?php echo __('Rect Light',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/rect_light.JPG' ?>" alt="Rect Light" >
                    </label>
                </div>
            </div>
            <hr>
            <div class="form-group row ays_themes_images_main_div">
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'modern_light') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_modern_light" class="ays-quiz-theme-item">
                        <p><?php echo __('Modern Light',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/modern_light.jpg' ?>" alt="Modern Light" >
                    </label>
                </div>
                <div class="ays_theme_image_div display_flex_theme col-sm-2 <?php echo ($quiz_theme == 'modern_dark') ? 'ays_active_theme_image' : '' ?>" style="padding:0;">
                    <label for="theme_modern_dark" class="ays-quiz-theme-item">
                        <p><?php echo __('Modern Dark',$this->plugin_name)?></p>
                        <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/themes/modern_dark.jpg' ?>" alt="Modern Dark" >
                    </label>
                </div>
            </div>
            <input type="radio" id="theme_elegant_dark" name="ays_quiz_theme" value="elegant_dark" <?php echo ($quiz_theme == 'elegant_dark') ? 'checked' : '' ?>>
            <input type="radio" id="theme_elegant_light" name="ays_quiz_theme" value="elegant_light" <?php echo ($quiz_theme == 'elegant_light') ? 'checked' : '' ?>>
            <input type="radio" id="theme_classic_dark" name="ays_quiz_theme" value="classic_dark" <?php echo ($quiz_theme == 'classic_dark') ? 'checked' : '' ?>>
            <input type="radio" id="theme_classic_light" name="ays_quiz_theme" value="classic_light" <?php echo ($quiz_theme == 'classic_light') ? 'checked' : '' ?>>
            <input type="radio" id="theme_rect_dark" name="ays_quiz_theme" value="rect_dark" <?php echo ($quiz_theme == 'rect_dark') ? 'checked' : '' ?>>
            <input type="radio" id="theme_rect_light" name="ays_quiz_theme" value="rect_light" <?php echo ($quiz_theme == 'rect_light') ? 'checked' : '' ?>>
            <input type="radio" id="theme_modern_light" name="ays_quiz_theme" value="modern_light" <?php echo ($quiz_theme == 'modern_light') ? 'checked' : '' ?>>
            <input type="radio" id="theme_modern_dark" name="ays_quiz_theme" value="modern_dark" <?php echo ($quiz_theme == 'modern_dark') ? 'checked' : '' ?>>

            <input type="hidden" id="ays-quiz-theme-type" value="">

        </div>
    </div> <!-- Quiz Theme -->
    <hr/>
    <div class="row">
        <div class="col-lg-7 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_quest_animation'>
                        <?php echo __('Animation effect', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Animation effect of transition between questions.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left d-flex">
                    <select class="ays-text-input ays-text-input-short" name="ays_quest_animation" id="ays_quest_animation">
                        <option <?php echo $quest_animation == "none" ? "selected" : ""; ?> value="none">None</option>
                        <option <?php echo $quest_animation == "fade" ? "selected" : ""; ?> value="fade">Fade</option>
                        <option <?php echo $quest_animation == "shake" ? "selected" : ""; ?> value="shake">Shake</option>
                        <option <?php echo $quest_animation == "rswing" ? "selected" : ""; ?> value="rswing">Swing right</option>
                        <option <?php echo $quest_animation == "lswing" ? "selected" : ""; ?> value="lswing">Swing left</option>
                    </select>
                    <button type="button" style="height:100%;" class="button ays_animate_animation"><?php echo __('Animate!', $this->plugin_name); ?></button>
                </div>
            </div> <!-- Animation effect -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-color'>
                        <?php echo __('Quiz Color', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Colors of the quiz main attributes(buttons, hover effect, progress bar, etc.)',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="text" class="ays-text-input" id='ays-quiz-color' name='ays_quiz_color' data-alpha="true"
                           value="<?php echo (isset($options['color'])) ? esc_attr( stripslashes($options['color']) ) : ''; ?>"/>
                </div>
            </div> <!-- Quiz Color -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-bg-color'>
                        <?php echo __('Background color', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background color of the quiz box. You can also choose the opacity(alfa) level on the right side',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="text" class="ays-text-input" id='ays-quiz-bg-color' data-alpha="true"
                           name='ays_quiz_bg_color'
                           value="<?php echo (isset($options['bg_color'])) ? esc_attr( stripslashes($options['bg_color']) ) : ''; ?>"/>
                </div>
            </div> <!-- Quiz Background Color -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-bg-color'>
                        <?php echo __('Text Color', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the text color inside the quiz and questions. It affects all kinds of texts and icons, including buttons.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="text" class="ays-text-input" id='ays-quiz-text-color' data-alpha="true"
                           name='ays_quiz_text_color'
                           value="<?php echo (isset($options['text_color'])) ? esc_attr( stripslashes($options['text_color']) ) : ''; ?>"/>
                </div>
            </div> <!-- Text Color -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-buttons-text-color'>
                        <?php echo __('Buttons text Color', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the text color of buttons inside the quiz and questions. It affects only to buttons.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="text" class="ays-text-input" id='ays-quiz-buttons-text-color' data-alpha="true"
                           name='ays_buttons_text_color'
                           value="<?php echo $buttons_text_color; ?>"/>
                </div>
            </div> <!-- Buttosn Text Color -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-width'>
                        <?php echo __('Quiz width', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container width in pixels.Set it 0 or leave it blank for making a quiz with 100% width. It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-6 ays_divider_left ays-display-flex">
                    <div class="ays_quiz_display_flex_width">
                        <div>
                            <input type="number" class="ays-text-input ays-text-input-short" id='ays-quiz-width'
                                   name='ays_quiz_width'
                                   value="<?php echo (isset($options['width'])) ? $options['width'] : ''; ?>"/>
                            <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                        </div>
                        <div class="ays_quiz_dropdown_max_width">
                            <select id="ays_quiz_width_by_percentage_px" name="ays_quiz_width_by_percentage_px" class="ays-text-input ays-text-input-short" style="display:inline-block; width: 60px;">
                                <option value="pixels" <?php echo $quiz_width_by_percentage_px == "pixels" ? "selected" : ""; ?>><?php echo __( "px", $this->plugin_name ); ?></option>
                                <option value="percentage" <?php echo $quiz_width_by_percentage_px == "percentage" ? "selected" : ""; ?>><?php echo __( "%", $this->plugin_name ); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div> <!-- Quiz width -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_mobile_max_width'>
                        <?php echo __('Quiz max-width for mobile', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container max-width for mobile in percentage. This option will work for the screens with less than 640 pixels width.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_mobile_max_width'
                           name='ays_mobile_max_width' style="display:inline-block;"
                           value="<?php echo $mobile_max_width; ?>"/>
                           <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                    </div>
                    <div class="ays_quiz_dropdown_max_width">
                        <input type="text" value="%" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Quiz max-width for mobile -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays-quiz-height'>
                        <?php echo __('Quiz min-height', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz minimal height in pixels',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short"
                           id='ays-quiz-height'
                           name='ays_quiz_height'
                           value="<?php echo (isset($options['height'])) ? $options['height'] : ''; ?>"/>
                           </div>
                    <div class="ays_quiz_dropdown_max_width">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>

                </div>
            </div> <!-- Quiz min height -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_quiz_title_transformation">
                        <?php echo __('Quiz title transformation', $this->plugin_name ); ?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php
                            echo __("Specify how to capitalize a title text of your quiz.", $this->plugin_name) .
                                "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                    "<li>". __('Uppercase – Transforms all characters to uppercase',$this->plugin_name) ."</li>".
                                    "<li>". __('Lowercase – ransforms all characters to lowercase',$this->plugin_name) ."</li>".
                                    "<li>". __('Capitalize – Transforms the first character of each word to uppercase',$this->plugin_name) ."</li>".
                                "</ul>";
                            ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select name="ays_quiz_title_transformation" id="ays_quiz_title_transformation" class="ays-text-input ays-text-input-short" style="display:block;">
                        <option value="uppercase" <?php echo $quiz_title_transformation == 'uppercase' ? 'selected' : ''; ?>><?php echo __( "Uppercase", $this->plugin_name ); ?></option>
                        <option value="lowercase" <?php echo $quiz_title_transformation == 'lowercase' ? 'selected' : ''; ?>><?php echo __( "Lowercase", $this->plugin_name ); ?></option>
                        <option value="capitalize" <?php echo $quiz_title_transformation == 'capitalize' ? 'selected' : ''; ?>><?php echo __( "Capitalize", $this->plugin_name ); ?></option>
                        <option value="none" <?php echo $quiz_title_transformation == 'none' ? 'selected' : ''; ?>><?php echo __( "None", $this->plugin_name ); ?></option>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_quiz_title_font_size'>
                        <?php echo __('Quiz title font size', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Set your preferred text size for the Quiz Title. The default size is 21px.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_answers_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_quiz_title_font_size' name='ays_quiz_title_font_size' value="<?php echo $quiz_title_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_quiz_title_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_quiz_title_mobile_font_size' name='ays_quiz_title_mobile_font_size' value="<?php echo $quiz_title_mobile_font_size; ?>"/>
                                </div>
                            <div class="ays_quiz_dropdown_max_width">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_quiz_enable_title_text_shadow">
                        <?php echo __('Quiz title text shadow',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the text shadow of the quiz title.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide" id="ays_quiz_enable_title_text_shadow" name="ays_quiz_enable_title_text_shadow" <?php echo ($quiz_enable_title_text_shadow == 'on') ? 'checked' : ''; ?>/>
                    <label for="ays_quiz_enable_title_text_shadow" class="ays_switch_toggle">Toggle</label>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top <?php echo ($quiz_enable_title_text_shadow == 'on') ? '' : 'display_none'; ?>" style="margin-top: 10px; padding-top: 10px;">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="ays_quiz_title_text_shadow_color">
                                    <?php echo __('Text shadow color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the text shadow of the quiz title.',$this->plugin_name ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                 </label>
                                <input type="text" class="ays-text-input" id='ays_quiz_title_text_shadow_color' name='ays_quiz_title_text_shadow_color' data-alpha="true" data-default-color="#333" value="<?php echo $quiz_title_text_shadow_color; ?>"/>
                           </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="col-sm-3" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('X', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_title_text_shadow_x_offset' name='ays_quiz_title_text_shadow_x_offset' value="<?php echo $quiz_title_text_shadow_x_offset; ?>" />
                                </div>
                                <div class="col-sm-3 ays_divider_left" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('Y', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_title_text_shadow_y_offset' name='ays_quiz_title_text_shadow_y_offset' value="<?php echo $quiz_title_text_shadow_y_offset; ?>" />
                                </div>
                                <div class="col-sm-3 ays_divider_left" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('Z', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_title_text_shadow_z_offset' name='ays_quiz_title_text_shadow_z_offset' value="<?php echo $quiz_title_text_shadow_z_offset; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Quiz title text shadow -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_quiz_image_height'>
                        <?php echo __('Quiz image height', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Set quiz image height in pixels. It accepts only number values.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_quiz_image_height' name='ays_quiz_image_height' value="<?php echo $quiz_image_height; ?>"/>
                    </div>
                    <div class="ays_quiz_dropdown_max_width">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label>
                        <?php echo __('Questions Image Styles',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It affects the images chosen from “Add Image” not from “Add media” on the Edit question page',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="form-group row">
                        <div class="col-sm-12 ays_quiz_display_flex_width">
                            <div>
                                <label for="ays_image_width">
                                    <?php echo __('Image Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Question image width in pixels. Set it 0 or leave it blank for making it 100%. It accepts only numeric values.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <input type="number" class="ays-text-input ays-text-input-short" id="ays_image_width" name="ays_image_width" value="<?php echo $image_width; ?>"/>
                                <span class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: center;">
                                <select id="ays_quiz_image_width_by_percentage_px" name="ays_quiz_image_width_by_percentage_px" class="ays-text-input ays-text-input-short" style="display:inline-block; width: 60px; margin-top: .5rem;">
                                    <option value="pixels" <?php echo $quiz_image_width_by_percentage_px == "pixels" ? "selected" : ""; ?>><?php echo __( "px", $this->plugin_name ); ?></option>
                                    <option value="percentage" <?php echo $quiz_image_width_by_percentage_px == "percentage" ? "selected" : ""; ?>><?php echo __( "%", $this->plugin_name ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group row">
                        <div class="col-sm-12 ays_quiz_display_flex_width">
                            <div>
                                <label for="ays_image_height">
                                    <?php echo __('Image Height',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Question image height in pixels. It accepts only number values.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                                <input type="number" class="ays-text-input ays-text-input-short" id="ays_image_height" name="ays_image_height" value="<?php echo (isset($options['image_height']) && $options['image_height'] != '') ? $options['image_height'] : ''; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="ays_image_sizing">
                                <?php echo __('Image sizing', $this->plugin_name ); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It helps to configure the scale of the images inside the quiz in case of differences between the sizes.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                            <select name="ays_image_sizing" id="ays_image_sizing" class="ays-text-input ays-text-input-short" style="display:block;">
                                <option value="cover" <?php echo $image_sizing == 'cover' ? 'selected' : ''; ?>><?php echo __( "Cover", $this->plugin_name ); ?></option>
                                <option value="contain" <?php echo $image_sizing == 'contain' ? 'selected' : ''; ?>><?php echo __( "Contain", $this->plugin_name ); ?></option>
                                <option value="none" <?php echo $image_sizing == 'none' ? 'selected' : ''; ?>><?php echo __( "None", $this->plugin_name ); ?></option>
                                <option value="unset" <?php echo $image_sizing == 'unset' ? 'selected' : ''; ?>><?php echo __( "Unset", $this->plugin_name ); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div> <!-- Questions Image Styles -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_enable_border">
                        <?php echo __('Quiz container border',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow quiz container border',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide"
                           id="ays_enable_border"
                           name="ays_enable_border"
                           value="on"
                           <?php echo ($enable_border) ? 'checked' : ''; ?>/>
                    <label for="ays_enable_border" class="ays_switch_toggle">Toggle</label>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                       <div class="ays_quiz_display_flex_width">
                            <div>
                                <label for="ays_quiz_border_width">
                                    <?php echo __('Border width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of quiz container border',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                 </label>
                                <input type="number" class="ays-text-input ays-text-input-short" id='ays_quiz_border_width'
                                   name='ays_quiz_border_width'
                                   value="<?php echo $quiz_border_width; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                        <label for="ays_quiz_border_style">
                            <?php echo __('Border style',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The style of quiz container border',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                        <select id="ays_quiz_border_style"
                                name="ays_quiz_border_style"
                                class="ays-text-input">
                            <option <?php echo ($quiz_border_style == 'solid') ? 'selected' : ''; ?> value="solid">Solid</option>
                            <option <?php echo ($quiz_border_style == 'dashed') ? 'selected' : ''; ?> value="dashed">Dashed</option>
                            <option <?php echo ($quiz_border_style == 'dotted') ? 'selected' : ''; ?> value="dotted">Dotted</option>
                            <option <?php echo ($quiz_border_style == 'double') ? 'selected' : ''; ?> value="double">Double</option>
                            <option <?php echo ($quiz_border_style == 'groove') ? 'selected' : ''; ?> value="groove">Groove</option>
                            <option <?php echo ($quiz_border_style == 'ridge') ? 'selected' : ''; ?> value="ridge">Ridge</option>
                            <option <?php echo ($quiz_border_style == 'inset') ? 'selected' : ''; ?> value="inset">Inset</option>
                            <option <?php echo ($quiz_border_style == 'outset') ? 'selected' : ''; ?> value="outset">Outset</option>
                            <option <?php echo ($quiz_border_style == 'none') ? 'selected' : ''; ?> value="none">None</option>
                        </select>
                    </div>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_border) ? '' : 'display:none;' ?>">
                        <label for="ays_quiz_border_color">
                            <?php echo __('Border color',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the quiz container border',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                        <input id="ays_quiz_border_color" class="ays-text-input"  data-alpha="true" type="text" name='ays_quiz_border_color'
                               value="<?php echo $quiz_border_color; ?>"
                               data-default-color="#000000">
                    </div>
                </div>
            </div> <!-- Quiz container border -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_quiz_border_radius">
                        <?php echo __('Border radius', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz container border-radius in pixels. It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short"
                           id="ays_quiz_border_radius"
                           name="ays_quiz_border_radius"
                           value="<?php echo $quiz_border_radius; ?>"/>
                   </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Quiz border radius -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_enable_box_shadow">
                        <?php echo __('Box shadow',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow quiz container box shadow',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide"
                           id="ays_enable_box_shadow"
                           name="ays_enable_box_shadow"
                           <?php echo ($enable_box_shadow == 'on') ? 'checked' : ''; ?>/>
                    <label for="ays_enable_box_shadow" class="ays_switch_toggle">Toggle</label>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($enable_box_shadow == 'on') ? '' : 'display:none;' ?>">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="ays-quiz-box-shadow-color">
                                    <?php echo __('Box shadow color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the shadow of the quiz container',$this->plugin_name ); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                 </label>
                                <input type="text" class="ays-text-input" id='ays-quiz-box-shadow-color' name='ays_quiz_box_shadow_color' data-alpha="true" data-default-color="#000000" value="<?php echo $box_shadow_color; ?>"/>
                           </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4" style="display: inline-block;">
                                <span class="ays_quiz_small_hint_text"><?php echo __('X', $this->plugin_name); ?></span>
                                <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_box_shadow_x_offset' name='ays_quiz_box_shadow_x_offset' value="<?php echo $quiz_box_shadow_x_offset; ?>" />
                            </div>
                            <div class="col-sm-4 ays_divider_left" style="display: inline-block;">
                                <span class="ays_quiz_small_hint_text"><?php echo __('Y', $this->plugin_name); ?></span>
                                <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_box_shadow_y_offset' name='ays_quiz_box_shadow_y_offset' value="<?php echo $quiz_box_shadow_y_offset; ?>" />
                            </div>
                            <div class="col-sm-4 ays_divider_left" style="display: inline-block;">
                                <span class="ays_quiz_small_hint_text"><?php echo __('Z', $this->plugin_name); ?></span>
                                <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_box_shadow_z_offset' name='ays_quiz_box_shadow_z_offset' value="<?php echo $quiz_box_shadow_z_offset; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Quiz box shadow -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label>
                        <?php echo __('Background image', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Background image of the container. You can choose different images for each question from the Settings tab on the Edit question page. The background-size is set “Cover” by default for not scaling the image.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <a href="javascript:void(0)" style="<?php echo $quiz_bg_image == '' ? 'display:inline-block' : 'display:none'; ?>" class="add-quiz-bg-image"><?php echo $bg_image_text; ?></a>
                    <input type="hidden" id="ays_quiz_bg_image" name="ays_quiz_bg_image"
                           value="<?php echo $quiz_bg_image; ?>"/>
                    <div class="ays-quiz-bg-image-container" style="<?php echo $quiz_bg_image == '' ? 'display:none' : 'display:block'; ?>">
                        <span class="ays-edit-quiz-bg-img">
                            <i class="ays_fa ays_fa_pencil_square_o"></i>
                        </span>
                        <span class="ays-remove-quiz-bg-img"></span>
                        <img src="<?php echo $quiz_bg_image; ?>" id="ays-quiz-bg-img"/>
                    </div>
                    <hr/>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="ays_quiz_bg_image_position">
                                <?php echo __( "Background image position", $this->plugin_name ); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The position of background image of the quiz',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                            <select id="ays_quiz_bg_image_position" name="ays_quiz_bg_image_position" class="ays-text-input ays-text-input-short" style="display:inline-block;">
                                <option value="left top" <?php echo $quiz_bg_image_position == "left top" ? "selected" : ""; ?>><?php echo __( "Left Top", $this->plugin_name ); ?></option>
                                <option value="left center" <?php echo $quiz_bg_image_position == "left center" ? "selected" : ""; ?>><?php echo __( "Left Center", $this->plugin_name ); ?></option>
                                <option value="left bottom" <?php echo $quiz_bg_image_position == "left bottom" ? "selected" : ""; ?>><?php echo __( "Left Bottom", $this->plugin_name ); ?></option>
                                <option value="center top" <?php echo $quiz_bg_image_position == "center top" ? "selected" : ""; ?>><?php echo __( "Center Top", $this->plugin_name ); ?></option>
                                <option value="center center" <?php echo $quiz_bg_image_position == "center center" ? "selected" : ""; ?>><?php echo __( "Center Center", $this->plugin_name ); ?></option>
                                <option value="center bottom" <?php echo $quiz_bg_image_position == "center bottom" ? "selected" : ""; ?>><?php echo __( "Center Bottom", $this->plugin_name ); ?></option>
                                <option value="right top" <?php echo $quiz_bg_image_position == "right top" ? "selected" : ""; ?>><?php echo __( "Right Top", $this->plugin_name ); ?></option>
                                <option value="right center" <?php echo $quiz_bg_image_position == "right center" ? "selected" : ""; ?>><?php echo __( "Right Center", $this->plugin_name ); ?></option>
                                <option value="right bottom" <?php echo $quiz_bg_image_position == "right bottom" ? "selected" : ""; ?>><?php echo __( "Right Bottom", $this->plugin_name ); ?></option>
                            </select>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group row">
                        <div class="col-sm-8">
                            <label for="ays_quiz_bg_img_in_finish_page">
                                <?php echo __( "Hide background image on result page", $this->plugin_name ); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('If this option is enabled background image of quiz will disappear on the result page.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <input type="checkbox" class="ays_toggle ays_toggle_slide"
                                   id="ays_quiz_bg_img_in_finish_page"
                                   name="ays_quiz_bg_img_in_finish_page"
                                    <?php echo ($quiz_bg_img_in_finish_page) ? 'checked' : ''; ?>/>
                            <label for="ays_quiz_bg_img_in_finish_page" style="display:inline-block;margin-left:10px;" class="ays_switch_toggle">Toggle</label>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group row">
                        <div class="col-sm-8">
                            <label for="ays_quiz_bg_img_on_start_page">
                                <?php echo __( "Hide background image on start page", $this->plugin_name ); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('If this option is enabled background image of quiz will disappear on the start page.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <input type="checkbox" class="ays_toggle ays_toggle_slide" id="ays_quiz_bg_img_on_start_page" name="ays_quiz_bg_img_on_start_page" <?php echo ($quiz_bg_img_on_start_page) ? 'checked' : ''; ?>/>
                            <label for="ays_quiz_bg_img_on_start_page" style="display:inline-block;margin-left:10px;" class="ays_switch_toggle">Toggle</label>
                        </div>
                    </div>
                </div>
            </div> <!-- Quiz background image -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays-enable-background-gradient">
                        <?php echo __('Quiz background gradient',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color gradient of the quiz background',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide"
                           id="ays-enable-background-gradient"
                           name="ays_enable_background_gradient"
                            <?php echo ($enable_background_gradient) ? 'checked' : ''; ?>/>
                    <label for="ays-enable-background-gradient" class="ays_switch_toggle">Toggle</label>
                    <div class="row ays_toggle_target" style="margin: 10px 0 0 0; padding-top: 10px; <?php echo ($enable_background_gradient) ? '' : 'display:none;' ?>">
                        <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                            <label for='ays-background-gradient-color-1'>
                                <?php echo __('Color 1', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color 1 of the quiz background gradient',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                            <input type="text" class="ays-text-input" id='ays-background-gradient-color-1' name='ays_background_gradient_color_1' data-alpha="true" value="<?php echo $background_gradient_color_1; ?>"/>
                        </div>
                        <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                            <label for='ays-background-gradient-color-2'>
                                <?php echo __('Color 2', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Color 2 of the quiz background gradient',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                            <input type="text" class="ays-text-input" id='ays-background-gradient-color-2' name='ays_background_gradient_color_2' data-alpha="true" value="<?php echo $background_gradient_color_2; ?>"/>
                        </div>
                        <div class="col-sm-12 ays_divider_top" style="margin-top: 10px; padding-top: 10px;">
                            <label for="ays_quiz_gradient_direction">
                                <?php echo __('Gradient direction',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The direction of the color gradient.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                            <select id="ays_quiz_gradient_direction" name="ays_quiz_gradient_direction" class="ays-text-input ays-text-input-short">
                                <option <?php echo ($quiz_gradient_direction == 'vertical') ? 'selected' : ''; ?> value="vertical"><?php echo __( 'Vertical', $this->plugin_name); ?></option>
                                <option <?php echo ($quiz_gradient_direction == 'horizontal') ? 'selected' : ''; ?> value="horizontal"><?php echo __( 'Horizontal', $this->plugin_name); ?></option>
                                <option <?php echo ($quiz_gradient_direction == 'diagonal_left_to_right') ? 'selected' : ''; ?> value="diagonal_left_to_right"><?php echo __( 'Diagonal left to right', $this->plugin_name); ?></option>
                                <option <?php echo ($quiz_gradient_direction == 'diagonal_right_to_left') ? 'selected' : ''; ?> value="diagonal_right_to_left"><?php echo __( 'Diagonal right to left', $this->plugin_name); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div> <!-- Quiz background gradient -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_buttons_position">
                        <?php echo __('Buttons position',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the position of buttons of the quiz.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select id="ays_buttons_position" name="ays_buttons_position" class="ays-text-input ays-text-input-short">
                        <option <?php echo ($buttons_position == 'center') ? 'selected' : ''; ?> value="center"><?php echo __( 'Center', $this->plugin_name); ?></option>
                        <option <?php echo ($buttons_position == 'flex-start') ? 'selected' : ''; ?> value="flex-start"><?php echo __( 'Left', $this->plugin_name); ?></option>
                        <option <?php echo ($buttons_position == 'flex-end') ? 'selected' : ''; ?> value="flex-end"><?php echo __( 'Right', $this->plugin_name); ?></option>
                        <option <?php echo ($buttons_position == 'space-between') ? 'selected' : ''; ?> value="space-between"><?php echo __( 'Space Between', $this->plugin_name); ?></option>
                    </select>
                </div>
            </div> <!-- Buttons position -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_progress_bar_style">
                        <?php echo __('Progress bar style',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Design of the progress bar which will appear on the finish page only. It will show the user’s score.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select id="ays_progress_bar_style" name="ays_progress_bar_style" class="ays-text-input ays-text-input-short">
                        <option <?php echo ($progress_bar_style == 'first') ? 'selected' : ''; ?> value="first"><?php echo __( 'Rounded', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_bar_style == 'second') ? 'selected' : ''; ?> value="second"><?php echo __( 'Rectangle', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_bar_style == 'third') ? 'selected' : ''; ?> value="third"><?php echo __( 'With stripes', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_bar_style == 'fourth') ? 'selected' : ''; ?> value="fourth"><?php echo __( 'With stripes and animation', $this->plugin_name); ?></option>
                    </select>
                    <div style="margin:20px 0;">
                        <div class='ays-progress first <?php echo ($progress_bar_style == 'first') ? "display_block" : ""; ?>'>
                            <span class='ays-progress-value first' style='width:67%;'>67%</span>
                            <div class="ays-progress-bg first">
                                <div class="ays-progress-bar first" style='width:67%;'></div>
                            </div>
                        </div>

                        <div class='ays-progress second <?php echo ($progress_bar_style == 'second') ? "display_block" : ""; ?>'>
                            <span class='ays-progress-value second' style='width:88%;'>88%</span>
                            <div class="ays-progress-bg second">
                                <div class="ays-progress-bar second" style='width:88%;'></div>
                            </div>
                        </div>

                        <div class="ays-progress third <?php echo ($progress_bar_style == 'third') ? "display_block" : ""; ?>">
                            <span class="ays-progress-value third">55%</span>
                            <div class="ays-progress-bg third">
                                <div class="ays-progress-bar third" style='width:55%;'></div>
                            </div>
                        </div>

                        <div class="ays-progress fourth <?php echo ($progress_bar_style == 'fourth') ? "display_block" : ""; ?>">
                            <span class="ays-progress-value fourth">34%</span>
                            <div class="ays-progress-bg fourth">
                                <div class="ays-progress-bar fourth" style="width:34%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Progress bar style -->
            <hr>
            <!-- Progress Live bar style start -->
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_progress_bar_style">
                        <?php echo __('Progress live bar style',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Design of the progress live bar which will appear during passing quiz. It will show the user’s score.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select id="ays_progress_live_bar_style" name="ays_progress_live_bar_style" class="ays-text-input ays-text-input-short">
                        <option <?php echo ($progress_live_bar_style == 'default') ? 'selected' : ''; ?> value="default"><?php echo __( 'Default', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_live_bar_style == 'second') ? 'selected' : ''; ?> value="second"><?php echo __( 'Rectangle', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_live_bar_style == 'third') ? 'selected' : ''; ?> value="third"><?php echo __( 'With stripes', $this->plugin_name); ?></option>
                        <option <?php echo ($progress_live_bar_style == 'fourth') ? 'selected' : ''; ?> value="fourth"><?php echo __( 'With stripes and animation', $this->plugin_name); ?></option>
                    </select>
                    <div style="margin:20px 0;">
                        <div class="ays-progress default <?php echo ($progress_live_bar_style == 'default') ? "display_block" : ""; ?>">
                            <span class="ays-progress-value ays-live-default" aria-valuenow="100"><?php echo ($progress_live_bar_style == 'default') ? "100%" : ""; ?></span>
                            <div class="ays-progress-bg ays-live-default-line"></div>
                        </div>

                        <div class='ays-progress second <?php echo ($progress_live_bar_style == 'second') ? "display_block" : ""; ?>'>
                            <span class='ays-progress-value second' style='width:67%;'>67%</span>
                            <div class="ays-progress-bg second">
                                <div class="ays-progress-bar second" style='width:67%;'></div>
                            </div>
                        </div>

                        <div class='ays-progress third <?php echo ($progress_live_bar_style == 'third') ? "display_block" : ""; ?> ays-live-preview'>
                            <span class='ays-progress-value third ays-live-third' style='width:88%;'>88%</span>
                            <div class="ays-progress-bg third ays-live-preview">
                                <div class="ays-progress-bar third ays-live-preview" style='width:88%;'></div>
                            </div>
                        </div>

                        <div class="ays-progress fourth <?php echo ($progress_live_bar_style == 'fourth') ? "display_block" : ""; ?> ays-live-preview">
                            <span class="ays-progress-value fourth ays-live-preview">55%</span>
                            <div class="ays-progress-bg fourth ays-live-preview">
                                <div class="ays-progress-bar fourth ays-live-preview" style='width:55%;'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Progress live bar style -->
            <!-- Progress Live bar style end -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_custom_class">
                        <?php echo __('Custom class for quiz container',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Custom HTML class for quiz container. You can use your class for adding your custom styles for quiz container. Example: p{color:red !important}',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="text" class="ays-text-input" name="ays_custom_class" id="ays_custom_class" placeholder="myClass myAnotherClass..." value="<?php echo $custom_class; ?>">
                </div>
            </div> <!-- Custom class for quiz container -->
        </div>
        <div class="col-lg-5 col-sm-12 ays_divider_left" style="position:relative;">
            <div id="ays_styles_tab" style="position:sticky;top:50px; margin:auto;">
                <div class="ays-quiz-live-container ays-quiz-live-container-1">
                    <div class="step active-step">
                        <div class="ays-abs-fs">
                            <img src="" alt="Ays Question Image" class="ays-quiz-live-image">
                            <p class="ays-fs-title ays-quiz-live-title"></p>
                            <div class="ays-fs-subtitle ays-quiz-live-subtitle"></div>
                            <input type="hidden" name="ays_quiz_id" value="2">
                            <div class="ays_buttons_div">
                                <input type="button" name="next" class="action-button ays-quiz-live-button" style="padding:0;"
                                       value="<?php echo __( "Start", $this->plugin_name ); ?>">
                            </div>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="ays-quiz-live-container ays-quiz-live-container-2" style="display:none;">
                    <div class="step active-step">
                        <div class="ays-abs-fs">
                            <img src="" alt="Ays Question Image" class="ays-quiz-live-image">
                            <p class="ays-fs-title ays-quiz-live-title"></p>
                            <div class="ays-fs-subtitle ays-quiz-live-subtitle"></div>
                            <input type="hidden" name="ays_quiz_id" value="2">
                            <div class="ays_buttons_div">
                                <input type="button" name="next" class="action-button ays-quiz-live-button" style="padding:0;"
                                       value="<?php echo __( "Start", $this->plugin_name ); ?>">
                            </div>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Quiz Styles -->
    <hr/>
    <p class="ays-subtitle" style="margin-top:0;"><?php echo __('Answers Styles',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row">
        <div class="col-lg-7 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_answers_font_size'>
                        <?php echo __('Answer font size', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The font size of the answers in pixels in the quiz. It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_answers_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_answers_font_size'name='ays_answers_font_size' value="<?php echo $answers_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_answers_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_answers_mobile_font_size'name='ays_answers_mobile_font_size' value="<?php echo $answers_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Answers font size -->
            <!-- ================= -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_answers_font_size'>
                        <?php echo __('Question font size', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The font size of the questions in pixels in the quiz (only for <p> tag). It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_question_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_question_font_size'name='ays_question_font_size' value="<?php echo $question_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_question_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_question_mobile_font_size'name='ays_question_mobile_font_size' value="<?php echo $question_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Question font size -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_right_answers_font_size">
                        <?php echo __('Font size for the right answer',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the Font Size for the Message displayed for the right answer( only for <p> tag ).',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_right_answers_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_right_answers_font_size' name='ays_right_answers_font_size' value="<?php echo $right_answers_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_right_answers_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_right_answers_mobile_font_size' name='ays_right_answers_mobile_font_size' value="<?php echo $right_answers_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Font size for the right answer -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_wrong_answers_font_size">
                        <?php echo __('Font size for the wrong answer',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the Font Size for the Message displayed for the wrong answer( only for <p> tag ).',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_question_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_wrong_answers_font_size' name='ays_wrong_answers_font_size' value="<?php echo $wrong_answers_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_wrong_answers_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_wrong_answers_mobile_font_size' name='ays_wrong_answers_mobile_font_size' value="<?php echo $wrong_answers_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Font size for the wrong answer -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_quest_explanation_font_size">
                        <?php echo __('Font size for the question explanation',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the Font Size for the question explanation text( only for <p> tag ).',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_quest_explanation_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_quest_explanation_font_size' name='ays_quest_explanation_font_size' value="<?php echo $quest_explanation_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_quest_explanation_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_quest_explanation_mobile_font_size' name='ays_quest_explanation_mobile_font_size' value="<?php echo $quest_explanation_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Font size for the question explanation -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_note_text_font_size">
                        <?php echo __('Font size for the note text',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the Font Size for the Message displayed for the note text( only for <p> tag ).',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_note_text_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_note_text_font_size' name='ays_note_text_font_size' value="<?php echo $note_text_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_note_text_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_note_text_mobile_font_size' name='ays_note_text_mobile_font_size' value="<?php echo $note_text_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Font size for the wrong answer -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_view">
                        <?php echo __('Answer view',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the design of the answers of question.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select class="ays-text-input ays-text-input-short" id="ays_answers_view" name="ays_answers_view">
                        <option value="list" <?php echo (isset($options['answers_view']) && $options['answers_view'] == 'list') ? 'selected' : ''; ?>>
                            <?php echo __('List',$this->plugin_name)?>
                        </option>
                        <option value="grid" <?php echo (isset($options['answers_view']) && $options['answers_view'] == 'grid') ? 'selected' : ''; ?>>
                            <?php echo __('Grid',$this->plugin_name)?>
                        </option>
                    </select>
                </div>
            </div> <!-- Answers view -->
            <!-- ______________________________ -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_disable_hover_effect">
                        <?php echo __('Disable answer hover',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable the hover effect for the answers.', $this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" id="ays_disable_hover_effect" name="ays_disable_hover_effect" class="ays_toggle ays_toggle_slide" <?php echo ($disable_hover_effect) ? 'checked' : ''; ?>/>
                    <label for="ays_disable_hover_effect" class="ays_switch_toggle">Toggle</label>
                </div>
            </div> <!-- Disable answer hover -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_limitation_message">
                        <?php echo __( 'Question text alignment', $this->plugin_name ); ?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<?php echo __( 'Align the text of your questions to the left, center, or right.', $this->plugin_name ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="form-check form-check-inline checkbox_ays">
                        <input type="radio" id="ays_quiz_question_text_alignment_left" class="form-check-input" name="ays_quiz_question_text_alignment" value="left" <?php echo ($quiz_question_text_alignment == 'left') ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_quiz_question_text_alignment_left"><?php echo __( 'Left', $this->plugin_name ); ?></label>
                    </div>
                    <div class="form-check form-check-inline checkbox_ays">
                        <input type="radio" id="ays_quiz_question_text_alignment_center" class="form-check-input" name="ays_quiz_question_text_alignment" value="center" <?php echo ($quiz_question_text_alignment == 'center') ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_quiz_question_text_alignment_center"><?php echo __( 'Center', $this->plugin_name ); ?></label>
                    </div>
                    <div class="form-check form-check-inline checkbox_ays">
                        <input type="radio" id="ays_quiz_question_text_alignment_right" class="form-check-input" name="ays_quiz_question_text_alignment" value="right" <?php echo ($quiz_question_text_alignment == 'right') ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_quiz_question_text_alignment_right"><?php echo __( 'Right', $this->plugin_name ); ?></label>
                    </div>
                </div>
            </div> <!-- Question text alignment -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_object_fit">
                        <?php echo __('Answer object-fit',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the fit of the images in the answers in the questions.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select class="ays-text-input ays-text-input-short" id="ays_answers_object_fit" name="ays_answers_object_fit">
                        <option value="cover" <?php echo (isset($options['answers_object_fit']) && $options['answers_object_fit'] == 'cover') ? 'selected' : ''; ?>>
                            <?php echo __('Cover',$this->plugin_name)?>
                        </option>
                        <option value="fill" <?php echo (isset($options['answers_object_fit']) && $options['answers_object_fit'] == 'fill') ? 'selected' : ''; ?>>
                            <?php echo __('Fill',$this->plugin_name)?>
                        </option>
                        <option value="contain" <?php echo (isset($options['answers_object_fit']) && $options['answers_object_fit'] == 'contain') ? 'selected' : ''; ?>>
                            <?php echo __('Contain',$this->plugin_name)?>
                        </option>
                        <option value="scale-down" <?php echo (isset($options['answers_object_fit']) && $options['answers_object_fit'] == 'scale-down') ? 'selected' : ''; ?>>
                            <?php echo __('Scale-down',$this->plugin_name)?>
                        </option>
                        <option value="none" <?php echo (isset($options['answers_object_fit']) && $options['answers_object_fit'] == 'none') ? 'selected' : ''; ?>>
                            <?php echo __('None',$this->plugin_name)?>
                        </option>
                    </select>
                </div>
            </div> <!-- Answers Object fit -->
            <!-- ______________________________ -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_padding">
                        <?php echo __('Answer padding',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Padding of answers.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_answers_padding' name='ays_answers_padding' value="<?php echo $answers_padding; ?>"/>
                    </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Answers padding -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_margin">
                        <?php echo __('Answer gap',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Gap between answers.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_answers_margin' name='ays_answers_margin' value="<?php echo $answers_margin; ?>"/>
                    </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Answers gap -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_border">
                        <?php echo __('Answer border',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow answer border',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide" id="ays_answers_border" name="ays_answers_border" value="on"
                           <?php echo ($answers_border) ? 'checked' : ''; ?>/>
                    <label for="ays_answers_border" class="ays_switch_toggle">Toggle</label>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                        <div class="ays_quiz_display_flex_width">
                            <div>
                                <label for="ays_answers_border_width">
                                    <?php echo __('Border width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of answers border',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                 </label>
                                <input type="number" class="ays-text-input" id='ays_answers_border_width' name='ays_answers_border_width'
                                       value="<?php echo $answers_border_width; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                        <label for="ays_answers_border_style">
                            <?php echo __('Border style',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The style of answers border',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                        <select id="ays_answers_border_style" name="ays_answers_border_style" class="ays-text-input">
                            <option <?php echo ($answers_border_style == 'solid') ? 'selected' : ''; ?> value="solid">Solid</option>
                            <option <?php echo ($answers_border_style == 'dashed') ? 'selected' : ''; ?> value="dashed">Dashed</option>
                            <option <?php echo ($answers_border_style == 'dotted') ? 'selected' : ''; ?> value="dotted">Dotted</option>
                            <option <?php echo ($answers_border_style == 'double') ? 'selected' : ''; ?> value="double">Double</option>
                            <option <?php echo ($answers_border_style == 'groove') ? 'selected' : ''; ?> value="groove">Groove</option>
                            <option <?php echo ($answers_border_style == 'ridge') ? 'selected' : ''; ?> value="ridge">Ridge</option>
                            <option <?php echo ($answers_border_style == 'inset') ? 'selected' : ''; ?> value="inset">Inset</option>
                            <option <?php echo ($answers_border_style == 'outset') ? 'selected' : ''; ?> value="outset">Outset</option>
                            <option <?php echo ($answers_border_style == 'none') ? 'selected' : ''; ?> value="none">None</option>
                        </select>
                    </div>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_border) ? '' : 'display:none;' ?>">
                        <label for="ays_answers_border_color">
                            <?php echo __('Border color',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The color of the answers border',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                        <input id="ays_answers_border_color" class="ays-text-input" type="text" data-alpha="true" name='ays_answers_border_color'
                               value="<?php echo $answers_border_color; ?>" data-default-color="#000000">
                    </div>
                </div>
            </div> <!-- Answers border -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_answers_box_shadow">
                        <?php echo __('Answers box shadow',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow answer container box shadow',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide"
                           id="ays_answers_box_shadow" name="ays_answers_box_shadow"
                           <?php echo ($answers_box_shadow) ? 'checked' : ''; ?>/>
                    <label for="ays_answers_box_shadow" class="ays_switch_toggle">Toggle</label>
                    <div class="col-sm-12 ays_toggle_target ays_divider_top" style="margin-top: 10px; padding-top: 10px; <?php echo ($answers_box_shadow) ? '' : 'display:none;' ?>">
                        <div class="form-group row">
                            <div class="col-sm-12">
                            <label for="ays_answers_box_shadow_color">
                                <?php echo __('Answer box-shadow color',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The shadow color of answers container',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                             </label>
                            <input type="text" class="ays-text-input" id='ays_answers_box_shadow_color' name='ays_answers_box_shadow_color' data-alpha="true" data-default-color="#000000" value="<?php echo $answers_box_shadow_color; ?>"/>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="col-sm-4" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('X', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_answer_box_shadow_x_offset' name='ays_quiz_answer_box_shadow_x_offset' value="<?php echo $quiz_answer_box_shadow_x_offset; ?>" />
                                </div>
                                <div class="col-sm-4 ays_divider_left" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('Y', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_answer_box_shadow_y_offset' name='ays_quiz_answer_box_shadow_y_offset' value="<?php echo $quiz_answer_box_shadow_y_offset; ?>" />
                                </div>
                                <div class="col-sm-3 ays_divider_left" style="display: inline-block;">
                                    <span class="ays_quiz_small_hint_text"><?php echo __('Z', $this->plugin_name); ?></span>
                                    <input type="number" class="ays-text-input ays-text-input-90-width" id='ays_quiz_answer_box_shadow_z_offset' name='ays_quiz_answer_box_shadow_z_offset' value="<?php echo $quiz_answer_box_shadow_z_offset; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Answers box shadow -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_ans_img_height">
                        <?php echo __('Answer image height',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Height of answers images.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_ans_img_height' name='ays_ans_img_height' value="<?php echo $ans_img_height; ?>"/>
                    </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Answers image height -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_show_answers_caption">
                        <?php echo __('Show answer caption',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show answers caption near the answer image. This option will be work only when answer has image.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <input type="checkbox" class="ays_toggle ays_toggle_slide"
                           id="ays_show_answers_caption" name="ays_show_answers_caption"
                           <?php echo ($show_answers_caption) ? 'checked' : ''; ?>/>
                    <label for="ays_show_answers_caption" class="ays_switch_toggle">Toggle</label>
                </div>
            </div> <!-- Show answers caption -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_ans_img_caption_style">
                        <?php echo __('Caption style of the image answer',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the preferred view type of captions in the image answers.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select id="ays_ans_img_caption_style" name="ays_ans_img_caption_style" class="ays-text-input ays-text-input-short">
                        <option <?php echo ($ans_img_caption_style == 'outside') ? 'selected' : ''; ?> value="outside"><?php echo __('Outside', $this->plugin_name); ?></option>
                        <option <?php echo ($ans_img_caption_style == 'inside') ? 'selected' : ''; ?> value="inside"><?php echo __('Inside', $this->plugin_name); ?></option>
                    </select>
                </div>
            </div> <!-- Answers image caption style -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_ans_img_caption_position">
                        <?php echo __('Caption position of the image answer',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the position of captions in the image answers.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select id="ays_ans_img_caption_position" name="ays_ans_img_caption_position" class="ays-text-input ays-text-input-short">
                        <option <?php echo ($ans_img_caption_position == 'top') ? 'selected' : ''; ?> value="top"><?php echo __('Top', $this->plugin_name); ?></option>
                        <option <?php echo ($ans_img_caption_position == 'bottom') ? 'selected' : ''; ?> value="bottom"><?php echo __('Bottom', $this->plugin_name); ?></option>
                    </select>
                </div>
            </div> <!-- Answers image caption position -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_ans_right_wrong_icon">
                        <?php echo __('Right/wrong answer icons',$this->plugin_name)?>
                    </label>
                    <p>
                        <span><?php echo __('Show icons in live preview',$this->plugin_name)?></span>
                        <input type="checkbox" class="ays_toggle" id="ays_ans_rw_icon_preview"/>
                        <label for="ays_ans_rw_icon_preview" style="display:inline-block;margin-left:3px;" class="ays_switch_toggle">Toggle</label>
                    </p>
                    <p>
                        <span><?php echo __('Show wrong icons in live preview',$this->plugin_name)?></span>
                        <input type="checkbox" class="ays_toggle" id="ays_wrong_icon_preview"/>
                        <label for="ays_wrong_icon_preview" style="display:inline-block;margin-left:3px;" class="ays_switch_toggle">Toggle</label>
                    </p>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <label class="ays_quiz_rw_icon">
                        <input name="ays_ans_right_wrong_icon" type="radio" value="default" <?php echo $ans_right_wrong_icon == 'default' ? 'checked' : ''; ?>>
                        <img class="right_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/correct.png">
                        <img class="wrong_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/wrong.png">
                    </label>
                    <?php
                        for($i = 1; $i <= 10; $i++):
                            $right_style_name = "correct-style-".$i;
                            $wrong_style_name = "wrong-style-".$i;
                    ?>
                    <label class="ays_quiz_rw_icon">
                        <input name="ays_ans_right_wrong_icon" type="radio" value="style-<?php echo $i; ?>" <?php echo $ans_right_wrong_icon == 'style-'.$i ? 'checked' : ''; ?>>
                        <img class="right_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/<?php echo $right_style_name; ?>.png">
                        <img class="wrong_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/<?php echo $wrong_style_name; ?>.png">
                    </label>
                    <?php
                        endfor;
                    ?>
                    <label class="ays_quiz_rw_icon">
                        <input name="ays_ans_right_wrong_icon" type="radio" value="none" <?php echo $ans_right_wrong_icon == 'none' ? 'checked' : ''; ?>>
                        <?php echo __("None", $this->plugin_name); ?>
                        <!-- <img class="right_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/correct.png">
                        <img class="wrong_icon" src="<?php echo AYS_QUIZ_PUBLIC_URL; ?>/images/wrong.png"> -->
                    </label>
                </div>
            </div> <!-- Right/wrong answers icons -->
        </div>
        <div class="col-lg-5 col-sm-12 ays_divider_left" style="position:relative;">
            <div style="position:sticky;top:90px; margin:auto;">
                <div class="ays-quiz-live-container ays-quiz-live-container-answers" style="overflow:initial;">
                    <div class="answers-with">
                        <div class="nav-answers-tab-wrapper">
                            <a href="#step1" class="nav-tab nav-tab-active">
                                <?php echo __("Without images", $this->plugin_name);?>
                            </a>
                            <a href="#step2" class="nav-tab">
                                <?php echo __("With images", $this->plugin_name);?>
                            </a>
                        </div>
                    </div>
                    <p style="position: absolute;top: 5px;">
                        <span class="ays_quiz_small_hint_text" style="color: #ccc;"><?php echo __("This species does not apply to themes Modern light and Modern dark", $this->plugin_name); ?></span>
                    </p>
                    <div id="step1" class="step active-step">
                        <div class="ays-abs-fs">
                            <div class="ays-quiz-answers ays_list_view_container">
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="1">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-74]" id="ays-answer-72-19" value="72">
                                    <label for="ays-answer-72-19">Mark Zuckerberg</label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-74]" id="ays-answer-73-19" value="73">
                                    <label for="ays-answer-73-19">Elon Musk</label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-74]" id="ays-answer-74-19" value="74">
                                    <label for="ays-answer-74-19">Bill Gates</label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-74]" id="ays-answer-75-19" value="75">
                                    <label for="ays-answer-75-19">Steve Jobs</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step2" class="step">
                        <div class="ays-abs-fs answers-image-container">
                            <div class="ays-quiz-answers ays_list_view_container">
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="1">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-124]" id="ays-answer-245-1" value="245">
                                    <label for="ays-answer-245-1" style="margin-bottom: 0; line-height: 1.5">Mark Zuckerberg</label>
                                    <label for="ays-answer-245-1" class="ays_answer_image ays_empty_before_content">
                                        <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416.jpg" alt="answer_image" class="ays-answer-image">
                                    </label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-124]" id="ays-answer-249-1" value="249">
                                    <label for="ays-answer-249-1" style="margin-bottom: 0; line-height: 1.5">Elon Musk</label>
                                    <label for="ays-answer-249-1" class="ays_answer_image ays_empty_before_content">
                                        <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-2.jpg" alt="answer_image" class="ays-answer-image">
                                    </label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-124]" id="ays-answer-248-1" value="248">
                                    <label for="ays-answer-248-1" style="margin-bottom: 0; line-height: 1.5">Bill Gates</label>
                                    <label for="ays-answer-248-1" class="ays_answer_image ays_empty_before_content">
                                        <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-1.jpg" alt="answer_image" class="ays-answer-image">
                                    </label>
                                </div>
                                <div class="ays-field ays_list_view_item">
                                    <input type="hidden" name="ays_answer_correct[]" value="0">
                                    <input class="display_none_imp" type="radio" name="ays_questions[ays-question-124]" id="ays-answer-250-1" value="249">
                                    <label for="ays-answer-250-1" style="margin-bottom: 0; line-height: 1.5">Steve Jobs</label>
                                    <label for="ays-answer-250-1" class="ays_answer_image ays_empty_before_content">
                                        <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/live-preview/416x416-3.jpg" alt="answer_image" class="ays-answer-image">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Answers Styles -->
    <hr/>
    <p class="ays-subtitle" style="margin-top:0;"><?php echo __('Buttons Styles',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row"><!-- Buttons Styles -->
        <div class="col-lg-7 col-sm-12">
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_buttons_size">
                        <?php echo __('Button size',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The default sizes of buttons.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <select class="ays-text-input ays-text-input-short" id="ays_buttons_size" name="ays_buttons_size">
                        <option value="small" <?php echo (isset($options['buttons_size']) && $options['buttons_size'] == 'small') ? 'selected' : ''; ?>>
                            <?php echo __('Small',$this->plugin_name)?>
                        </option>
                        <option value="medium" <?php echo ( (isset($options['buttons_size']) && $options['buttons_size'] == 'medium') || !isset($options['buttons_size']) ) ? 'selected' : ''; ?>>
                            <?php echo __('Medium',$this->plugin_name)?>
                        </option>
                        <option value="large" <?php echo (isset($options['buttons_size']) && $options['buttons_size'] == 'large') ? 'selected' : ''; ?>>
                            <?php echo __('Large',$this->plugin_name)?>
                        </option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_buttons_font_size'>
                        <?php echo __('Button font-size', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The font size of the buttons in pixels in the quiz. It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_buttons_font_size'>
                                <?php echo __('On PC', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for PC devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_buttons_font_size'name='ays_buttons_font_size' value="<?php echo $buttons_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-5">
                            <label for='ays_buttons_mobile_font_size'>
                                <?php echo __('On mobile', $this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Define the font size for mobile devices.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 ays_quiz_display_flex_width">
                            <div>
                                <input type="number" class="ays-text-input" id='ays_buttons_mobile_font_size'name='ays_buttons_mobile_font_size' value="<?php echo $buttons_mobile_font_size; ?>"/>
                            </div>
                            <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: end;">
                                <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Buttons font size -->
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for='ays_buttons_width'>
                        <?php echo __('Button width', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Set the button width in pixels. For an initial width, leave the field blank.', $this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id='ays_buttons_width'name='ays_buttons_width' value="<?php echo $buttons_width; ?>"/>
                        <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __('For an initial width, leave the field blank.', $this->plugin_name); ?></span>
                    </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: flex-start;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Buttons font size -->
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_buttons_padding">
                        <?php echo __('Button padding',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Padding of buttons.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left">
                    <div class="col-sm-5" style="display: inline-block; padding-left: 0;">
                        <span class="ays_quiz_small_hint_text"><?php echo __('Left / Right',$this->plugin_name)?></span>
                        <input type="number" class="ays-text-input" id='ays_buttons_left_right_padding' name='ays_buttons_left_right_padding' value="<?php echo $buttons_left_right_padding; ?>" style="width: 100px;" />
                    </div>
                    <div class="col-sm-5 ays_divider_left ays-buttons-top-bottom-padding-box" style="display: inline-block;">
                        <span class="ays_quiz_small_hint_text"><?php echo __('Top / Bottom',$this->plugin_name)?></span>
                        <input type="number" class="ays-text-input" id='ays_buttons_top_bottom_padding' name='ays_buttons_top_bottom_padding' value="<?php echo $buttons_top_bottom_padding; ?>" style="width: 100px;" />
                    </div>
                </div>
            </div> <!-- Buttons padding -->
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_buttons_border_radius">
                        <?php echo __('Button border-radius', $this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz buttons border-radius in pixels. It accepts only numeric values.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7 ays_divider_left ays_quiz_display_flex_width">
                    <div>
                        <input type="number" class="ays-text-input ays-text-input-short" id="ays_buttons_border_radius" name="ays_buttons_border_radius" value="<?php echo $buttons_border_radius; ?>"/>
                    </div>
                    <div class="ays_quiz_dropdown_max_width ays-display-flex" style="align-items: flex-start;">
                        <input type="text" value="px" class='ays-quiz-form-hint-for-size' disabled>
                    </div>
                </div>
            </div> <!-- Buttons border radius -->
            <hr/>
        </div>
        <div class="col-lg-5 col-sm-12 ays_divider_left" style="position:relative;">
            <div id="ays_buttons_styles_tab" style="position:sticky;top:50px; margin:auto;">
                <div class="ays_buttons_div" style="justify-content: center; overflow:hidden;">
                    <input type="button" name="next" class="action-button ays-quiz-live-button" style="padding:0;" value="<?php echo __( "Start", $this->plugin_name ); ?>">
                </div>
            </div>
        </div><!-- Buttons Styles Live -->
    </div><!-- Buttons Styles End -->
    <div class="form-group row">
        <div class="col-sm-3">
            <label for="ays_custom_css">
                <?php echo __('Custom CSS',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Field for entering your own CSS code',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-9">
            <textarea class="ays-textarea" id="ays_custom_css" name="ays_custom_css" cols="30" rows="10"><?php echo $ays_quiz_custom_css; ?></textarea>
        </div>
    </div> <!-- Custom CSS -->
</div>
