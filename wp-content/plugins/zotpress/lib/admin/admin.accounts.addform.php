<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

            <h3><?php esc_html_e('Add a Zotero Account','zotpress'); ?></h3>

            <?php //$phpSelf = htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8"); ?>
            <?php 
            $phpSelf = false;
            if ( isset($_SERVER["PHP_SELF"]) )
                $phpSelf = sanitize_url(wp_unslash($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8"));
            ?>

            <form action="<?php echo esc_url($phpSelf); ?>" method="post" id="zp-Add" name="zp-Add">

                <fieldset>
                    <input class="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" type="hidden" value="<?php echo esc_url(ZOTPRESS_PLUGIN_URL); ?>" />

                    <div class="field">
                        <label for="account_type" class="required" title="<?php esc_html_e('Account Type','zotpress'); ?>"><?php esc_html_e('Account Type','zotpress'); ?></label>
                        <select id="account_type" name="account_type" tabindex="1">
                            <option value="users">User</option>
                            <option value="groups">Group</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="api_user_id" class="required" title="<?php esc_html_e('API User ID','zotpress'); ?>"><?php esc_html_e('API User ID','zotpress'); ?></label>
                        <input id="api_user_id" name="api_user_id" type="text" tabindex="2">
						<aside>
							<p>
                                <?php echo sprintf(
                                    wp_kses(
                                        /* translators: s: Zotero API URL */
                                        __( 'The API User ID for <strong>User</strong> (individual, personal) accounts can be found on the <a href="%s" target="_blank">Zotero Settings > Keys</a> page, right above where you create a new key. ', 'zotpress' ),
                                        array(
                                            'a' => array(
                                                'href' => array()
                                            ),
                                            'strong' => array()
                                        )
                                    ), esc_url( 'https://www.zotero.org/settings/keys' )
                                );

                                echo sprintf(
                                    wp_kses(
                                        /* translators: s: Zotero Groups URL */
                                        __( 'The API User ID for <strong>Group</strong> accounts can be found on the <a href="%s" target="_blank">Zotero Group</a> page. Hover over the title of a group or click the title of the group to see the URL; the API User ID is the number in the URL.', 'zotpress' ),
                                        array(
                                            'a' => array(
                                                'href' => array()
                                            ),
                                            'strong' => array()
                                        )
                                    ), esc_url( 'https://www.zotero.org/groups/' )
                                );
                                ?>
							</p>
						</aside>
                    </div>

                    <div class="field zp-public_key">
                        <label for="public_key" class="zp-Help required" title="<?php esc_html_e('Private Key','zotpress'); ?>"><?php esc_html_e('Private Key','zotpress'); ?></label>
                        <input id="public_key" name="public_key" type="text" tabindex="3" />
						<aside>
							<p>
                                <?php esc_html_e('A private key is required for Zotpress to make requests to Zotero from WordPress. ','zotpress');                                

                                if ( isset( $oauth_is_not_installed )
                                        && $oauth_is_not_installed === false )
                                {
                                    echo '<strong>';
                                    echo wp_kses(
                                        __( "You can create a key using OAuth <u>after</u> you've added your account.", 'zotpress' ),
                                        array(
                                            'u' => array(),
                                            'strong' => array()
                                        )
                                    );
                                    echo '</strong>';
                                }
                                else
                                {
                                    echo sprintf(
                                        wp_kses(
                                            /* translators: s: Zotero API URL */
                                            __( 'Go to the <a href="%s" target="_blank">Zotero Settings > Keys</a> page and choose <strong>"Create new private key."</strong>', 'zotpress' ),
                                            array(
                                                'a' => array(
                                                    'href' => array()
                                                ),
                                                'strong' => array()
                                            )
                                        ), esc_url( 'https://www.zotero.org/settings/keys' )
                                    );
                                }

                                echo sprintf(
                                    wp_kses(
                                        /* translators: s: Zotero Groups URL */
                                        __( 'If you\'ve already created a key, you can find it on the <a href="%s" target="_blank">Zotero Settings > Keys</a> page. Make sure that <strong>"Allow library access"</strong> is checked. For groups, make sure the Default Group Permissions or Specific Group Permissions are set to "<strong>Read Only</strong>" or "Read/Write." ', 'zotpress' ),
                                        array(
                                            'a' => array(
                                                'href' => array()
                                            ),
                                            'strong' => array()
                                        )
                                    ), esc_url( 'https://www.zotero.org/settings/keys' )
                                );
                                ?>
							</p>
						</aside>
                    </div>

                    <div class="field last">
                        <label for="nickname" class="zp-Help" title="<?php esc_html_e('Nickname','zotpress'); ?>"><span><?php esc_html_e('Nickname','zotpress'); ?></span></label>
                        <input id="nickname" name="nickname" type="text" tabindex="4" />
						<aside>
							<p>
								<?php esc_html_e('Your API User ID can be hard to remember. Make it easier for yourself by giving your account a nickname.','zotpress'); ?>
							</p>
						</aside>
                    </div>

                    <div class="proceed">
                        <input id="zp-Connect" name="zp-Connect" class="button-primary" type="submit" value="<?php esc_html_e('Validate','zotpress'); ?>" tabindex="5" />
                    </div>

                    <div class="message">
                        <div class="zp-Loading">loading</div>
                        <div class="zp-Errors"><p><?php esc_html_e('Errors','zotpress'); ?>!</p></div>
                        <div class="zp-Success"><p><?php esc_html_e('Success','zotpress'); ?>!</p></div>
                    </div>

                </fieldset>

            </form>