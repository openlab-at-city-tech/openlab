<div class="form-field group-wrap">
    <h3><?php _e('Category Groups'); ?></h3>
    <label><input type="checkbox" id="clubCheck" value="club" name="group[]" <?php echo (isset($values['club']) ? 'checked="checked"' : '' ) ?> > Club</label><br>
    <label><input type="checkbox" id="projectCheck" value="project" name="group[]" <?php echo (isset($values['project']) ? 'checked="checked"' : '' ) ?> > Project</label>
</div>

<?php if (!$edit_form): ?>
    <input type="hidden" name="action" value="add-bp-customizable-category" />
<?php endif; ?>