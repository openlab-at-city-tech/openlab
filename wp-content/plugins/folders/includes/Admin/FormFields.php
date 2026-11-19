<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Form Fields Management Class
 *
 * Handles the rendering of various form fields in the admin settings.
 */
class FormFields {

    /**
     * Register the actions that render the settings page form fields.
     */
    public function __construct() {
        add_action( 'folders_field_prefix_settings', array( $this, 'field_prefix_settings' ), 10, 2 );
        add_action( 'folders_field_label', array( $this, 'field_label' ), 10, 1 );
        add_action( 'folders_field_input', array( $this, 'field_input' ), 10, 4 );
        add_action( 'folders_field_tooltip', array( $this, 'field_tooltip' ), 10, 1 );
        add_action( 'folders_field_tooltip_use_shortcuts', array( $this, 'shortcuts_tooltip' ), 10, 1 );
        add_action( 'premio_folders_custom_color', array( $this, 'custom_color' ), 10, 1 );
        add_action( 'premio_folders_color_settings', array( $this, 'folders_color_settings' ), 10, 1 );
        add_action( 'folders_field_after_show_media_details', array( $this, 'show_media_details' ), 10, 4 );
    }

    /**
     * Render the folder icon color picker on the customization tab.
     *
     * Shows the saved colors as radio buttons for the default icon color,
     * with an "Upgrade to Pro" link for adding custom colors.
     *
     * @param array $args {
     *     Field arguments.
     *
     *     @type string   $title              Field label.
     *     @type string[] $folder_colors      Available colors.
     *     @type string   $default_icon_color Currently selected color.
     *     @type string   $upgrade_url        URL of the upgrade page.
     * }
     * @return void
     */
    public function folders_color_settings( $args = [] ) {
        if(empty($args)) {
            return;
        }
        $args['folder_colors'] = !isset($args['folder_colors']) ? array() : $args['folder_colors'];
        $args['default_icon_color'] = !isset($args['default_icon_color']) ? '' : $args['default_icon_color'];
        ?>
            <div class="flex flex-col md:flex-row gap-3 sm:justify-between sm:items-center">
                <div>
                    <label for="folders-size" class="text-base text-grey900">
                        <?php echo $args['title']; ?>
                        <div class="relative group inline-flex items-center gap-1 align-middle">
                            <div class="text-grey cursor-pointer w-4 h-4" role="img" aria-label="<?php esc_attr_e( 'More information', 'folders' ); ?>">
                                <svg class="w-4 h-4" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.33333 12.6667V9.33333M9.33333 6H9.34167M17.6667 9.33333C17.6667 13.9357 13.9357 17.6667 9.33333 17.6667C4.73096 17.6667 1 13.9357 1 9.33333C1 4.73096 4.73096 1 9.33333 1C13.9357 1 17.6667 4.73096 17.6667 9.33333Z" stroke="#A4A7AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-80 p-3 bg-white text-sm text-grey rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-grey200 font-normal leading-normal">
                                <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-grey200 rotate-45"></div>
                                <div class="flex flex-col gap-2">
                                    <div class="font-inter text-center">
                                        <?php esc_html_e( 'You can set the default icon color for Folders from here. Each folder and subfolder can have a different color, which you can change in the folder settings.', 'folders' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                <script>
                    let customColors = <?php echo json_encode($args['folder_colors']); ?>;
                </script>
                <div class="flex-1 max-w-60">
                    <div id="selected-folder-color" class="">
                        <div class="flex flex-wrap gap-4">
                            <?php foreach ($args['folder_colors'] as $key => $value) { ?>
                                <div>
                                    <input class="sr-only peer" id="default-color-<?php echo esc_attr($key) ?>" <?php checked($value, $args['default_icon_color']) ?> type="radio" name="customization_settings[default_icon_color]" value="<?php echo esc_attr( $value ); ?>" />
                                    <label class="inline-flex w-10 h-10 cursor-pointer rounded-lg peer-checked:[&_.custom-check]:opacity-100! peer-checked:outline-2! peer-checked:outline-offset-2! peer-checked:outline-[#2A9D8F]! hover:outline-2! hover:outline-offset-2! hover:outline-[#2A9D8F]!" for="default-color-<?php echo esc_attr($key) ?>">
                                        <span class="inline-flex w-10 h-10 items-center justify-center rounded-lg border-1 border-grey300" style="background-color: <?php echo esc_attr($value) ?>">
                                            <svg class="custom-check opacity-0 w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17L4 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="hidden" id="default-colors-wrap">
                            <div>
                                <input class="sr-only peer" id="default-radio-color-__count__" type="radio" name="customization_settings[default_icon_color]" value="" />
                                <label class="inline-flex w-10 h-10 cursor-pointer rounded-lg peer-checked:[&_.custom-check]:opacity-100! peer-checked:outline-2! peer-checked:outline-offset-2! peer-checked:outline-[#2A9D8F]! hover:outline-2! hover:outline-offset-2! hover:outline-[#2A9D8F]!" for="default-radio-color-__count__">
                                    <span class="inline-flex w-10 h-10 items-center justify-center rounded-lg default-radio-color-__count__" style="">
                                        <svg class="custom-check opacity-0 w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <a target="_blank" href="<?php echo esc_url($args['upgrade_url']) ?>" class="w-auto h-10 inline-flex rounded-lg! border-1 border-grey300 items-center justify-center px-3 mt-4 gap-2.5 text-[#181D27]! text-base! font-medium hover:bg-primary! hover:text-white!">
                            <svg class="w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <?php esc_html_e('Add More Colors', 'folders'); ?>
                            <img class="w-5 h-5" src="<?php echo esc_url( FOLDERS_IMAGE_URL . 'settings/pro-crown.svg' ); ?>" alt="">
                        </a>
                    </div>
                    <div id="select-folder-color" class="hidden py-2 px-2 border-1 border-grey300 rounded-lg">
                        <div class="flex flex-wrap gap-2" id="custom-folder-colors-wrap">
                            <div class="relative folder-color">
                                <input class="sr-only" data-id="__count__" id="custom_color_option___count__" type="hidden" name="customization_settings[folder_colors][]" value="" />
                                <div class="custom-color-wrap">
                                    <label class="sr-only" for="custom-color-__count__"><?php esc_html_e('Custom Color', 'folders'); ?></label>
                                    <input class="sr-only peer" data-id="__count__" id="custom-color-__count__" type="text" value="" />
                                </div>
                                <button type="button" class="remove-custom-color absolute bg-white rounded-full p-0! m-0! text-red-700 border border-1 border-grey300 -right-1 -top-1 w-3.5 h-3.5 inline-flex items-center justify-center hover:border-red-700 hover:bg-red-700 hover:text-white">
                                    <svg class="w-1.5 h-1.5 inline-flex" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative" id="folder-custom-color">
                                <div class="custom-color-wrap">
                                    <label class="sr-only" for="custom-folder-color"><?php esc_html_e('Custom Color', 'folders'); ?></label>
                                    <input class="sr-only peer custom-folder-color" id="custom-folder-color" type="text" value="#ffffff" />
                                </div>
                            </div>
                        </div>
                        <div class="h-px bg-grey300 w-full my-2"></div>
                        <div class="flex item-center gap-2">
                            <button id="save-custom-colors" type="button" class="text-xs! text-primary! hover:text-primaryDark!">
                                <?php esc_html_e('Save', 'folders'); ?>
                            </button>
                            <button id="cancel-custom-colors" type="button" class="text-xs! text-primary! hover:text-primaryDark!">
                                <?php esc_html_e('Cancel', 'folders'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     * Render a color picker field with three preset colors and an optional custom color.
     *
     * The custom color option is only enabled for Pro fields with a valid
     * license; otherwise an "Upgrade to Pro" link is shown.
     *
     * @param array $args {
     *     Field arguments.
     *
     *     @type string $id          Field ID.
     *     @type string $name        Key inside `customization_settings[...]`.
     *     @type string $title       Field label (may contain HTML).
     *     @type string $value       Currently selected color.
     *     @type bool   $is_pro      Whether the custom color option is a Pro feature.
     *     @type bool   $is_valid    Whether a valid Pro license is active.
     *     @type string $upgrade_url URL of the upgrade page.
     * }
     * @return void
     */
    public function custom_color( $args ) {
        if(empty($args)) {
            return;
        }
        $defaultColors = [
            '#FA166B',
            '#0073AA',
            '#484848'
        ];
        $currentColor = $args['value'];
        $hasCustomColor = ! in_array( $currentColor, $defaultColors );
        ?>
            <div class="flex flex-col md:flex-row gap-3 sm:justify-between sm:items-center">
                <div>
                    <span id="color-label-<?php echo esc_attr( $args['id'] ); ?>" class="text-base text-grey900"><?php echo wp_kses_post( $args['title'] ); ?></span>
                </div>
                <div class="flex-1 max-w-60">
                    <div class="flex items-center gap-4">
                        <?php foreach ($defaultColors as $key=>$color) { ?>
                            <div>
                                <input <?php checked($color, $currentColor) ?> type="radio" class="sr-only peer" value="<?php echo esc_attr($color) ?>" id="color-<?php echo esc_attr($args['id']."-".$key) ?>" name="customization_settings[<?php echo esc_attr($args['name']); ?>]" aria-labelledby="color-label-<?php echo esc_attr( $args['id'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%1$s: %2$s', 'folders' ), wp_strip_all_tags( $args['title'] ), $color ) ); ?>">
                                <label class="w-10 h-10 cursor-pointer inline-flex rounded-lg peer-checked:outline-2! peer-checked:outline-offset-2! peer-checked:outline-[#2A9D8F]! hover:outline-2! hover:outline-offset-2! hover:outline-[#2A9D8F]! peer-checked:[&_.custom-check]:opacity-100!" for="color-<?php echo esc_attr($args['id']."-".$key) ?>">
                                    <span class="w-10 border-1 border-grey300 h-10 inline-flex rounded-lg items-center justify-center" style="background-color: <?php echo esc_attr($color); ?>">
                                        <svg class="custom-check opacity-0 w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        <?php }
                        if ($args['is_pro'] && $args['is_valid']) { ?>
                            <div class="custom-color-wrap">
                                <div class="sr-only">
                                    <input type="text" class="custom-color-field" aria-hidden="true" tabindex="-1">
                                </div>
                                <input <?php checked($hasCustomColor, true) ?> type="radio" class="sr-only peer" value="<?php echo esc_attr($currentColor) ?>" id="color-<?php echo esc_attr($args['id']."-custom") ?>" name="customization_settings[<?php echo esc_attr($args['name']); ?>]" aria-label="<?php echo esc_attr( sprintf( __( '%s: custom color', 'folders' ), wp_strip_all_tags( $args['title'] ) ) ); ?>">
                                <label class="custom-color-label w-10 h-10 cursor-pointer inline-flex rounded-lg peer-checked:outline-2! peer-checked:outline-offset-2! peer-checked:outline-[#2A9D8F]! hover:outline-2! hover:outline-offset-2! hover:outline-[#2A9D8F]! peer-checked:[&_.custom-color]:hidden! peer-checked:[&_.custom-select-color]:inline-flex!" for="color-<?php echo esc_attr($args['id']."-custom") ?>">
                                    <span class="custom-select-color w-10 h-10 rounded-lg hidden border-1 border-grey300 items-center justify-center" style="background-color: <?php echo esc_attr($currentColor); ?>">
                                        <svg class="w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="custom-color w-10 h-10 inline-flex rounded-lg border-1 border-grey300 items-center justify-center">
                                        <svg class="w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="#717680" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>
                            </div>
                        <?php } else { ?>
                            <div>
                                <a href="<?php echo esc_url($args['upgrade_url'])  ?>" target="_blank" class="pro-feature relative w-10 h-10 cursor-pointer inline-flex rounded-lg peer-checked:outline-2! peer-checked:outline-offset-2! peer-checked:outline-[#2A9D8F]! hover:outline-2! hover:outline-offset-2! hover:outline-[#2A9D8F]! peer-checked:[&_.custom-color]:hidden! peer-checked:[&_.custom-select-color]:inline-flex!" for="color-<?php echo esc_attr($args['id']."-custom") ?>">
                                    <span class="w-10 h-10 inline-flex rounded-lg border-1 border-grey300 items-center justify-center">
                                        <svg class="w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="#717680" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="license-url absolute! left-full! mt-1.5! ml-2! py-1!">Upgrade to Pro</span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     * Render shortcuts tooltip.
     *
     * @return void
     */
    public function shortcuts_tooltip() { ?>
        <button data-modal-id="folders-shortcuts" class="flex items-center folders-modal-button gap-1.5 text-grey" >
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_1997_1274)">
                    <rect x="0.5" y="4.25" width="19" height="11.5" rx="1.5" stroke="#73738A"/>
                    <path d="M9.10714 6.78488C9.10714 6.58763 9.26704 6.42773 9.46429 6.42773H10.5357C10.733 6.42773 10.8929 6.58763 10.8929 6.78488V7.85631C10.8929 8.05355 10.733 8.21345 10.5357 8.21345H9.46429C9.26704 8.21345 9.10714 8.05355 9.10714 7.85631V6.78488ZM9.10714 9.46345C9.10714 9.2662 9.26704 9.10631 9.46429 9.10631H10.5357C10.733 9.10631 10.8929 9.2662 10.8929 9.46345V10.5349C10.8929 10.7321 10.733 10.892 10.5357 10.892H9.46429C9.26704 10.892 9.10714 10.7321 9.10714 10.5349V9.46345ZM6.42857 6.78488C6.42857 6.58763 6.58847 6.42773 6.78571 6.42773H7.85714C8.05439 6.42773 8.21429 6.58763 8.21429 6.78488V7.85631C8.21429 8.05355 8.05439 8.21345 7.85714 8.21345H6.78571C6.58847 8.21345 6.42857 8.05355 6.42857 7.85631V6.78488ZM6.42857 9.46345C6.42857 9.2662 6.58847 9.10631 6.78571 9.10631H7.85714C8.05439 9.10631 8.21429 9.2662 8.21429 9.46345V10.5349C8.21429 10.7321 8.05439 10.892 7.85714 10.892H6.78571C6.58847 10.892 6.42857 10.7321 6.42857 10.5349V9.46345ZM3.75 9.46345C3.75 9.2662 3.9099 9.10631 4.10714 9.10631H5.17857C5.37582 9.10631 5.53571 9.2662 5.53571 9.46345V10.5349C5.53571 10.7321 5.37582 10.892 5.17857 10.892H4.10714C3.9099 10.892 3.75 10.7321 3.75 10.5349V9.46345ZM3.75 6.78488C3.75 6.58763 3.9099 6.42773 4.10714 6.42773H5.17857C5.37582 6.42773 5.53571 6.58763 5.53571 6.78488V7.85631C5.53571 8.05355 5.37582 8.21345 5.17857 8.21345H4.10714C3.9099 8.21345 3.75 8.05355 3.75 7.85631V6.78488ZM6.42857 12.142C6.42857 11.9448 6.58847 11.7849 6.78571 11.7849H13.2143C13.4115 11.7849 13.5714 11.9448 13.5714 12.142V13.2134C13.5714 13.4107 13.4115 13.5706 13.2143 13.5706H6.78571C6.58847 13.5706 6.42857 13.4107 6.42857 13.2134V12.142ZM11.7857 9.46345C11.7857 9.2662 11.9456 9.10631 12.1429 9.10631H13.2143C13.4115 9.10631 13.5714 9.2662 13.5714 9.46345V10.5349C13.5714 10.7321 13.4115 10.892 13.2143 10.892H12.1429C11.9456 10.892 11.7857 10.7321 11.7857 10.5349V9.46345ZM11.7857 6.78488C11.7857 6.58763 11.9456 6.42773 12.1429 6.42773H13.2143C13.4115 6.42773 13.5714 6.58763 13.5714 6.78488V7.85631C13.5714 8.05355 13.4115 8.21345 13.2143 8.21345H12.1429C11.9456 8.21345 11.7857 8.05355 11.7857 7.85631V6.78488ZM14.4643 9.46345C14.4643 9.2662 14.6242 9.10631 14.8214 9.10631H15.8929C16.0901 9.10631 16.25 9.2662 16.25 9.46345V10.5349C16.25 10.7321 16.0901 10.892 15.8929 10.892H14.8214C14.6242 10.892 14.4643 10.7321 14.4643 10.5349V9.46345ZM14.4643 6.78488C14.4643 6.58763 14.6242 6.42773 14.8214 6.42773H15.8929C16.0901 6.42773 16.25 6.58763 16.25 6.78488V7.85631C16.25 8.05355 16.0901 8.21345 15.8929 8.21345H14.8214C14.6242 8.21345 14.4643 8.05355 14.4643 7.85631V6.78488Z" fill="#73738A"/>
                </g>
                <defs>
                    <clipPath id="clip0_1997_1274">
                        <rect width="20" height="20" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
            <span class="text-grey underline"><?php esc_html_e( 'View Shortcuts', 'folders'); ?></span>
        </button>
    <?php }

    /**
     * Placeholder for content rendered before a settings field. Outputs nothing.
     *
     * @param array  $field Field data.
     * @param string $value Field value.
     *
     * @return void
     */
    public function field_prefix_settings( $field, $value = 'no' ) {
    }

    /**
     * Render field label.
     *
     * @param array $field Field data.
     *
     * @return void
     */
    public function field_label( $field ) {
        if ( in_array( $field['type'], array( 'input' ) )  ) {
            ?>
            <label class="mb-2 text-sm text-grey" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
        <?php }
    }

    /**
     * Render field input.
     *
     * @param array  $field      Field data.
     * @param string $value      Current value.
     * @param bool   $isValid    Whether the input is valid.
     * @param string $upgradeUrl Upgrade URL.
     *
     * @return void
     */
    public function field_input( $field, $value = '', $isValid = false, $upgradeUrl = '' ) {
        $disabled = ( ! $isValid && $field['is_pro'] ) ? 'disabled' : '';
        $readonly = ( ! $isValid && $field['is_pro'] ) ? 'readonly' : '';
        $value    = ( ! $isValid && $field['is_pro'] ) ? 0 : $value;
        ?>
        <div class="folder-field-wrap flex items-center gap-2 <?php echo esc_attr( $field['is_pro'] ? 'pro-feature' : '' ); ?>">
            <?php if ( $disabled ) { ?>
                <a href="<?php echo esc_url( $upgradeUrl ); ?>" target="_blank" class="relative inline-flex items-center gap-1 align-middle">
            <?php } ?>
            <?php if ( 'checkbox' == $field['type'] ) { ?>
                <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="0">
                <<?php echo esc_attr( $disabled ? 'span' : 'label' ); ?> for="<?php echo esc_attr( $field['id'] ); ?>" class="relative inline-flex items-center cursor-pointer">
                    <input <?php echo esc_attr( $disabled ); ?> <?php checked( $field['value'], $value ); ?> id="<?php echo esc_attr( $field['id'] ); ?>" type="checkbox" value="1" name="<?php echo esc_attr( $field['name'] ); ?>" class="sr-only peer">
                    <span class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal"></span>
                    <?php if ( $disabled ) { ?>
                        <img class="ml-2 rtl:ml-0 rtl:mr-2 text-sm text-grey" src="<?php echo esc_url( FOLDERS_IMAGE_URL . 'settings/pro-crown.svg' ); ?>" alt="">
                    <?php } ?>
                    <span class="ml-2 rtl:ml-0 rtl:mr-2 text-sm text-grey"><?php echo esc_html( $field['label'] ); ?></span>
                </<?php echo esc_attr( $disabled ? 'span' : 'label' ); ?>>
                <?php do_action( 'folders_field_tooltip', $field ); ?>
                <?php do_action( 'folders_field_tooltip_' . esc_attr( $field['id'] ), $field ); ?>
            <?php } else if ( 'timeout' == $field['type'] ) { ?>
                <div class="flex items-center gap-2 pl-11">
                    <label class="text-sm text-grey" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <div class="seconds-box">
                        <input <?php echo esc_attr( $readonly ); ?> class="border! border-grey300! w-16! pr-2! rounded-lg!" id="<?php echo esc_attr( $field['id'] ); ?>" type="number"  min="0" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
                    </div>
                    <span class="folder-label"><?php esc_html_e( 'Seconds', 'folders'); ?></span>
                </div>
            <?php } else if ( 'upload_size' == $field['type'] ) { ?>
                <div class="flex items-center gap-2 pl-11">
                    <label class="text-sm text-grey" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <div class="seconds-box">
                        <input <?php echo esc_attr( $readonly ); ?> class="border! border-grey300! w-18! pr-2! rounded-lg!" id="<?php echo esc_attr( $field['id'] ); ?>" type="number"  min="0" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" /> MB
                    </div>
                </div>
            <?php } else if ( 'select' == $field['type'] ) { ?>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label class="text-sm text-grey" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <div>
                        <select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
                            <?php foreach ($field['options'] as $key => $option) { ?>
                                <?php
                                $id = isset($option['id']) ? strval($option['id']) : uniqid("radio-");
                                $label = isset($option['label']) ? $option['label'] : "";
                                ?>
                                <option <?php selected($key, $value) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($option) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if ( $disabled ) { ?>
                <span class="license-url"><?php esc_html_e( 'Upgrade to Pro', 'folders' ); ?></span>
                </a>
            <?php } ?>
        </div>
        <?php
    }

    /**
     * Render field tooltip.
     *
     * @param array $field Field data.
     *
     * @return void
     */
    public function field_tooltip( $field ) {
        if ( $field['has_tooltip'] && ! empty( $field['tooltip'] ) ) {
            ?>
            <div class="relative group inline-flex items-center gap-1 align-middle">
                <div class="text-grey cursor-pointer w-4 h-4">
                    <svg class="w-4 h-4" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.33333 12.6667V9.33333M9.33333 6H9.34167M17.6667 9.33333C17.6667 13.9357 13.9357 17.6667 9.33333 17.6667C4.73096 17.6667 1 13.9357 1 9.33333C1 4.73096 4.73096 1 9.33333 1C13.9357 1 17.6667 4.73096 17.6667 9.33333Z" stroke="#A4A7AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-[280px] p-3 bg-white text-sm text-grey rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-grey200 font-normal leading-normal">
                    <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-grey200 rotate-45"></div>
                    <div class="flex flex-col gap-2">
                        <div class="font-inter">
                            <?php echo wp_kses_post( $field['tooltip'] ); ?>
                        </div>
                        <?php if ( isset( $field['tooltip_image'] ) && ! empty( $field['tooltip_image'] ) ) { ?>
                            <img src="<?php echo esc_url( $field['tooltip_image'] ); ?>" class="w-full h-auto rounded border border-grey200" alt="Tooltip">
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Get basic general settings.
     *
     * @return array Basic settings.
     */
    public static function get_basic_settings() {
        $general_settings = \Folders\Admin\Settings::get_field_settings( 'general_settings' );
        return [
            'use_shortcuts'                => [
                'label'          => esc_html__( 'Use keyboard shortcuts to navigate faster', 'folders'),
                'name'           => 'general_settings[use_shortcuts]',
                'type'           => 'checkbox',
                'id'             => 'use_shortcuts',
                'value'          => 1,
                'has_tooltip'    => false,
                'tooltip'        => '',
                'is_recommended' => false,
                'label_class'    => 'inline-flex',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'dynamic_folders'              => [
                'label'          => esc_html__( 'Dynamic Folders', 'folders'),
                'name'           => 'general_settings[dynamic_folders]',
                'type'           => 'checkbox',
                'id'             => 'dynamic_folders',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Automatically filter posts/pages/custom posts/media files based on author, date, file types & more', 'folders'),
                'tooltip_image'  => FOLDERS_IMAGE_URL . 'tooltip/dynamic-folders.gif',
                'is_recommended' => true,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
            'use_folder_undo'              => [
                'label'          => esc_html__( 'Use folders with Undo action after performing tasks', 'folders'),
                'name'           => 'general_settings[use_folder_undo]',
                'type'           => 'checkbox',
                'id'             => 'use_folder_undo',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Undo any action on Folders. Undo move, copy, rename etc actions with this feature', 'folders'),
                'tooltip_image'  => FOLDERS_IMAGE_URL . 'tooltip/undo-feature-folders.gif',
                'is_recommended' => true,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'default_timeout'              => [
                'label'          => esc_html__( 'Default timeout', 'folders'),
                'name'           => 'general_settings[default_timeout]',
                'type'           => 'timeout',
                'id'             => 'default_timeout',
                'value'          => '5',
                'has_tooltip'    => false,
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => 'timeout-settings ' . esc_attr( 1 == $general_settings['use_folder_undo'] ? '' : 'hidden' ),
            ],
            'folders_enable_replace_media' => [
                'label'          => esc_html__( 'Enable Replace Media', 'folders'),
                'name'           => 'general_settings[folders_enable_replace_media]',
                'type'           => 'checkbox',
                'id'             => 'folders_enable_replace_media',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'The Replace Media feature will allow you to replace your media files throughout your website with the click of a button,  which means the file will be replaced for all your posts, pages, etc', 'folders'),
                'is_recommended' => true,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'show_media_details'           => [
                'label'          => esc_html__( 'Show media details on hover', 'folders'),
                'name'           => 'general_settings[show_media_details]',
                'type'           => 'checkbox',
                'id'             => 'show_media_details',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Show useful metadata including title, size, type, date, dimension & more on hover.', 'folders'),
                'tooltip_image'  => FOLDERS_IMAGE_URL . 'tooltip/folders-media.gif',
                'is_recommended' => true,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
        ];
    }

    /**
     * Get maximum upload file size.
     *
     * @return int Size in MB.
     */
    public static function max_upload_file_size() {
        $max_upload_size = wp_max_upload_size();
        return $max_upload_size / 1024 / 1024;
    }

    /**
     * Get advanced settings fields.
     *
     * @return array Advanced settings.
     */
    public static function get_advance_settings() {
        $general_settings = \Folders\Admin\Settings::get_field_settings( 'general_settings' );
        return [
            'use_max_upload_size'     => [
                'label'          => esc_html__( 'Max Upload File Size', 'folders'),
                'name'           => 'general_settings[use_max_upload_size]',
                'type'           => 'checkbox',
                'id'             => 'use_max_upload_size',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Specify the maximum allowed file size for uploads. This setting helps increase the size of files that users can upload.', 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
            'max_upload_size'         => [
                'label'          => esc_html__( 'Maximum upload size', 'folders'),
                'name'           => 'general_settings[max_upload_size]',
                'type'           => 'upload_size',
                'id'             => 'max_upload_size',
                'value'          => self::max_upload_file_size(),
                'has_tooltip'    => false,
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => 'upload-size-settings hidden',
            ],
            'enable_media_trash'      => [
                'label'          => esc_html__( 'Move files to trash by default before deleting', 'folders'),
                'name'           => 'general_settings[enable_media_trash]',
                'type'           => 'checkbox',
                'id'             => 'enable_media_trash',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'When enabled, files will be moved to trash to prevent mistakes, and then you can delete permanently from the trash', 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
            'folders_media_cleaning'  => [
                'label'          => esc_html__( 'Use Media Cleaning to clear unused media files', 'folders'),
                'name'           => 'general_settings[folders_media_cleaning]',
                'type'           => 'checkbox',
                'id'             => 'folders_media_cleaning',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'The Media Cleaning feature enables you to clean unused media files for your WordPress site and adds a Media Cleaning item under Media', 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
            'replace_media_title'     => [
                'label'          => esc_html__( 'Auto Rename file based on title', 'folders'),
                'name'           => 'general_settings[replace_media_title]',
                'type'           => 'checkbox',
                'id'             => 'replace_media_title',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Replace the actual file name of media files with the title from the WordPress editor.', 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => true,
                'field_class'    => '',
            ],
            'open_new_folder_by_default'  => [
                'label'          => esc_html__( 'Open new folder by default', 'folders'),
                'name'           => 'general_settings[open_new_folder_by_default]',
                'type'           => 'checkbox',
                'id'             => 'open_new_folder_by_default',
                'value'          => 1,
                'has_tooltip'    => false,
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'force_sorting'           => [
                'label'          => esc_html__( 'Force sorting every time when a new folder is added', 'folders'),
                'name'           => 'general_settings[force_sorting]',
                'type'           => 'checkbox',
                'id'             => 'force_sorting',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'If unchecked, the new folder will be added at the top or bottom based on folder placement setting. If checked, the new folder will be placed according to your selected sorting order. For example, if sorting is set to A to Z, the new folder will be added in alphabetical order.', 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'show_folder_in_settings' => [
                'label'          => esc_html__( 'Place the Folders settings page nested under "Settings"', 'folders'),
                'name'           => 'general_settings[show_folder_in_settings]',
                'type'           => 'checkbox',
                'id'             => 'show_folder_in_settings',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( "When this setting is enabled, you will be able to access the Folders settings page by clicking on 'Settings' in the WordPress dashboard and then selecting 'Folders' from the submenu.", 'folders'),
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
            'folders_show_in_menu'    => [
                'label'          => esc_html__( 'Show the folders also in WordPress menu', 'folders'),
                'name'           => 'general_settings[folders_show_in_menu]',
                'type'           => 'checkbox',
                'id'             => 'folders_show_in_menu',
                'value'          => 1,
                'has_tooltip'    => true,
                'tooltip'        => esc_html__( 'Show folders separately on your WordPress sidebar for quick and easy access to folders of Media, Posts, Pages etc', 'folders'),
                'tooltip_image'  => FOLDERS_IMAGE_URL . 'tooltip/page-folders.png',
                'is_recommended' => false,
                'label_class'    => '',
                'is_pro'         => false,
                'field_class'    => '',
            ],
                'new_folder_placement'  => [
                    'label'          => esc_html__( 'New folder placement', 'folders'),
                    'name'           => 'general_settings[new_folder_placement]',
                    'type'           => 'select',
                    'id'             => 'new_folder_placement',
                    'value'          => 'top',
                    'has_tooltip'    => false,
                    'is_recommended' => false,
                    'label_class'    => '',
                    'is_pro'         => false,
                    'field_class'    => 'folder-placement ' . esc_attr( !$general_settings['force_sorting'] ? '' : 'hidden' ),
                    'options'        => [
                        'top'  => esc_html__( 'Add to top', 'folders'),
                        'end'  => esc_html__( 'Add to end', 'folders'),
                    ]
            ],
        ];
    }

    /**
     * Render the multi-select that chooses which media details are shown on hover.
     *
     * Only rendered when `$is_valid` is true. The select is hidden until the
     * "show media details" setting is enabled.
     *
     * @param array  $field       Field data.
     * @param array  $settings    General settings, including `show_media_details` and `media_col_settings`.
     * @param bool   $is_valid    Whether a valid Pro license is active.
     * @param string $upgrade_url URL of the upgrade page.
     * @return void
     */
    public function show_media_details($field, $settings, $is_valid = false, $upgrade_url = '') {
        if(!$is_valid) {
            return;
        }
        $disabled = $is_valid?'':'disabled';
        $media_settings = array(
            'image_title' => array(
                    "title" => esc_html__("Title", 'folders'),
                    "default" => "on",
            ),
            'image_alt_text' => array(
                    "title" => esc_html__("Alternative Text", 'folders'),
                    "default" => "off",
            ),
            'image_file_url' => array(
                    "title" => esc_html__("File URL", 'folders'),
                    "default" => "off",
            ),
            'image_dimensions' => array(
                    "title" => esc_html__("Dimensions", 'folders'),
                    "default" => "on",
            ),
            'image_size' => array(
                    "title" => esc_html__("Size", 'folders'),
                    "default" => "off",
            ),
            'image_file_name' => array(
                    "title" => esc_html__("Filename", 'folders'),
                    "default" => "off",
            ),
            'image_type' => array(
                    "title" => esc_html__("Type", 'folders'),
                    "default" => "on",
            ),
            'image_date' => array(
                    "title" => esc_html__("Date", 'folders'),
                    "default" => "on",
            ),
            'image_uploaded_by' => array(
                    "title" => esc_html__("Uploaded by", 'folders'),
                    "default" => "off",
            )
        );
        $selected_media_settings = isset($settings['media_col_settings'])?$settings['media_col_settings']:[];
        $is_active = ($settings['show_media_details'] == 1)?'':'hidden';


        ?>
        <div class="folder-single-field-items max-w-sm <?php echo esc_attr($is_active)?>" id="media-col-settings">
            <div class="normal-box">
                <input type="hidden" name="general_settings[media_col_settings][]" value="all">
                <div class="form-input">
                    <label class="sr-only" for="media-hover-fields"><?php esc_html_e('Select details which you want to show on media hover', 'folders'); ?></label>
                    <select <?php echo esc_attr($disabled)?> multiple="multiple" name="general_settings[media_col_settings][]" id="media-hover-fields">
                        <?php foreach ($media_settings as $key => $media) {
                            $selected = in_array($key, $selected_media_settings)?1:0;
                            ?>
                            <option <?php selected($selected, 1) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($media['title']) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php if(!$is_valid) { ?>
                <div class="upgrade-box">
                    <a href="<?php echo esc_url($upgrade_url) ?>" target="_blank" class="upgrade-link">Upgrade to Pro</a>
                </div>
            <?php } ?>
        </div>
        <?php
    }
}
