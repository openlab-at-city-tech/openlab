<?php
include_once('afg_libs.php');

function afg_view_delete_galleries_header() {
    $params = array(
        'galleries' => json_encode(get_option('afg_galleries')),
    );
    wp_enqueue_script('view-delete-galleries-script');
    wp_localize_script('view-delete-galleries-script', 'genparams', $params);
}

function afg_view_delete_galleries() {
?>
<div class='wrap'>
<h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Saved Galleries | Awesome Flickr Gallery</h2>

<?php
        if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Selected Galleries') {
            $galleries = get_option('afg_galleries');
            foreach($galleries as $id => $ginfo) {
                if ($id) {
                    if (isset($_POST['delete_gallery_' . $id]) && $_POST['delete_gallery_' . $id] == 'on') {
                        unset($galleries[$id]);
                    }
                }
            }
            update_option('afg_galleries', $galleries);
?>
    <div class="updated"><p><strong><?php echo 'Galleries deleted successfully.' ?></strong></p></div> <?php
        }
    echo afg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
?>

      <form onsubmit="return verifySelectedGalleries()" method='post' action='<?php echo $url ?>'>
         <div class="postbox-container" style="width:69%; margin-right:1%">
            <div id="poststuff">
               <div class="postbox" style='box-shadow:0 0 2px'>
                  <h3>Saved Galleries</h3>
                  <table class='form-table' style='margin-top:0'>
                     <tr style='border:1px solid Gainsboro' valign='top'>
                        <th cope='row'><input type='checkbox' name='delete_all_galleries' id='delete_all_galleries'
                           onclick="CheckAllDeleteGalleries()"/></th>
                        <th scope='row'><strong>ID</strong></th>
                        <th scope='row'><strong>Name</strong></th>
                        <th scope='row'><strong>Gallery Code</strong></th>
                        <th scope='row'><strong>Description</strong></th>
                     </tr>
<?php
    $galleries = get_option('afg_galleries');
    foreach($galleries as $id => $ginfo) {
        echo "<tr style='border:1px solid Gainsboro' valign='top'>";
        if ($id)
            echo "<td style='width:4%'><input type='checkbox' name='delete_gallery_$id' id='delete_gallery_$id' /></td>";
        else
            echo "<td style='width:4%'></td>";
        echo "<td style='width:12%'>{$id}</td>";
        if ($id) {
            echo "<th style='width:22%'>
                <a href=\"{$_SERVER['PHP_SELF']}?page=afg_edit_galleries_page&gallery_id=$id\" title='Edit this gallery'>
        {$ginfo['name']}</a></th>";
            echo "<td style='width:22%; color:steelblue; font-size:110%;' onfocus='this.select()'>[AFG_gallery id='$id']</td>";
        }
        else {
            echo "<th style='width:22%'>{$ginfo['name']}</th>";
            echo "<td style='width:22%; color:steelblue; font-size:110%;' onfocus='this.select()'>[AFG_gallery]</td>";
        }
        echo "<td>{$ginfo['gallery_descr']}</td>";
        echo "</tr>";
    }
?>
                  </table>
            </div></div>
            <input type="submit" name="submit" class="button" value="Delete Selected Galleries" />
         </div>
         <div class="postbox-container" style="width: 29%;">
            <?php echo afg_usage_box('the Gallery Code');
    echo afg_donate_box();
    echo afg_share_box();
 ?>
         </div>
      </form>
<?php
}
