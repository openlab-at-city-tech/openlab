<?php
include_once('afg_libs.php');

function afg_delete_users_header() {
    $params = array(
        'users' => json_encode(get_option('afg_users')),
    );
    wp_enqueue_script('delete-users-script');
    wp_localize_script('delete-users-script', 'genparams', $params);
}

function afg_add_users() {
    $users = get_option('afg_users');
    $default_user = get_option('afg_user_id');
    ?>
<div class='wrap'>
<h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Add Gallery | Awesome Flickr Gallery</h2>

<?php
    if ($_POST) {
        $users = get_option('afg_users');
        if ($_POST['submit'] == 'Add User') {
            if ($_POST['afg_new_user_id']) {
                $users[$_POST['afg_new_user_id']] = $_POST['afg_new_user_name'];

                update_option('afg_users', $users);
?>
        <div class="updated"><p><strong>User Added Successfully.</strong>
        </p></div>
<?php
            }
        }
        $modified = false;
        if ($_POST['submit'] == 'Delete Selected Users') {
            foreach($users as $uid => $uname) {
                if ($_POST['delete_user_' . $uid] == 'on') {
                    unset($users[$uid]);
                    $modified = true;
                }
            }
            if ($modified) {
                update_option('afg_users', $users);
?>
        <div class="updated"><p><strong>Users Deleted Successfully.</strong>
        </p></div>
<?php
            }
        }
    }

    echo afg_generate_version_line();
    $url=$_SERVER['REQUEST_URI'];
    $users = get_option('afg_users');
?>

<form method='post' action='<?php echo $url ?>'>
<div class="postbox-container" style="width:70%;">
<div id="poststuff">
<div class="postbox">
    <h3>Add New User</h3>
    <table class='form-table'>
        <tr valign='top'>
        <th scope='row'>User ID</th>
        <td width='20%'><input maxlength='30' type='text' id='afg_new_user_id' name='afg_new_user_id'/><font size='3' color='red'>*</font></td>
        <td><font size='2'>Don't know your Flickr Usesr ID?  Get it from <a href="http://idgettr.com/" target='blank'>here.</a></font></td>
        </tr>

        <tr valign='top'>
        <th scope='row'>User Name</th>
        <td><input maxlength='30' type='text' id='afg_new_user_name' name='afg_new_user_name' value="" /></td>
        <td><font size='2'>This is NOT your Flickr username.  This is just for your reference to easily cofigure settings for the plugin.  You can write any name in this field.</font></td>
        </tr>
    </table>
</div>
<?php
?>
<input type="submit" name="submit" id="afg_add_users" class="button-primary" value="Add User" />
<br><br>
<div class="postbox">
    <h3>Saved Users</h3>
    <table class='form-table' style='margin-top:0'>
    <tr style='border:1px solid Gainsboro' valign='top'>
    <th cope='row'><input type='checkbox' name='delete_all_users' id='delete_all_users'
        onclick="CheckAllDeleteUsers()"/></th>
    <th scope='row'><strong>User ID</strong></th>
    <th scope='row'><strong>User Name</strong></th>
    </tr>
    <?php
    $users = get_option('afg_users');
    foreach($users as $uid => $uname) {
        if ($uid) {
            echo "<tr style='border:1px solid Gainsboro' valign='top'>";
            echo "<td style='width:4%'><input type='checkbox' name='delete_user_$uid' id='delete_user_$uid' /></td>";
            echo "<td style='width:12%'>{$uid}</td>";
            echo "<th style='width:22%'>{$uname}</th>";
            echo "</tr>";
        }
    }
    ?>
</table>
</div>
<input type="submit" name="submit" class="button" value="Delete Selected Users" />
</div></div>
<div class="postbox-container" style="width: 29%;">
<?php
    echo afg_donate_box();
?>
</div>
</form>
<?php
}
