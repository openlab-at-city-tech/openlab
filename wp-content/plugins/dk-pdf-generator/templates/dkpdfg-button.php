<?php
/**
* dkpdfg-button.php
* This template is used to display DK PDF Generator PDF Button
*
* Do not edit this template directly,
* copy this template and paste in your theme inside a directory named dkpdfg
*/
?>
<?php
	$pdfbutton_text = sanitize_option( 'dkpdfg_pdfgbutton_text', get_option( 'dkpdfg_pdfgbutton_text', 'PDF Button' ) );
	$pdfbutton_align = sanitize_option( 'dkpdfg_pdfgbutton_align', get_option( 'dkpdfg_pdfgbutton_align', 'right' ) );
?>
<div class="dkpdf-button-container" style="<?php echo apply_filters( 'dkpdf_button_container_css', '' );?> text-align:<?php echo $pdfbutton_align;?> ">
	<a id="dkpdfg-button" class="dkpdf-button" href="<?php echo esc_url( add_query_arg( 'pdfg', 'frontend' ) );?>" target="_blank"><span class="dkpdf-button-icon"><i class="fa fa-file-pdf-o"></i></span> <?php echo $pdfbutton_text;?></a>
</div>
