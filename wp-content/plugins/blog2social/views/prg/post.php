<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/PRG/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/PRG/Post/Item.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
/* Sort */
$currentPage = isset($_GET['b2sPage']) ? (int) $_GET['b2sPage'] : 1;
$prgSortPostType = isset($_GET['prgSortPostType']) ? trim(sanitize_text_field($_GET['prgSortPostType'])) : "";
$prgSortPostStatus = isset($_GET['prgSortPostStatus']) ? (in_array(trim($_GET['prgSortPostStatus']), array('publish', 'future', 'pending')) ? trim(sanitize_text_field(wp_unslash($_GET['prgSortPostStatus']))) : "") : "";
$prgSortPostTitle = isset($_GET['prgSortPostTitle']) ? trim(sanitize_text_field($_GET['prgSortPostTitle'])) : "";
$prgSortPostAuthor = isset($_GET['prgSortPostAuthor']) ? (int) $_GET['prgSortPostAuthor'] : 0;
$prgUserLang = strtolower(substr(get_locale(), 0, 2));
?>

<div class="b2s-container">
    <div class="b2s-inbox">
<?php require_once (B2S_PLUGIN_DIR . 'views/prg/html/header.php'); ?>
        <div class="col-md-12 del-padding-left">
            <div class="col-md-12 del-padding-left">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Filter Start-->
                        <div class="b2s-post">
                            <div class="grid-body">
                                <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i> <?php esc_html_e('filter', 'blog2social') ?></a></div>
                                <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i> <?php esc_html_e('filter', 'blog2social') ?></a></div>

                                <!-- Filter Post Start-->
                                <form class="form-inline pull-left" action="#" method="GET">
                                    <input id="page" type="hidden" value="prg-post" name="page">
                                    <?php
                                    $postFilter = new PRG_Post_Filter('all', $prgSortPostTitle, $prgSortPostAuthor, $prgSortPostType, $prgSortPostStatus);
                                    echo wp_kses($postFilter->getItemHtml(), array(
                                        'div' => array(
                                            'class' => array()
                                        ),
                                        'input' => array(
                                            'id' => array(),
                                            'name' => array(),
                                            'class' => array(),
                                            'value' => array(),
                                            'maxlength' => array(),
                                            'placeholder' => array(),
                                            'type' => array(),
                                        ),
                                        'select' => array(
                                            'name' => array(),
                                            'class' => array(),
                                            'id' => array(),
                                        ),
                                        'option' => array(
                                            'value' => array(),
                                            'selected' => array(),
                                        ),
                                        'button' => array(
                                            'type' => array(),
                                            'class' => array(),
                                        ),
                                        'a' => array(
                                            'href' => array(),
                                            'class' => array(),
                                        ),
                                    ));
                                    ?>
                                </form>
                                <!-- Filter Post Ende-->
                                <br>
                            </div>       
                        </div>
                        <div class="clearfix"></div>
                        <!--Filter End-->
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <?php
                                    $postItem = new PRG_Post_Item('all', $prgSortPostTitle, $prgSortPostAuthor, $prgSortPostType, $prgSortPostStatus, $currentPage, $prgUserLang);
                                    echo wp_kses($postItem->getItemHtml(), array(
                                        'li' => array(
                                            'class' => array(),
                                        ),
                                        'div' => array(
                                            'class' => array(),
                                        ),
                                        'span' => array(
                                            'class' => array(),
                                        ),
                                        'p' => array(
                                            'class' => array(),
                                        ),
                                        'img' => array(
                                            'class' => array(),
                                            'src' => array(),
                                            'alt' => array(),
                                        ),
                                        'strong' => array(),
                                        'a' => array(
                                            'target' => array(),
                                            'href' => array(),
                                            'class' => array(),
                                        ),
                                    ));
                                    ?>
                                </ul>
                                <br>
                                <nav class="text-center">
                                    <?php
                                    echo wp_kses($postItem->getPaginationHtml(), array(
                                        'ul' => array(
                                            'class' => array()
                                        ),
                                        'li' => array(
                                            'class' => array()
                                        ),
                                        'a' => array(
                                            'href' => array()
                                        ),
                                        'span' => array(),
                                    ));
                                    ?>
                                </nav>
<?php require_once (B2S_PLUGIN_DIR . 'views/prg/html/footer.php'); ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>