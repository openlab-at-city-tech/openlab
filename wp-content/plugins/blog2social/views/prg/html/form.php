<!--Form-->
<div class="col-md-6 del-padding-left">
    <div class="panel panel-group ">
        <div class="panel-body panel-min-height">
            <h4>
                <span class="label label-primary">1</span>
                <?php esc_html_e('Post a Press Release', 'blog2social') ?>  
            </h4>

            <div class="form-group">
                <label class="col-md-6 del-padding-left hidden-sm hidden-xs"><small>  <?php esc_html_e('Category', 'blog2social') ?>  </small></label>
                <label class="col-md-6 del-padding-left hidden-sm hidden-xs"><small> <?php esc_html_e('Language', 'blog2social') ?></small></label>
                <div class="col-md-6 del-padding-left">
                    <select name="kategorie_id" id="prg_cat" class="form-control b2s-select">
                        <?php echo wp_kses($item->getCategoryHtml(), array(
                            'option' => array('value' => array())
                        )); ?>
                    </select>
                </div>
                <div class="col-md-6 del-padding-left">
                    <select name="sprache" id="sprache" class="form-control b2s-select">
                        <option value="de"><?php esc_html_e('German', 'blog2social') ?></option>
                        <option value="en"><?php esc_html_e('English', 'blog2social') ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Title', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">
                    <input id="prg_title" name="title" maxlength="150" placeholder="<?php esc_attr_e('Title', 'blog2social') ?>" class="form-control" type="text" value="<?php echo esc_attr($title); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Subtitle', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">
                    <input id="prg_subline" name="subline" placeholder="<?php esc_attr_e('Subtitle', 'blog2social') ?>" class="form-control" type="text" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('YouTube-Link', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">
                    <input id="prg_videolink" name="video_link" placeholder="<?php esc_attr_e('YouTube-Link', 'blog2social') ?>" class="form-control" type="text" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Message', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">                     
                    <textarea id="prg_message" name="message" rows="10" data-provide="markdown" class="form-control" placeholder="<?php esc_attr_e('Message', 'blog2social') ?>"><?php echo esc_html($message); ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Keywords', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">                     
                    <input id="prg_keywords" name="keywords" maxlength="200" placeholder="<?php esc_attr_e('Keywords with commas (e.g .: Blog2Social, PR-Gateway)', 'blog2social') ?>" class="form-control" type="text" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Shortext', 'blog2social') ?></small></label>
                <div class="col-md-12 del-padding-left">                     
                    <textarea id="prg_shorttext" name="shorttext" rows="4" class="form-control" placeholder="<?php esc_attr_e('Shortext', 'blog2social') ?>"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6 del-padding-left">
    <div class="panel panel-group ">
        <div class="panel-body panel-min-height">
            <h4>
                <span class="label label-primary">2</span>
                <?php esc_html_e('Contact Details', 'blog2social') ?>
            </h4>
            <div class="col-md-12 del-padding-left">
                <ul id="formContact" class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#mandant"><?php esc_html_e('Company', 'blog2social') ?></a>
                    </li>
                    <li class="">
                        <a data-toggle="tab" href="#presse"><?php esc_html_e('Press', 'blog2social') ?></a>
                    </li>
                </ul>
            </div>
            <div id="myTabContent" class="tab-content">
                <div id="mandant" class="tab-pane active">
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Name', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_name_mandant" name="name_mandant" placeholder="<?php esc_attr_e('Name', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 del-padding-left"></label>
                        <label class="col-md-4 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('First Name', 'blog2social') ?></small></label>
                        <label class="col-md-4 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Last Name', 'blog2social') ?></small></label>
                        <div class="col-md-4 del-padding-left">
                            <select name="anrede_mandant" id="prg_anrede_mandant" class="form-control b2s-select">
                                <option value="0"><?php esc_html_e('Mrs.', 'blog2social') ?></option>
                                <option value="1"><?php esc_html_e('Mr.', 'blog2social') ?></option>
                            </select>
                        </div>
                        <div class="col-md-4 del-padding-left">
                            <input id="prg_vorname_mandant" name="vorname_mandant" placeholder="<?php esc_attr_e('First Name', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                        <div class="col-md-4 del-padding-left">
                            <input id="prg_nachname_mandant" name="nachname_mandant" placeholder="<?php esc_attr_e('Last Name', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-9 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Street', 'blog2social') ?></small></label>
                        <label class="col-md-3 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Number', 'blog2social') ?></small></label>
                        <div class="col-md-9 del-padding-left">
                            <input id="prg_strasse_mandant" name="strasse_mandant" placeholder="<?php esc_attr_e('Street', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                        <div class="col-md-3 del-padding-left">
                            <input id="prg_nummer_mandant" maxlength="10" name="nummer_mandant" placeholder="<?php esc_attr_e('Number', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Zip Code', 'blog2social') ?></small></label>
                        <label class="col-md-9 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('City', 'blog2social') ?></small></label>
                        <div class="col-md-3 del-padding-left">
                            <input id="prg_plz_mandant" name="plz_mandant" maxlength="10" placeholder="<?php esc_attr_e('Zip Code', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                        <div class="col-md-9 del-padding-left">
                            <input id="prg_ort_mandant" name="ort_mandant" placeholder="<?php esc_attr_e('City', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Country', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <select name="land_mandant" id="prg_land_mandant" class="form-control b2s-select">
                                <?php echo wp_kses($item->getCountryHtml(), array(
                                    'option' => array(
                                        'value' => array(),
                                        'selected' => array()
                                    )
                                )); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Phone', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_telefon_mandant" name="telefon_mandant" placeholder="<?php esc_attr_e('Phone', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('E-Mail', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_email_mandant" name="email_mandant" placeholder="<?php esc_attr_e('E-Mail', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Website', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_url_mandant" name="url_mandant" placeholder="<?php esc_attr_e('Website', 'blog2social') ?>" class="form-control" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Company Description', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <textarea id="prg_info_mandant" name="info_mandant" rows="6" class="form-control" placeholder="<?php esc_attr_e('Company Description', 'blog2social') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div id="presse" class="tab-pane">
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Name', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_name_presse" type="text" name="name_presse" value="<?php echo esc_attr(isset($userData->name_presse) ? $userData->name_presse : ''); ?>" placeholder="<?php esc_attr_e('Name', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 del-padding-left"></label>
                        <label class="col-md-4 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('First Name', 'blog2social') ?></small></label>
                        <label class="col-md-4 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Last Name', 'blog2social') ?></small></label>
                        <div class="col-md-4 del-padding-left">
                            <select name="anrede_presse" id="prg_anrede_presse" class="form-control b2s-select">
                                <option value="0" <?php echo (isset($userData->anrede_presse) && $userData->anrede_presse == 0) ? 'selected="selected"' : ''; ?>><?php esc_html_e('Mrs.', 'blog2social') ?></option>
                                <option value="1" <?php echo (isset($userData->anrede_presse) && $userData->anrede_presse == 1) ? 'selected="selected"' : ''; ?>><?php esc_html_e('Mr.', 'blog2social') ?></option>
                            </select>
                        </div>
                        <div class="col-md-4 del-padding-left">
                            <input id="prg_vorname_presse" type="text" name="vorname_presse" value="<?php echo esc_attr(isset($userData->vorname_presse) ? $userData->vorname_presse : ''); ?>" placeholder="<?php esc_attr_e('First Name', 'blog2social') ?>" class="form-control">
                        </div>
                        <div class="col-md-4 del-padding-left">
                            <input id="prg_nachname_presse" type="text" name="nachname_presse" value="<?php echo esc_attr(isset($userData->nachname_presse) ? $userData->nachname_presse : ''); ?>" placeholder="<?php esc_attr_e('Last Name', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-9 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Street', 'blog2social') ?></small></label>
                        <label class="col-md-3 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Number', 'blog2social') ?></small></label>
                        <div class="col-md-9 del-padding-left">
                            <input id="prg_strasse_presse" type="text" name="strasse_presse" value="<?php echo esc_attr(isset($userData->strasse_presse) ? $userData->strasse_presse : ''); ?>" placeholder="<?php esc_attr_e('Street', 'blog2social') ?>" class="form-control">
                        </div>
                        <div class="col-md-3 del-padding-left">
                            <input id="prg_nummer_presse" type="text" maxlength="10" name="nummer_presse" value="<?php echo esc_attr(isset($userData->nummer_presse) ? $userData->nummer_presse : ''); ?>" placeholder="<?php esc_attr_e('Number', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('Zip Code', 'blog2social') ?></small></label>
                        <label class="col-md-9 del-padding-left hidden-sm hidden-xs"><small><?php esc_html_e('City', 'blog2social') ?></small></label>
                        <div class="col-md-3 del-padding-left">
                            <input id="prg_plz_presse" type="text" maxlength="10" name="plz_presse" value="<?php echo esc_attr(isset($userData->plz_presse) ? $userData->plz_presse : ''); ?>" placeholder="<?php esc_attr_e('Zip Code', 'blog2social') ?>" class="form-control">
                        </div>
                        <div class="col-md-9 del-padding-left">
                            <input id="prg_ort_presse" type="text" name="ort_presse" value="<?php echo esc_attr(isset($userData->ort_presse) ? $userData->ort_presse : ''); ?>" placeholder="<?php esc_attr_e('City', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Country', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <select name="land_presse" id="prg_land_presse" class="form-control b2s-select">
                                <?php echo wp_kses($item->getCountryHtml(), array(
                                    'option' => array(
                                        'value' => array(),
                                        'selected' => array()
                                    )
                                )); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Phone', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_telefon_presse" type="text" name="telefon_presse" value="<?php echo esc_attr(isset($userData->telefon_presse) ? $userData->telefon_presse : ''); ?>" placeholder="<?php esc_attr_e('Phone', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('E-Mail', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_email_presse" type="text" name="email_presse" value="<?php echo esc_attr(isset($userData->email_presse) ? $userData->email_presse : ''); ?>" placeholder="<?php esc_attr_e('E-Mail', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 del-padding-left"><small><?php esc_html_e('Website', 'blog2social') ?></small></label>
                        <div class="col-md-12 del-padding-left">
                            <input id="prg_url_presse" type="text" name="url_presse" value="<?php echo esc_url(isset($userData->url_presse) ? $userData->url_presse : ''); ?>" placeholder="<?php esc_attr_e('Website', 'blog2social') ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Form-->