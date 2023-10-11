<div id="tab8" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab8') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('Integrations settings',$this->plugin_name)?></p>
    <hr/>
    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=joPQrsF0a60" target="_blank">
                <?php echo __("How to integrate MailChimp - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
            <h5><?php echo __('MailChimp Settings',$this->plugin_name)?></h5>
        </legend>
        <?php
            if(count($mailchimp) > 0):
        ?>
            <?php
                if($mailchimp_username == "" || $mailchimp_api_key == ""):
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
            <?php
                else:
            ?>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_enable_mailchimp">
                        <?php echo __('Enable MailChimp',$this->plugin_name)?>
                    </label>
                </div>
                <div class="col-sm-1">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_mailchimp"
                           name="ays_enable_mailchimp"
                           value="on"
                           <?php
                                if($mailchimp_username == "" || $mailchimp_api_key == ""){
                                    echo "disabled";
                                }else{
                                    echo $enable_mailchimp ? 'checked' : '';
                                }
                           ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_mailchimp_list">
                        <?php echo __('MailChimp list',$this->plugin_name)?>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php if(is_array($mailchimp_select)): ?>
                        <select name="ays_mailchimp_list" id="ays_mailchimp_list" class="ays-text-input ays-text-input-short"
                           <?php
                                if($mailchimp_username == "" || $mailchimp_api_key == ""){
                                    echo 'disabled';
                                }
                            ?>>
                            <option value="" disabled selected>Select list</option>
                        <?php foreach($mailchimp_select as $mlist): ?>
                            <option <?php echo ($mailchimp_list == $mlist['listId']) ? 'selected' : ''; ?>
                                value="<?php echo $mlist['listId']; ?>"><?php echo $mlist['listName']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <span><?php echo $mailchimp_select; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_enable_double_opt_in">
                        <?php echo __('Enable double opt-in',$this->plugin_name)?>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_enable_double_opt_in"
                           name="ays_enable_double_opt_in"
                           value="on"
                           <?php
                                if($mailchimp_username == "" || $mailchimp_api_key == ""){
                                    echo "disabled";
                                }else{
                                    echo ($enable_double_opt_in == 'on') ? 'checked' : '';
                                }
                           ?>/>
                    <span class="ays_option_description"><?php echo __( 'Send contacts an opt-in confirmation email when their email address added to the list.', $this->plugin_name ); ?></span>
                </div>
            </div>
            <?php
                endif;
            ?>
        <?php
            else:
        ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
            endif;
        ?>
    </fieldset> <!-- MailChimp Settings -->
    <hr/>
    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=IwT-2d9OE1g" target="_blank">
                <?php echo __("How to integrate PayPal - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/paypal_logo.png" alt="">
            <h5><?php echo __('PayPal Settings',$this->plugin_name)?></h5>
        </legend>
        <?php
            $ays_paypal_enabling = ($quiz_paypal['clientId'] == null || $quiz_paypal['clientId'] == '') ? false : true;
            if(!$ays_paypal_enabling):
        ?>
        <blockquote class="error_message">
            <?php
                echo sprintf(
                    __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                );
            ?>
        </blockquote>
        <?php
            else:
        ?>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_enable_paypal">
                    <?php echo __('Enable PayPal',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" class="ays-enable-timer1" id="ays_enable_paypal"
                       name="ays_enable_paypal"
                       value="on"
                       <?php
                            if($ays_paypal_enabling){
                                echo ($enable_paypal == 'on') ? 'checked' : '';
                            }else{
                                echo 'disabled';
                            }
                       ?>/>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_paypal_amount">
                    <?php echo __('Amount',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <input type="text"
                    class="ays-text-input ays-text-input-short"
                    id="ays_paypal_amount"
                    name="ays_paypal_amount"
                    value="<?php echo $paypal_amount; ?>"
                    <?php
                        if(!$ays_paypal_enabling){
                            echo 'disabled';
                        }
                    ?>
                />
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_paypal_currency">
                    <?php echo __('Currency',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <select name="ays_paypal_currency" id="ays_paypal_currency" class="ays-text-input ays-text-input-short"
                    <?php
                        if(!$ays_paypal_enabling){
                            echo 'disabled';
                        }
                    ?>>
                    <option <?php echo ($paypal_currency == 'USD') ? 'selected' : ''; ?> value="USD">
                        USD - <?php echo __( 'United States Dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'EUR') ? 'selected' : ''; ?> value="EUR">
                        EUR - <?php echo __( 'Euro', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'GBP') ? 'selected' : ''; ?> value="GBP">
                        GBP - <?php echo __( 'British Pound Sterling', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'AUD') ? 'selected' : ''; ?> value="AUD">
                        AUD - <?php echo __( 'Australian dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'CHF') ? 'selected' : ''; ?> value="CHF">
                        CHF - <?php echo __( 'Swiss Franc', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'JPY') ? 'selected' : ''; ?> value="JPY">
                        JPY - <?php echo __( 'Japanese Yen', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'INR') ? 'selected' : ''; ?> value="INR">
                        INR - <?php echo __( 'Indian Rupee', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'CNY') ? 'selected' : ''; ?> value="CNY">
                        CNY - <?php echo __( 'Chinese Yuan', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'CAD') ? 'selected' : ''; ?> value="CAD">
                        CAD - <?php echo __( 'Canadian Dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'AED') ? 'selected' : ''; ?> value="AED">
                        AED - <?php echo __( 'United Arab Emirates Dirham', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'RUB') ? 'selected' : ''; ?> value="RUB">
                        RUB - <?php echo __( 'Russian Ruble', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'NZD') ? 'selected' : ''; ?> value="NZD">
                        NDZ - <?php echo __( 'New Zealand dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'CZK') ? 'selected' : ''; ?> value="CZK">
                        CZK - <?php echo __( 'Czech koruna', $this->plugin_name ); ?></option>
                    <option <?php echo ($paypal_currency == 'PLN') ? 'selected' : ''; ?> value="PLN">
                        PLN - <?php echo __( 'Polish złoty', $this->plugin_name ); ?></option>
                </select>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_paypal_currency">
                    <?php echo __('Payment details',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <?php
                    $editor_id = 'ays_paypal_message';
                    $settings = array(
                        'editor_height' => $quiz_wp_editor_height,
                        'textarea_name' => 'ays_paypal_message',
                        'editor_class' => 'ays-textarea',
                        'media_elements' => false
                    );
                    wp_editor($paypal_message, $editor_id, $settings);
                ?>
            </div>
        </div>
        <?php
            endif;
        ?>
    </fieldset> <!-- PayPal Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/stripe_logo.png" alt="">
            <h5><?php echo __('Stripe Settings',$this->plugin_name)?></h5>
        </legend>
        <?php
            if(!$is_enabled_stripe):
        ?>
        <blockquote class="error_message">
            <?php
                echo sprintf(
                    __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                    "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                );
            ?>
        </blockquote>
        <?php
            else:
        ?>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_enable_stripe">
                    <?php echo __('Enable Stripe',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" class="ays-enable-timer1" id="ays_enable_stripe"
                       name="ays_enable_stripe"
                       value="on"
                       <?php
                            if($is_enabled_stripe){
                                echo ( $enable_stripe ) ? 'checked' : '';
                            }else{
                                echo 'disabled';
                            }
                       ?>/>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_stripe_amount">
                    <?php echo __('Amount',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <input type="text"
                    class="ays-text-input ays-text-input-short"
                    id="ays_stripe_amount"
                    name="ays_stripe_amount"
                    value="<?php echo $stripe_amount; ?>"
                    <?php
                        if(!$is_enabled_stripe){
                            echo 'disabled';
                        }
                    ?>
                />
                <span class="ays_option_description"><?php echo __( "Specify the amount of the payment.", $this->plugin_name ); ?></span>
                <span class="ays_option_description"><?php echo __( "This field doesn't accept an empty value or a value less than 1.", $this->plugin_name ); ?></span>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_stripe_currency">
                    <?php echo __('Currency',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <select name="ays_stripe_currency" id="ays_stripe_currency" class="ays-text-input ays-text-input-short"
                    <?php
                        if(!$is_enabled_stripe){
                            echo 'disabled';
                        }
                    ?>>
                    <option <?php echo ($stripe_currency == 'usd') ? 'selected' : ''; ?> value="usd">
                        USD - <?php echo __( 'United States Dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'eur') ? 'selected' : ''; ?> value="eur">
                        EUR - <?php echo __( 'Euro', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'gbp') ? 'selected' : ''; ?> value="gbp">
                        GBP - <?php echo __( 'British Pound Sterling', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'aud') ? 'selected' : ''; ?> value="aud">
                        AUD - <?php echo __( 'Australian dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'chf') ? 'selected' : ''; ?> value="chf">
                        CHF - <?php echo __( 'Swiss Franc', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'jpy') ? 'selected' : ''; ?> value="jpy">
                        JPY - <?php echo __( 'Japanese Yen', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'inr') ? 'selected' : ''; ?> value="inr">
                        INR - <?php echo __( 'Indian Rupee', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'cny') ? 'selected' : ''; ?> value="cny">
                        CNY - <?php echo __( 'Chinese Yuan', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'cad') ? 'selected' : ''; ?> value="cad">
                        CAD - <?php echo __( 'Canadian Dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'aed') ? 'selected' : ''; ?> value="aed">
                        AED - <?php echo __( 'United Arab Emirates Dirham', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'rub') ? 'selected' : ''; ?> value="rub">
                        RUB - <?php echo __( 'Russian Ruble', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'nzd') ? 'selected' : ''; ?> value="nzd">
                        NDZ - <?php echo __( 'New Zealand dollar', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'czk') ? 'selected' : ''; ?> value="czk">
                        CZK - <?php echo __( 'Czech koruna', $this->plugin_name ); ?></option>
                    <option <?php echo ($stripe_currency == 'pln') ? 'selected' : ''; ?> value="pln">
                        PLN - <?php echo __( 'Polish złoty', $this->plugin_name ); ?></option>
                </select>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <div class="col-sm-4">
                <label for="ays_stripe_currency">
                    <?php echo __('Payment details',$this->plugin_name)?>
                </label>
            </div>
            <div class="col-sm-8">
                <?php
                    $editor_id = 'ays_stripe_message';
                    $settings = array(
                        'editor_height' => $quiz_wp_editor_height,
                        'textarea_name' => 'ays_stripe_message',
                        'editor_class' => 'ays-textarea',
                        'media_elements' => false
                    );
                    wp_editor($stripe_message, $editor_id, $settings);
                ?>
            </div>
        </div>
        <?php
            endif;
        ?>
    </fieldset> <!-- Stripe Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/campaignmonitor_logo.png" alt="">
            <h5><?php echo __('Campaign Monitor Settings', $this->plugin_name) ?></h5>
        </legend>
        <?php
        if (count($monitor) > 0):
            ?>
            <?php
            if ($monitor_client == "" || $monitor_api_key == ""):
                ?>
                <blockquote class="error_message">
                    <?php
                        echo sprintf(
                            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                        );
                    ?>
                </blockquote>
            <?php
            else:
                ?>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_monitor">
                            <?php echo __('Enable Campaign Monitor', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_monitor"
                               name="ays_enable_monitor"
                               value="on"
                            <?php
                            if ($monitor_client == "" || $monitor_api_key == "") {
                                echo "disabled";
                            } else {
                                echo ($enable_monitor == 'on') ? 'checked' : '';
                            }
                            ?>/>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_monitor_list">
                            <?php echo __('Campaign Monitor list', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php if (is_array($monitor_select)): ?>
                            <select name="ays_monitor_list" id="ays_monitor_list" class="ays-text-input ays-text-input-short"
                                <?php
                                if ($monitor_client == "" || $monitor_api_key == "") {
                                    echo 'disabled';
                                }
                                ?>>
                                <option value="" disabled selected><?= __("Select List", $this->plugin_name) ?></option>
                                <?php foreach ( $monitor_select as $mlist ): ?>
                                    <option <?= ($monitor_list == $mlist['ListID']) ? 'selected' : ''; ?>
                                            value="<?= $mlist['ListID']; ?>"><?php echo $mlist['Name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <span><?php echo $monitor_select; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            endif;
            ?>
        <?php
        else:
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
        endif;
        ?>
    </fieldset> <!-- Campaign Monitor Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/zapier_logo.png" alt="">
            <h5><?php echo __('Zapier Integration Settings', $this->plugin_name) ?></h5>
        </legend>
        <?php
        if (count($zapier) > 0):
            ?>
            <?php
            if ($zapier_hook == ""):
                ?>
                <blockquote class="error_message">
                    <?php
                        echo sprintf(
                            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                        );
                    ?>
                </blockquote>
            <?php else: ?>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_zapier">
                            <?php echo __('Enable Zapier Integration', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_zapier"
                               name="ays_enable_zapier"
                               value="on"
                            <?php
                            if ($zapier_hook == "") {
                                echo "disabled";
                            } else {
                                echo ($enable_zapier == 'on') ? 'checked' : '';
                            }
                            ?>/>
                    </div>
                    <div class="col-sm-3">
                        <button type="button"
                                data-url="<?= $zapier_hook ?>" <?= $zapier_hook ? "" : "disabled" ?>
                                id="testZapier"
                                class="btn btn-outline-secondary">
                            <?= __("Send test data", $this->plugin_name) ?>
                        </button>
                        <a class="ays_help" data-toggle="tooltip" style="font-size: 16px;"
                           title="<?= __('We will send you a test data, and you can catch it in your ZAP for configure it.', $this->plugin_name) ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </div>
                </div>
                <div id="testZapierFields" class="d-none">
                    <input type="checkbox" name="zapierTest[]" value="ays_user_name" data-name="Name" checked/>
                    <input type="checkbox" name="zapierTest[]" value="ays_user_email" data-name="E-mail" checked/>
                    <input type="checkbox" name="zapierTest[]" value="ays_user_phone" data-name="Phone" checked/>
                    <?php
                    foreach ( $all_attributes as $attribute ) {
                        $checked = (in_array(strval($attribute['id']), $quiz_attributes)) ? 'checked' : '';
                        echo "<input type=\"checkbox\" name=\"zapierTest[]\" value=\"" . $attribute['slug'] . "\" data-name=\"".$attribute['name']."\" checked/>";
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php
        else:
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
        endif;
        ?>
    </fieldset> <!-- Zapier Integration Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/activecampaign_logo.png" alt="">
            <h5><?php echo __('ActiveCampaign Settings', $this->plugin_name) ?></h5>
        </legend>
        <?php
        if (count($active_camp) > 0):
            ?>
            <?php
            if ($active_camp_url == "" || $active_camp_api_key == ""):
                ?>
                <blockquote class="error_message">
                    <?php
                        echo sprintf(
                            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                        );
                    ?>
                </blockquote>
            <?php
            else:
                ?>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_active_camp">
                            <?php echo __('Enable ActiveCampaign', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_active_camp"
                               name="ays_enable_active_camp"
                               value="on"
                            <?php
                            if ($active_camp_url == "" || $active_camp_api_key == "") {
                                echo "disabled";
                            } else {
                                echo ($enable_active_camp == 'on') ? 'checked' : '';
                            }
                            ?>/>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_active_camp_list">
                            <?php echo __('ActiveCampaign list', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php if (is_array($active_camp_list_select)): ?>
                            <select name="ays_active_camp_list" id="ays_active_camp_list" class="ays-text-input ays-text-input-short"
                                <?php
                                if ($active_camp_url == "" || $active_camp_api_key == "") {
                                    echo 'disabled';
                                }
                                ?>>
                                <option value="" disabled
                                        selected><?= __("Select List", $this->plugin_name) ?></option>
                                <option value=""><?= __("Just create contact", $this->plugin_name) ?></option>
                                <?php foreach ( $active_camp_list_select as $list ): ?>
                                    <option <?= ($active_camp_list == $list['id']) ? 'selected' : ''; ?>
                                            value="<?= $list['id']; ?>"><?= $list['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <span><?php echo $active_camp_list_select; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_active_camp_automation">
                            <?php echo __('ActiveCampaign automation', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php if (is_array($active_camp_automation_select)): ?>
                            <select name="ays_active_camp_automation" id="ays_active_camp_automation" class="ays-text-input ays-text-input-short"
                                <?php
                                if ($active_camp_url == "" || $active_camp_api_key == "") {
                                    echo 'disabled';
                                }
                                ?>>
                                <option value="" disabled
                                        selected><?= __("Select List", $this->plugin_name) ?></option>
                                <option value=""><?= __("Just create contact", $this->plugin_name) ?></option>
                                <?php foreach ( $active_camp_automation_select as $automation ): ?>
                                    <option <?= ($active_camp_automation == $automation['id']) ? 'selected' : ''; ?>
                                            value="<?= $automation['id']; ?>"><?= $automation['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <span><?php echo $active_camp_automation_select; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            endif;
            ?>
        <?php
        else:
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
        endif;
        ?>
    </fieldset> <!-- ActiveCampaign Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/slack_logo.png" alt="">
            <h5><?php echo __('Slack Settings', $this->plugin_name) ?></h5>
        </legend>
        <?php
        if (count($slack) > 0):
            ?>
            <?php
            if ($slack_token == ""):
                ?>
                <blockquote class="error_message">
                    <?php
                        echo sprintf(
                            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                        );
                    ?>
                </blockquote>
            <?php
            else:
                ?>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_slack">
                            <?php echo __('Enable Slack integration', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_slack"
                               name="ays_enable_slack"
                               value="on"
                            <?php
                            if ($slack_token == "") {
                                echo "disabled";
                            } else {
                                echo ($enable_slack == 'on') ? 'checked' : '';
                            }
                            ?>/>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_slack_conversation">
                            <?php echo __('Slack conversation', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <?php if (is_array($slack_select)): ?>
                            <select name="ays_slack_conversation" id="ays_slack_conversation" class="ays-text-input ays-text-input-short"
                                <?php
                                if ($slack_token == "") {
                                    echo 'disabled';
                                }
                                ?>>
                                <option value="" disabled
                                        selected><?= __("Select Channel", $this->plugin_name) ?></option>
                                <?php foreach ( $slack_select as $conversation ): ?>
                                    <option <?= ($slack_conversation == $conversation['id']) ? 'selected' : ''; ?>
                                            value="<?= $conversation['id']; ?>"><?php echo $conversation['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <span><?php echo $slack_select; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            endif;
            ?>
        <?php
        else:
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
        endif;
        ?>
    </fieldset> <!-- Slack Settings -->
    <hr/>
    <fieldset>
        <legend>
            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/sheets_logo.png" alt="">
            <h5><?php echo __('Google Sheet Settings', $this->plugin_name) ?></h5>
        </legend>
        <?php
        if (count($google) > 0):
            ?>
            <?php
            if ($google_token == ""):
                ?>
                <blockquote class="error_message">
                    <?php
                        echo sprintf(
                            __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                            "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                        );
                    ?>
                </blockquote>
            <?php
            else:
                ?>
                <hr/>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="ays_enable_google">
                            <?php echo __('Enable Google integration', $this->plugin_name) ?>
                        </label>
                    </div>
                    <div class="col-sm-1">
                        <input type="checkbox" class="ays-enable-timer1" id="ays_enable_google"
                               name="ays_enable_google"
                               value="on"
                            <?php
                            if ($google_token == "") {
                                echo "disabled";
                            } else {
                                echo ($enable_google_sheets == 'on') ? 'checked' : '';
                            }
                            ?>/>
                    </div>
                </div>
                <hr>
            <?php
            endif;
            ?>
        <?php
        else:
            ?>
            <blockquote class="error_message">
                <?php
                    echo sprintf(
                        __( "For enabling this option, please go to %s page and fill all options.", $this->plugin_name ),
                        "<a style='color:blue;text-decoration:underline;font-size:20px;' href='?page=$this->plugin_name-settings&ays_quiz_tab=tab2'>". __( "this", $this->plugin_name ) ."</a>"
                    );
                ?>
            </blockquote>
        <?php
        endif;
        ?>
    </fieldset> <!-- Google Sheets -->
    <?php
        if(has_action('ays_qm_quiz_page_integrations')){
            $args = apply_filters( 'ays_qm_quiz_page_integrations_options', array(), $options );
            do_action( 'ays_qm_quiz_page_integrations', $args);
        }
    ?>
</div>
