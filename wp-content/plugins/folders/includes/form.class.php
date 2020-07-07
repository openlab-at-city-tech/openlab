<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WCP_Forms {
    public function __construct() {

    }

    public static function get_form_html($option_data = "") {
        ob_start();
        ?>

        <div class="wcp-custom-form">
            <div class="form-title">
                <?php esc_html_e("Folders", WCP_FOLDER ) ?>
                <a href="javascript:;" class="add-new-folder" id="add-new-folder"><span class="folder-icon-create_new_folder"></span> <span><?php esc_html_e("New Folder", WCP_FOLDER ) ?></span></a>
                <span class="folder-loader-ajax">
                    <img class="active" src="<?php echo esc_url(admin_url('/images/spinner.gif')); ?>" alt="">
                    <svg id="successAnimation" fill="#F51366" class="animated" xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 70 70">
                        <path id="successAnimationResult" fill="#D8D8D8" d="M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z"></path>
                        <circle id="successAnimationCircle" cx="35" cy="35" r="24" stroke="#979797" stroke-width="2" stroke-linecap="round" fill="transparent"></circle>
                        <polyline id="successAnimationCheck" stroke="#979797" stroke-width="2" points="23 34 34 43 47 27" fill="transparent"></polyline>
                    </svg>
                </span>
                <div class="clear"></div>
            </div>
            <div class="form-options">
                <ul>
                    <li>
                        <a href="javascript:;" id="inline-update"><span class="icon folder-icon-border_color"><span class="path1"></span><span class="path2"></span></span> <span class="text"><?php esc_html_e("Rename", WCP_FOLDER ) ?></span> </a>
                    </li>
                    <li>
                        <a href="javascript:;" id="inline-remove"><span class="icon folder-icon-delete"></span> <span class="text"><?php esc_html_e("Delete", WCP_FOLDER ) ?></span> </a>
                    </li>
                    <li>
                        <a href="javascript:;" id="expand-collapse-list"><span class="icon folder-icon-expand_more"></span></a>
                    </li>
                    <li class="last folder-order">
                        <a data-tooltip="Sort Folders" href="javascript:;" id="sort-order-list" class="sort-folder-order">
                            <svg xmlns="http://www.w3.org/2000/svg" width="506.667" height="506.667" viewBox="0 0 380 380" preserveAspectRatio="xMidYMid meet"><path d="M91.5 1.9C86.6 5 3.6 88.8 2.2 92c-1.5 3.8-.7 7.1 2.5 10.2l2.5 2.3 64.8.6v131.8l1.1 134.9c2 5.7 3.9 6.2 23.2 6.2 16.2 0 17.5-.1 19.6-2.1 1.2-1.1 2.6-3.2 3.2-4.6.5-1.5.9-54.6.9-134.3V105.1l64.8-.6 2.5-2.3c3.2-3.1 4-6.4 2.5-10.2-1.5-3.4-85.9-88.5-90-90.6-3.5-1.8-4.7-1.8-8.3.5zm173.2 1.7c-1.5 1.5-2.7 3.4-2.8 4.3-.6 11.8-.7 41.3-.8 144.3L261 274h-31.1c-35.5 0-37 .3-39 7-.9 2.9-.8 4.3.3 6.4 1.9 3.7 86.4 88.4 90.1 90.2 1.8 1 4 1.3 5.6.9 4.1-1 90.9-88 92.1-92.3 1-3.9.1-6.9-3-9.7-2.1-1.9-3.8-2-34.6-2.3l-32.4-.3-.2-133.4-.3-133.4-4.2-5.6-18.5-.3-18.5-.2-2.6 2.6z"/></svg>
                        </a>
                        <div class="folder-sort-menu">
                            <ul>
                                <li><a data-sort="a-z" href="#">A → Z</a></li>
                                <li><a data-sort="z-a" href="#">Z → A</a></li>
                                <li><a data-sort="n-o" href="#">Newest → Oldest</a></li>
                                <li><a data-sort="o-n" href="#">Oldest → Newest</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="last folder-checkbox">
                        <input type="checkbox" id="folder-hide-show-checkbox">
                    </li>
                </ul>
                <div class="upgrade-message">
                    <?php
                    $tlfs = get_option("folder_old_plugin_folder_status");
                    if($tlfs == false || $tlfs < 10) {
                        $tlfs = 10;
                    }
                    $total = WCP_Folders::get_ttl_fldrs();
                    if($total > $tlfs) {
                        $tlfs = $total;
                    }
                    ?>
                    <span class="upgrade-message">You have used <span class='pink' id='current-folder'><?php echo esc_attr($total) ?></span>/<span id='ttl-fldr'><?php echo esc_attr($tlfs) ?></span> Folders. <a class="pink" href="<?php echo esc_url(admin_url("admin.php?page=wcp_folders_upgrade")) ?>"><?php esc_html_e("Upgrade", WCP_FOLDER) ?></a></span>
                </div>
            </div>
            <div class="form-loader">
                <div class="form-loader-count"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}