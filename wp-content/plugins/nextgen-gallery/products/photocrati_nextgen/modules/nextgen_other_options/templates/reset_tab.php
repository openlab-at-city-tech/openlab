<table>
    <tr>
        <td class='column1'>
            <span class='tooltip' title="<?php echo $reset_warning; ?>">
                <?php echo $reset_label; ?>
            </span>
        </td>
        <td>
            <input type="submit"
                   class="button-primary"
				   data-confirm="<?php echo $reset_confirmation; ?>"
				   data-proxy-value="reset"
                   name="action_proxy"
                   value="<?php echo $reset_value; ?>"
				/>
        </td>
    </tr>
    <?php /*
        <tr>
            <td class='column1'>
                <?php echo $uninstall_label; ?>
            </td>
            <td>
                <input type='submit'
                       name="action_proxy"
                       class="button delete button-secondary"
					   data-proxy-value="uninstall"
					   data-confirm="<?php echo $uninstall_confirmation; ?>"
					   value='<?php echo $uninstall_label; ?>'
				/>
            </td>
        </tr>
    */ ?>
</table>
