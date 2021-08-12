
(function ($) {

    $(document).ready(function () {

        // Add Color Picker to all inputs that have 'color-field' class
        $(function () {
            $('input[type="text"].colorpicker').wpColorPicker();
        });

        /*
         * CUSTOM REPLACEMENTS
         */
        $.fn.add_new_replacement_row = function () {
            var articleRow, articleRowHtml, rowId;

            rowId = $(".custom-related-article").length;
            articleRow = $('<div class="custom-related-article"></div>');
            articleRowHtml = $('<input type="text" name="cmtt_related_article_name[]" style="width: 40%" id="cmtt_related_article_name" placeholder="Name"><input type="text" name="cmtt_related_article_url[]" style="width: 50%" id="cmtt_related_article_url" placeholder="http://"><a href="#javascript" class="cmtt_related_article_remove">Remove</a>');
            articleRow.append(articleRowHtml);
            articleRow.attr('id', 'custom-related-article-' + rowId);

            $("#glossary-related-article-list").append(articleRow);
            return false;
        };

        $.fn.delete_replacement_row = function (row_id) {
            $("#custom-related-article-" + row_id).remove();
            return false;
        };

        /*
         * Added in 2.7.7 remove replacement_row
         */
        $(document).on('click', 'a.cmtt_related_article_remove', function () {
            var $this = $(this), $parent;
            $parent = $this.parents('.custom-related-article').remove();
            return false;
        });

        /*
         * Added in 2.4.9 (shows/hides the explanations to the variations/synonyms/abbreviations)
         */
        $(document).on('click showHideInit', '.cm-showhide-handle', function () {
            var $this = $(this), $parent, $content;

            $parent = $this.parent();
            $content = $this.siblings('.cm-showhide-content');

            if (!$parent.hasClass('closed'))
            {
                $content.hide();
                $parent.addClass('closed');
            }
            else
            {
                $content.show();
                $parent.removeClass('closed');
            }
        });

        $('.cm-showhide-handle').trigger('showHideInit');

        /*
         * CUSTOM REPLACEMENTS - END
         */

    });

})(jQuery);