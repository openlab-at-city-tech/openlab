<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
global $typenow;
$post_type = $typenow;
if(isset($folder_post_type) && !empty($folder_post_type)){
    $post_type = sanitize_text_field($folder_post_type);
} else {
    $post_type = $typenow;
}
if(isset($_GET['post_type']) && !empty($_GET['post_type'])){
    $post_type = sanitize_text_field($_GET['post_type']);
}
$folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
$all_item_status = !isset($_GET[$folder_type]) || empty($_GET[$folder_type]) ? true : false;
$empty_item_status = isset($_GET[$folder_type]) && $_GET[$folder_type] == -1 ? true : false;
$has_horizontal_scroll = \Folders\Admin\Settings::get_field_settings( 'customization_settings', 'enable_horizontal_scroll' );
$status = get_option('wcp_dynamic_display_status_'. $post_type);

$title = ucfirst($post_type);
if ($post_type == "page") {
    $title = "Pages";
} else if ($post_type == "post") {
    $title = "Posts";
} else if ($post_type == "attachment") {
    $title = "Files";
} else {
    $postType = $typenow;
    $postTypes = get_post_types(["name" => $postType], 'objects');
    if (!empty($postTypes) && is_array($postTypes) && isset($postTypes[$postType]) && isset($postTypes[$postType]->label)) {
        $title = $postTypes[$postType]->label;
    }
}
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
<div class="folders-sidebar-wrap <?php echo esc_attr($status == 'hide' ? 'folders-hidden' : '') ?>" id="folders-sidebar-wrap">
    <div class="relative">
        <button type="button" id="folder-toggle-button" class="folder-toggle-button <?php echo esc_attr($status == 'hide' ? 'active' : '') ?>">
            <svg class="folder-color" width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M1.04004 1.03906L6.04004 6.03906L11.04 1.03906" stroke="currentColor" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"></path> </svg>
        </button>
    </div>
    <div class="folders-sidebar folders-wrap" id="folders-sidebar">
        <div class="folders-flex flex-col gap-3" id="folders-wrap-header">
            <div class="folders-flex items-center justify-between gap-2 flex-wrap">
                <div class="folder-title"><?php esc_html_e('Folders', 'folders'); ?></div>
                <div class="folders-flex items-center gap-2 flex-wrap"">
                    <div>
                        <button type="button" class="add-folder folders-flex items-center gap-2" id="add-folder">
                            <span class="pfolder-add-folder"></span>
                            <?php esc_html_e('New Folder', 'folders'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="folders-flex items-center gap-2 relative flex-wrap"">
                <div class="relative">
                    <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folder-sidebar-button expand-collapse custom-html-tooltip" id="expand-collapse">
                        <span class="pfolder-arrow-down"></span>
                        <span class="expand"><?php esc_html_e('Expand', 'folders'); ?></span>
                        
                        <div class="custom-tooltip-content w-30">
                            <?php printf( esc_html__( '%s to expand/collapse folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                        </div>
                    </a>
                </div>
                <div class="relative sort-folders-wrap" id="sort-folders-wrap">
                    <button type="button" class="folder-sidebar-button sort-folders" id="sort-folders">
                        <span class="pfolder-arrow-sort"></span>
                        <?php esc_html_e('Sort', 'folders'); ?>
                    </button>
                    <div class="folder-sort-options">
                        <ul>
                            <li><a href="#" data-sort="a-z"><?php esc_html_e('A → Z', 'folders'); ?></a></li>
                            <li><a href="#" data-sort="z-a"><?php esc_html_e('Z → A', 'folders'); ?></a></li>
                            <li><a class="upgrade-link" href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" data-sort="n-o">
                                    <?php esc_html_e('Sort by newest', 'folders'); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="#FFB743"></path>
                                        <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="#FFB743"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a class="upgrade-link" href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" data-sort="o-n">
                                    <?php esc_html_e('Sort by oldest', 'folders'); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="#FFB743"></path>
                                        <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="#FFB743"></path>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="folder-sidebar-button folders-search-button" id="folders-search-button">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.833 15.834L12.208 12.209M14.1663 7.50065C14.1663 11.1825 11.1816 14.1673 7.49967 14.1673C3.81778 14.1673 0.833008 11.1825 0.833008 7.50065C0.833008 3.81875 3.81778 0.833984 7.49967 0.833984C11.1816 0.833984 14.1663 3.81875 14.1663 7.50065Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="sr-only"><?php esc_html_e('Search folders', 'folders'); ?></span>
                </button>
                <?php do_action('folders_header_section'); ?>
            </div>
            <div class="folders-search-wrap">
                <div class="folders-search folders-flex items-center">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.833 15.834L12.208 12.209M14.1663 7.50065C14.1663 11.1825 11.1816 14.1673 7.49967 14.1673C3.81778 14.1673 0.833008 11.1825 0.833008 7.50065C0.833008 3.81875 3.81778 0.833984 7.49967 0.833984C11.1816 0.833984 14.1663 3.81875 14.1663 7.50065Z" stroke="#717680" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <label class="sr-only" for="folder-search"><?php esc_attr_e('Search folders...', 'folders'); ?></label>
                    <input type="text" maxlength="50" id="folder-search" placeholder="<?php esc_attr_e('Search folders...', 'folders'); ?>">
                </div>
            </div>
            <div class="folder-separator"></div>
            <div class="folders-flex gap-2-5 flex-col">
                <div class="folders-flex flex-col gap-px">
                    <button type="button" class="folder-item all-folder-items <?php echo esc_attr($all_item_status ? 'active' : ''); ?>">
                        <span class="folder-name"><?php echo esc_html__("All ", 'folders') . esc_attr($title); ?></span>
                        <span class="folder-count total-items-count"></span>
                    </button>
                    <button type="button" class="folder-item empty-folder-items <?php echo esc_attr($empty_item_status ? 'active' : ''); ?>">
                        <span class="folder-name"><?php echo esc_html__("Unassigned ", 'folders') . esc_attr($title); ?></span>
                        <span class="folder-count total-empty-items-count"></span>
                    </button>
                </div>
                <div class="folder-separator"></div>
                <div class="sticky-folders-wrap folders-flex flex-col gap-px">
                    <div class="folder-item folder-item-title">
                        <span class="folder-name"><?php esc_html_e('Sticky Folders', 'folders'); ?></span>
                        <div class="folder-actions pr-0">
                            <span class="pfolder-pin-filled"></span>
                        </div>
                    </div>
                    <div class="folders-flex flex-col gap-px" id="sticky-folders-wrap">

                    </div>
                </div>
                <div class="folder-separator sticky-folders-wrap"></div>
                <div class="folders-flex flex-wrap folders-menu-actions gap-2 items-center">
                    <div class="folders-menu-action">
                        <input type="checkbox" class="folder-main-checkbox" />
                    </div>
                    <div class="upload-folder-wrap">
                        <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folders-menu-action custom-html-tooltip" >
                            <span class="pfolder-upload font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Upload folder', 'folders'); ?></span>

                            <div class="custom-tooltip-content w-30">
                                <?php printf( esc_html__( '%s to upload folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folders-menu-action custom-html-tooltip" >
                            <span class="pfolder-cut font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Cut', 'folders'); ?></span>

                            <div class="custom-tooltip-content w-30">
                                <?php printf( esc_html__( '%s to cut folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folders-menu-action custom-html-tooltip" >
                            <span class="pfolder-copy font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Copy', 'folders'); ?></span>

                            <div class="custom-tooltip-content w-30">
                                <?php printf( esc_html__( '%s to copy folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folders-menu-action custom-html-tooltip" >
                            <span class="pfolder-paste font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Paste', 'folders'); ?></span>

                            <div class="custom-tooltip-content w-30">
                                <?php printf( esc_html__( '%s to paste folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                            </div>
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="folders-menu-action custom-html-tooltip" >
                            <span class="pfolder-lock font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Lock', 'folders'); ?></span>

                            <div class="custom-tooltip-content w-30">
                                <?php printf( esc_html__( '%s to lock/unlock folders', 'folders' ), '<u>' . esc_html__( 'Upgrade now', 'folders' ) . '</u>' ); ?>
                            </div>
                        </a>
                    </div>
                    <div>
                        <button type="button" disabled id="delete-folder-action" class="folders-menu-action folders-tooltip delete-button" data-tooltip="<?php esc_html_e('Delete', 'folders'); ?>">
                            <span class="pfolder-trash font-14"></span>
                            <span class="sr-only"><?php esc_html_e('Delete', 'folders'); ?></span>
                        </button>
                    </div>
                </div>
                <div class="folder-separator" id="folders-sidebar-position"></div>
                <div class="folders-main-wrap folders-list-sidebar <?php echo esc_attr($has_horizontal_scroll ? 'hor-scroll' : '') ?>" id="folders-list-sidebar">
                    <div class="horizontal-scroll-menu">
                        <div class="folders-flex gap-2-5 flex-col">
                            <?php do_action('folder_dynamic_tree'); ?>
                            <div id="folders-folderstree" class="folders-folderstree hidden"></div>
                            <div class="tree-animation">
                                <div class="tree-loader"></div>
                            </div>
                            <div id="folders-folderstree-result" class="hidden"><?php esc_html_e('No results found related to your search', 'folders'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php do_action( 'folders_sidebar_modal' ); ?>
    <div class="hidden" id="sticky-folder-button">
        <div class="relative sticky-folder-item-wrap" id="folder-item-__id__" data-id="__id__">
            <button type="button" class="folder-item sticky-folder-items folder-item-__id__" data-id="__id__">
                <span class="folder-name folder-name___id__"></span>
                <span class="folder-actions">
                    <span class="folders-inline-edit">
                        <svg class="folders-edit-icon" width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg"> <path class="avoid-folder-click" d="M6.75 12.1642H12.75Z" fill="currentColor"></path> <path class="avoid-folder-click" d="M9.75 1.16421C10.0152 0.898997 10.3749 0.75 10.75 0.75C10.9357 0.75 11.1196 0.78658 11.2912 0.857651C11.4628 0.928721 11.6187 1.03289 11.75 1.16421C11.8813 1.29554 11.9855 1.45144 12.0566 1.62302C12.1276 1.7946 12.1642 1.9785 12.1642 2.16421C12.1642 2.34993 12.1276 2.53383 12.0566 2.70541C11.9855 2.87699 11.8813 3.03289 11.75 3.16421L3.41667 11.4975L0.75 12.1642L1.41667 9.49755L9.75 1.16421Z" fill="currentColor"></path> <path class="avoid-folder-click" d="M6.75 12.1642H12.75M9.75 1.16421C10.0152 0.898997 10.3749 0.75 10.75 0.75C10.9357 0.75 11.1196 0.78658 11.2912 0.857651C11.4628 0.928721 11.6187 1.03289 11.75 1.16421C11.8813 1.29554 11.9855 1.45144 12.0566 1.62302C12.1276 1.7946 12.1642 1.9785 12.1642 2.16421C12.1642 2.34993 12.1276 2.53383 12.0566 2.70541C11.9855 2.87699 11.8813 3.03289 11.75 3.16421L3.41667 11.4975L0.75 12.1642L1.41667 9.49755L9.75 1.16421Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </svg>
                    </span>
                    <span class="folders-star-icon">
                        <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.66667 0L8.72667 4.17333L13.3333 4.84667L10 8.09333L10.7867 12.68L6.66667 10.5133L2.54667 12.68L3.33333 8.09333L0 4.84667L4.60667 4.17333L6.66667 0Z" fill="currentColor"></path> </svg></span><span class="folders-sticky-icon"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M11.4557 3.87011L9.4792 1.8917C8.12807 0.539269 7.45253 -0.136952 6.72693 0.0231218C6.0014 0.183202 5.6724 1.08104 5.01447 2.87671L4.56915 4.09207C4.39375 4.57078 4.30605 4.81013 4.14826 4.99528C4.07746 5.07836 3.99691 5.15258 3.90834 5.21634C3.71096 5.35847 3.46541 5.42614 2.9743 5.56154C1.86738 5.86667 1.31392 6.01927 1.10536 6.3814C1.0152 6.53793 0.968278 6.71567 0.969418 6.8964C0.972058 7.31433 1.37799 7.72067 2.18984 8.53333L3.1329 9.47753L0.148965 12.4643C-0.049655 12.6631 -0.049655 12.9855 0.148965 13.1843C0.347585 13.3831 0.669618 13.3831 0.868238 13.1843L3.85227 10.1974L4.82961 11.1757C5.6466 11.9935 6.05513 12.4024 6.4756 12.403C6.6528 12.4033 6.82693 12.3572 6.98087 12.2694C7.34613 12.0609 7.49953 11.5034 7.80633 10.3883C7.94127 9.89814 8.00867 9.653 8.15026 9.45567C8.21227 9.36934 8.28413 9.29053 8.36453 9.22093C8.54807 9.062 8.78587 8.97267 9.26147 8.794L10.4908 8.33207C12.2667 7.66487 13.1546 7.33127 13.3111 6.60767C13.4676 5.884 12.7969 5.2127 11.4557 3.87011Z" fill="currentColor"></path></svg>
                    </span>
                    <span class="folders-default-icon">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00004 0C10.6328 2.29099e-06 12.6509 1.50287 13.9639 2.90332C14.6271 3.61069 15.1332 4.3157 15.4737 4.84277C15.6443 5.1069 15.7747 5.32881 15.8633 5.48633C15.9077 5.56514 15.9424 5.62843 15.9659 5.67285C15.9775 5.69492 15.9867 5.71299 15.9932 5.72559C15.9963 5.73168 15.9991 5.73652 16.001 5.74023C16.002 5.7421 16.0023 5.74387 16.003 5.74512L16.004 5.74707L15.334 6.08301L16.004 6.41895V6.41992L16.003 6.42188C16.0024 6.42308 16.0019 6.425 16.001 6.42676C15.9991 6.4305 15.9963 6.43531 15.9932 6.44141C15.9867 6.45405 15.9776 6.47195 15.9659 6.49414C15.9424 6.53856 15.9077 6.60185 15.8633 6.68066C15.7747 6.83816 15.6442 7.05925 15.4737 7.32324C15.1332 7.85036 14.6273 8.55513 13.9639 9.2627C12.6509 10.6632 10.633 12.167 8.00004 12.167C5.36724 12.1669 3.34914 10.6632 2.03618 9.2627C1.37298 8.55524 0.866791 7.85028 0.526411 7.32324C0.355912 7.05923 0.225336 6.83812 0.136762 6.68066C0.0924601 6.6019 0.0587038 6.53856 0.0352 6.49414C0.0234461 6.47193 0.01436 6.45406 0.00785621 6.44141C0.00472778 6.43532 0.00194667 6.43051 4.37126e-05 6.42676C-0.000812791 6.42507 -0.00131205 6.42306 -0.00190941 6.42188L-0.00288597 6.41992L-0.00386254 6.41895C-0.10941 6.20784 -0.109369 5.95916 -0.00386254 5.74805V5.74707H-0.00288597L-0.00190941 5.74512C-0.00129104 5.74388 -0.000875585 5.74205 4.37126e-05 5.74023C0.00193576 5.7365 0.00471887 5.73169 0.00785621 5.72559C0.0143442 5.71297 0.02349 5.69498 0.0352 5.67285C0.0587043 5.62843 0.0924367 5.56513 0.136762 5.48633C0.225374 5.3288 0.355733 5.10705 0.526411 4.84277C0.866843 4.31567 1.3729 3.61082 2.03618 2.90332C3.34914 1.50284 5.36726 0.000123947 8.00004 0ZM8.00004 3.77734C6.72606 3.77734 5.6934 4.81 5.6934 6.08398C5.6934 7.35797 6.72606 8.39062 8.00004 8.39062C9.27402 8.39062 10.3067 7.35797 10.3067 6.08398C10.3067 4.81 9.27403 3.77734 8.00004 3.77734ZM16.0049 5.74805C16.1104 5.95913 16.1105 6.20789 16.0049 6.41895L15.334 6.08301L16.0049 5.74805Z" fill="currentColor"></path></svg>
                    </span>
                    <span class="folders-lock-icon">
                        <svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M2 5.014V4.084C2 3.006 2.417 1.97 3.165 1.203C3.53326 0.823305 3.97385 0.521233 4.46076 0.314615C4.94768 0.107997 5.47106 0.00102024 6 0C6.52894 0.00102024 7.05232 0.107997 7.53924 0.314615C8.02615 0.521233 8.46674 0.823305 8.835 1.203C9.58364 1.97461 10.0016 3.0079 10 4.083V5.013C10.5502 5.07451 11.0584 5.3367 11.4274 5.74942C11.7963 6.16213 12.0002 6.69639 12 7.25V12.75C12 13.3467 11.7629 13.919 11.341 14.341C10.919 14.7629 10.3467 15 9.75 15H2.25C1.65326 15 1.08097 14.7629 0.65901 14.341C0.237053 13.919 0 13.3467 0 12.75V7.25C1.67206e-05 6.69656 0.204007 6.16254 0.572972 5.75004C0.941936 5.33754 1.44999 5.07549 2 5.014ZM4.239 2.25C4.46752 2.01366 4.7411 1.82552 5.04356 1.6967C5.34603 1.56789 5.67125 1.501 6 1.5C6.657 1.5 7.29 1.767 7.761 2.25C8.232 2.733 8.5 3.392 8.5 4.083V5H3.5V4.083C3.5 3.393 3.768 2.733 4.239 2.25ZM6 9.25C5.80109 9.25 5.61032 9.32902 5.46967 9.46967C5.32902 9.61032 5.25 9.80109 5.25 10V11C5.25 11.1989 5.32902 11.3897 5.46967 11.5303C5.61032 11.671 5.80109 11.75 6 11.75C6.19891 11.75 6.38968 11.671 6.53033 11.5303C6.67098 11.3897 6.75 11.1989 6.75 11V10C6.75 9.80109 6.67098 9.61032 6.53033 9.46967C6.38968 9.32902 6.19891 9.25 6 9.25Z" fill="currentColor"></path> </svg>
                    </span>
                    <span class="folder-count folder-count___id__"></span>
                </span>
            </button>
            <div class="sticky-folder-menu">
                <ul>
                    <li>
                        <a data-id="__id__" class="add-new-folder" id="add-sub-folder" href="javascript:;">
                            <span class="folder-icon pfolder-add-folder"></span>
                            <?php esc_html_e('New Sub-folder', 'folders'); ?>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" id="sticky-rename-folder" href="javascript:;">
                            <span class="folder-icon pfolder-vector-stroke-2"></span>
                            <?php esc_html_e('Rename', 'folders'); ?>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" id="sticky-remove-sticky-folder" href="javascript:;">
                            <span class="folder-icon pfolder-pin"></span>
                            <?php esc_html_e('Remove Sticky Folder', 'folders'); ?>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" class="star-folder" id="sticky-add-remove-star-folder" href="javascript:;">
                            <span class="folder-icon pfolder-star"></span>
                            <span class="is-default"><?php esc_html_e('Add Star', 'folders'); ?></span>
                            <span class="is-active"><?php esc_html_e('Remove Star', 'folders'); ?></span>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" class="lock-folder" id="sticky-lock-unlock-folder" href="javascript:;">
                            <span class="folder-icon pfolder-lock"></span>
                            <span class="is-default"><?php esc_html_e('Lock Folder', 'folders'); ?></span>
                            <span class="is-active"><?php esc_html_e('Unlock Folder', 'folders'); ?></span>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" class="duplicate-folder" id="sticky-duplicate-folder" href="javascript:;">
                            <span class="folder-icon pfolder-duplicate"></span>
                            <?php esc_html_e('Duplicate Folder', 'folders'); ?>
                        </a>
                    </li>
                    <li>
                        <a data-id="__id__" class="remove-folder" id="sticky-delete-folder" href="javascript:;">
                            <span class="folder-icon pfolder-trash"></span>
                            <?php esc_html_e('Delete', 'folders'); ?>
                        </a>
                        <a data-id="__id__" class="no-delete-folder" href="javascript:;">
                            <span class="folder-icon pfolder-trash"></span>
                            <?php esc_html_e('Delete', 'folders'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
