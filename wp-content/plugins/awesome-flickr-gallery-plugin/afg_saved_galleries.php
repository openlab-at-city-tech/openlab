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
         <div id="afg-wrap">
            <div id="afg-main-box">
                  <h3>Saved Galleries</h3>
                  <table class='wp-list-table widefat fixed pages afg-settings-box'>
                     <tr style="border:1px solid gray">
                        <th style='width:5%'><input type='checkbox' name='delete_all_galleries' id='delete_all_galleries'
                           onclick="CheckAllDeleteGalleries()"/></th>
                        <th style='width:12%'><strong>Gallery ID</strong></th>
                        <th style='width:20%'><strong>Gallery Name</strong></th>
                        <th style='width:20%'><strong>Gallery Code</strong></th>
                        <th style='width:43%'><strong>Description</strong></th>
                     </tr>
<?php
    $row_count = 0;    
    $galleries = get_option('afg_galleries');
    foreach($galleries as $id => $ginfo) {
        if ($row_count % 2 == 0) {
            echo "<tr class='afg-saved-alternate'>";
        }
        else {
            echo "<tr>";
        }
        $row_count++;
        if ($id)
            echo "<td><input type='checkbox' name='delete_gallery_$id' id='delete_gallery_$id' /></td>";
        else
            echo "<td></td>";
        echo "<td>{$id}</td>";
        if ($id) {
            echo "<td>
                <a href=\"{$_SERVER['PHP_SELF']}?page=afg_edit_galleries_page&gallery_id=$id\" title='Edit this gallery'>
        {$ginfo['name']}</a></td>";
            echo "<td style='color:steelblue;' onfocus='this.select()'>[AFG_gallery id='$id']</td>";
        }
        else {
            echo "<td>{$ginfo['name']}</td>";
            echo "<td style='color:steelblue;' onfocus='this.select()'>[AFG_gallery]</td>";
        }
        echo "<td>{$ginfo['gallery_descr']}</td>";
        echo "</tr>";
    }
?>
                  </table>
            <input style='margin-top:15px' type="submit" name="submit" class="button" value="Delete Selected Galleries" />
         </div>
             <div id="afg-side-box">
            <?php echo afg_usage_box('the Gallery Code');
    echo afg_donate_box();
    echo afg_share_box();
 ?>
         </div>
             </div>
      </form>
<?php
}
