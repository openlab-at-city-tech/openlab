<?php
// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<div class="emcs-choose-customizer-form emcs emcs-text-center" id="<?php echo esc_attr($owner); ?>">
    <div class="sc-wrapper">
        <div class="sc-container">
            <form action="<?php echo esc_url(admin_url('admin.php?page=emcs-customizer')); ?>" method="POST">
                <div class="form-group">
                    <label for="choose-customizer">Choose Event Type</label>
                </div>
                <div class="form-group">
                    <select name="emcs-choose-customizer-select" class="emcs-choose-customizer-select">
                        <?php

                        foreach ($events as $event) {
                        ?>
                            <option value="<?php echo esc_attr($event->slug); ?>" id="<?php echo esc_attr($event->slug); ?>"><?php echo esc_attr($event->name); ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <button type="submit" name="emcs-choose-customizer" class="button button-primary emcs-button-primary">Start customizing</button>
                </div>
            </form>
        </div>
    </div>
</div>