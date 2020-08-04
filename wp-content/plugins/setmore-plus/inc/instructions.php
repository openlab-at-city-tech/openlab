<?php if ( ! defined( 'ABSPATH' ) ) die; ?>

<div class="wrap setmore">

	<h3><?php _e( 'How to add Setmore to your site', 'setmore-plus' ); ?></h3>

	<table class="setmore-instructions">
		<tbody>
		<tr>
			<td><p><?php _e( 'To add a button in a <b>sidebar</b> that opens the scheduler in a popup', 'setmore-plus' ); ?></p></td>
			<td><p><?php _e( 'widget' ); ?></p></td>
		</tr>
		<tr>
			<td><p><?php _e( 'To <b>embed</b> the scheduler directly in a page', 'setmore-plus' ); ?></p></td>
			<td class="code">[setmoreplus]</td>
		</tr>
		<tr>
			<td>
				<p><?php _e( 'To add a <b>link</b> to the popup scheduler in a page', 'setmore-plus' ); ?></p>
				<p><em><?php _e( 'The link will be styled by your theme.', 'setmore-plus' ); ?></em></p>
			</td>
			<td class="code">[setmoreplus link]</td>
		</tr>
		<tr>
			<td>
				<p><?php _e( 'To add a <b>button</b> to the popup scheduler in a page', 'setmore-plus' ); ?></p>
				<p><em><?php _e( 'The button will be styled by your theme.', 'setmore-plus' ); ?></em></p>
			</td>
			<td class="code">[setmoreplus button]</td>
		</tr>
        <tr>
            <td>
                <p><?php _e( 'To select a language', 'setmore-plus' ); ?></p>
                <p class="lang-wrap"><em>
                    <?php echo implode( ', ', array_keys( Setmore_Plus::get_lang() ) ); ?>
                </em></p>
            </td>
            <td class="code">[setmoreplus lang=russian]</td>
        </tr>
		<tr>
			<td><p><?php _e( 'To select a Staff Booking Page', 'setmore-plus' ); ?></p></td>
			<td class="code">
				[setmoreplus staff=1]
				<br>
				[setmoreplus staff="Chris"]
			</td>
		</tr>
		<tr>
			<td>
				<p>
					<?php _e( 'For a <b>menu link</b>', 'setmore-plus' ); ?>
					(<a href="<?php echo SETMOREPLUS_IMAGES; ?>SetmorePlus-menu-link.png" class="screenshot-menu-link"><?php _e( 'screenshot', 'setmore-plus' ); ?></a>)
				</p>
			</td>
			<td>
				<ol>
					<li><?php _e( 'go to Appearance > Menus', 'setmore-plus' ); ?></li>
					<li><?php _e( 'click "Screen Options" in the upper right corner', 'setmore-plus' ); ?>
					<li><?php _e( 'if necessary, check the "Custom Links" and "CSS Classes" boxes', 'setmore-plus' ); ?></li>
					<li><?php _e( 'add a custom link using your Setmore URL and link text', 'setmore-plus' ); ?></li>
					<li><?php printf( __( 'enter %s in the CSS Classes field', 'setmore-plus'), '<input type="text" readonly value="setmore-iframe" style="width: 130px; font-family: consolas, monospace;">' ); ?></li>
				</ol>
			</td>
		</tr>
		<tr>
			<td><p><?php _e( 'To add the popup scheduler to a <b>plain URL</b>, append <code>#setmoreplus</code>', 'setmore-plus' ); ?></p>
				<p><?php _e( 'For example, if your theme already has a "Book Appointment" option and you can only enter your URL.', 'setmore-plus' ); ?></p></td>
			<td>
				<p><code><span style="color: #777;">http://example.setmore.com</span>#setmoreplus</code></p>
			</td>
		</tr>
		</tbody>
	</table>

	<h3>Customizing</h3>

	<table class="setmore-instructions">
		<tbody>
		<tr>
			<td><p><?php _e( 'With <code>link</code> or <code>button</code>, you can customize the text', 'setmore-plus' ); ?></p></td>
			<td class="code">[setmoreplus button]<?php _e( 'Make an appointment today!', 'setmore-plus' ); ?>[/setmoreplus]</td>
		</tr>
		<tr>
			<td><p><?php _e( 'and/or add CSS classes from your theme or custom CSS.', 'setmore-plus' ); ?></p></td>
			<td class="code">
				[setmoreplus button class="class-1 class-2"]
				<br>
				[setmoreplus link class="class-1 class-2"]
			</td>
		</tr>
		<tr>
			<td><p><?php _e( 'The width and height shortcode attributes were removed.<br>Use the settings instead.', 'setmore-plus' ); ?></p></td>
			<td class="code">[setmoreplus <span style="text-decoration: line-through;">width="800" height="650"</span>]</td>
		</tr>
		</tbody>
	</table>

</div>
