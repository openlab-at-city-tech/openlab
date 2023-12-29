/**
 * Javascript to help the Name Directory in the WordPress admin
 * This file is part of the Name Directory plugin for WordPress
 */


/**
 * Export an HTML table to CSV
 * From: https://jsfiddle.net/mnsinger/65hqxygo/
 * @param table
 * @param filename
 */
function name_directory_exportTableToCSV(table, filename)
{
    // Get all the rows in given table which have at least one TD or TH element
    var rows = table.find('tr:has(td),tr:has(th)');

    // Temporary delimiter characters unlikely to be typed by keyboard
    // This is to avoid accidentally splitting the actual contents
    var tmpColDelim = String.fromCharCode(11); // vertical tab character
    var tmpRowDelim = String.fromCharCode(0); // null character

    var colDelim = '","';  // Column Delimiter
    var rowDelim = '"\r\n"'; // Row Delimiter

    // Grab text from table and map it into the CSV formatted string
    var csv = '"' + rows.map(function(i, row)
    {
        var row = jQuery(row), cols = row.find('td,th');

        return cols.map(function(j, col)
        {
            var col = jQuery(col), text = col.text();

            return text.replace(/"/g, '""'); // escape double quotes

        }).get().join(tmpColDelim);

    }).get().join(tmpRowDelim).split(tmpRowDelim).join(rowDelim).split(tmpColDelim).join(colDelim) + '"';

    // Data URI
    var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

    if (window.navigator.msSaveBlob)  // IE 10+
    {
        window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "name_directory_export.csv")
    }
    else
    {
        jQuery(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' });
    }
}

/* Save a named preference to a cookie */
function name_directory_savePreference(name, value)
{
    var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

/* Read the named preference from cookie */
function name_directory_readPreference(name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for(var i=0;i < ca.length;i++)
    {
        var c = ca[i];
        while (c.charAt(0)==" ") c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/**
 * All actions combined
 */
jQuery(document).ready(function()
{
    jQuery('.toggle-info').on('click', function(event)
    {
        event.preventDefault();
        var toggle_id = jQuery(this).attr('data-id');
        jQuery('#embed_code_' + toggle_id).toggle();
        return false;
    });

    if(jQuery(".name_directory_import_page #import-upload-form").length > 0)
    {
        jQuery(".name_directory_import_page #import-upload-form").prepend("<p><input type='checkbox' name='use_utf8_import' value='1' id='use_utf8_import'> <label for='use_utf8_import'>" + name_directory_translation.use_utf8_import + "</label></p>");
        jQuery(".name_directory_import_page #import-upload-form").prepend("<p><input type='checkbox' name='empty_dir_on_import' value='1' id='empty_on_import'> <label for='empty_on_import'>" + name_directory_translation.empty_directory_on_import + "</label></p>");
    }

    jQuery("#input_compact").on("click", function(e)
    {
        jQuery("#published_yes").attr("checked", "checked");
        jQuery("#add_description, #add_published, #add_submitter").hide();
        name_directory_savePreference("wp-plugin-nd-add_form", "compact");
    });

    jQuery("#input_extensive").on("click", function(e)
    {
        jQuery("#add_description, #add_published, #add_submitter").show();
        name_directory_savePreference("wp-plugin-nd-add_form", "extensive");
    });

    jQuery('.namedirectory_confirmdelete').click(function()
    {
        var delete_directory = confirm(name_directory_translation.delete_question);
        if(delete_directory == false)
        {
            return false;
        }
    });

    var pref = name_directory_readPreference("wp-plugin-nd-add_form");
    if(pref != null)
    {
        jQuery("#input_" + pref).trigger("click");
        if(! window.location.hash)
        {
            jQuery("html, body").animate({scrollTop:0}, 1);
        }
    }

    jQuery("#add_form_ajax_submit").val("name_directory_ajax_names");

    jQuery("#add_name_ajax").on("submit", function(e)
    {
        if(typeof tinyMCE !== "undefined")
        {
            tinyMCE.triggerSave();
        }

        var form_data = jQuery(this).serialize();

        e.preventDefault();

        jQuery("#add_button").attr("disabled", "disabled");

        jQuery.ajax({
            url: "admin-ajax.php",
            type: "POST",
            data: form_data,
            success: function(data)
            {
                window.scroll(0,0);
                jQuery("#add_result").addClass("updated").slideDown().html(data);
                jQuery("#add_name_ajax input[type=text], #add_name_ajax textarea, #edit_name_id").val("");

                jQuery("#edit_name_id").remove();
                jQuery("#add_name input").val("");
                jQuery("#add_description input").val("");
                jQuery("#add_submitter input").val("");
                if(typeof tinyMCE !== "undefined")
                {
                    tinyMCE.activeEditor.setContent('');
                }

            },
            error: function(data)
            {
                window.location.reload();
            },
            complete: function(data)
            {
                jQuery("#add_button").removeAttr("disabled");
            }
        });

        return false;
    });

    jQuery(".toggle_published").on("click", function(e)
    {
        var name_id = jQuery(this).attr("data-nameid");
        var update_ref = jQuery(this).attr("id");

        jQuery.ajax({
            url: "admin-ajax.php",
            type: "POST",
            data: { action: "name_directory_switch_name_published_status", name_id: name_id }
        }).done(function(status)
        {
            jQuery("#" + update_ref).prop("checked", parseInt(status));
        });
    });

    // This must be an a-element hyperlink, so it can download 'data:'-uri's
    jQuery("#export_name_directory_names_button").on('click', function (event)
    {
        name_directory_exportTableToCSV.apply(this, [jQuery('#export_names'), 'name_directory_export.csv']);
    });
});
