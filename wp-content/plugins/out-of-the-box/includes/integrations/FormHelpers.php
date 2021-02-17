<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class FormHelpers
{
    public function __construct()
    {
        add_filter('outofthebox_render_formfield_data', [&$this, 'render_form_value'], 10, 3);
    }

    public function render_form_value($data, $ashtml, $formclass)
    {
        $uploaded_files = json_decode($data);

        if (empty($uploaded_files) || (0 === count((array) $uploaded_files))) {
            return $data;
        }

        // Get folder information based on first uploaded file
        $first_entry = current($uploaded_files);
        $folder_name = (in_array(dirname($first_entry->path), ['\\', '/']) ? 'Dropbox' : dirname($first_entry->path));
        $folder_location = ($ashtml && isset($first_entry->folderurl)) ? '<a href="'.urldecode($first_entry->folderurl).'">'.$folder_name.'</a>' : $folder_name;

        // Fill our custom field with the details of our upload session
        $formated_value = sprintf(__('%d file(s) uploaded to %s:', 'wpcloudplugins'), count((array) $uploaded_files), $folder_location);

        if (!$ashtml) {
            // Render TEXT only
            $formated_value .= "\r\n";
            foreach ($uploaded_files as $fileid => $file) {
                $formated_value .= basename($file->path).' ('.$file->size.")\r\n";
            }

            return $formated_value;
        }

        // Render HTML
        ob_start();
        $current = 0;

        echo $formated_value; ?><table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;line-height:22px;table-layout:auto;width:100%;">
            <?php foreach ($uploaded_files as $fileid => $file) {            ?>
                <tr style="<?php echo ($current % 2) ? 'background: #fafafa;' : ''; ?> height: 26px;">
                    <td style="width:20px;padding-right:10px;padding-left: 5px;">
                        <img alt="" height="16" src="<?php echo \TheLion\OutoftheBox\Helpers::get_default_icon($file->type, false); ?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="16">
                    </td>
                    <td style="padding-right:10px;">
                        <a href="<?php echo urldecode($file->link); ?>" target="_blank"><?php echo basename($file->path).' ('.$file->size.')'; ?></a>
                        <?php echo (isset($file->description) && empty(!$file->description)) ? '<br/><div style="font-weight:normal; max-height: 200px; overflow-y: auto;word-break: break-word;">'.nl2br($file->description).'</div>' : ''; ?>
                    </td>
                </tr>
        <?php ++$current;
        } ?>
            </table><?php

          //Remove any newlines
        return trim(preg_replace('/\s+/', ' ', ob_get_clean()));
    }
}
