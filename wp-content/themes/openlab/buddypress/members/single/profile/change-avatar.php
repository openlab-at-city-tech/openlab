<?php echo openlab_submenu_markup(); ?>
<div id="item-body" role="main">
<?php do_action( 'bp_before_profile_avatar_upload_content' ) ?>

<?php if ( !(int)bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

	<form action="" method="post" id="avatar-upload-form" enctype="multipart/form-data" class="form-inline form-panel">

                <div class="panel panel-default">

		<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>
                <div class="panel-heading">Upload Avatar</div>
                    <div class="panel-body">
                        <?php do_action('template_notices') ?>
                        <div class="row">
                            <div class="col-sm-8">
                                <div id="avatar-wrapper">
                                    <div class="padded-img">
                                        <img class="img-responsive padded" src ="<?php echo get_stylesheet_directory_uri(); ?>/images/avatar_blank.png" alt="avatar-blank"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-16">

                                <p>Upload an avatar (picture) to be used on your profile and throughout the site. Your avatar is displayed publicly. Because your avatar is public, you don't have to use a picture of yourself – you can use something that represents you or your interests.</p>

                                <p id="avatar-upload">
                                    <div class="form-group form-inline">
                                            <div class="form-control type-file-wrapper">
                                                <input type="file" name="file" id="file" />
												<label for="file" class="screen-reader-text">Select File to Upload</label>
                                            </div>
                                            <input class="btn btn-primary top-align" type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
                                            <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                    </div>
                                </p>

                                <?php if ( bp_get_user_has_avatar() ) : ?>
                                        <p class="italics"><?php _e( "If you'd like to delete your current avatar but not upload a new one, please use the delete avatar button.", 'buddypress' ) ?></p>
                                        <a class="btn btn-primary no-deco" href="<?php bp_avatar_delete_link() ?>" title="<?php _e( 'Delete Avatar', 'buddypress' ) ?>"><?php _e( 'Delete My Avatar', 'buddypress' ) ?></a>
                                <?php endif; ?>

                                <?php wp_nonce_field( 'bp_avatar_upload' ) ?>
                            </div>
                        </div>
                </div>

		<?php endif; ?>

		<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

                <div class="panel-heading">Crop Avatar</div>
                        <div class="panel-body">
                            <?php do_action('template_notices') ?>
                            <img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e('Avatar to crop', 'buddypress') ?>" />

                            <div id="avatar-crop-pane">
                                <img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e('Avatar preview', 'buddypress') ?>" />
                            </div>

                            <input class="btn btn-primary" type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e('Crop Image', 'buddypress') ?>" />

                            <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
                            <input type="hidden" id="x" name="x" />
                            <input type="hidden" id="y" name="y" />
                            <input type="hidden" id="w" name="w" />
                            <input type="hidden" id="h" name="h" />

                            <?php wp_nonce_field('bp_avatar_cropstore') ?>
                        </div>

		<?php endif; ?>
                </div><!--.panel-->

	</form>

<?php else : ?>

	<p><?php _e( 'Your avatar will be used on your profile and throughout the site. To change your avatar, please create an account with <a href="http://gravatar.com">Gravatar</a> using the same email address as you used to register with this site.', 'buddypress' ) ?></p>

<?php endif; ?>
</div>
<?php do_action( 'bp_after_profile_avatar_upload_content' ) ?>
