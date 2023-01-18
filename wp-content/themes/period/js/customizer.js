jQuery(document).ready(function($){

    $('#accordion-section-period_display').find('.customize-section-description').appendTo('#customize-control-display_post_date');
    $('#accordion-section-period_layout').find('.customize-section-description').appendTo('#customize-control-layout');

    // set context to customizer panel outside iframe site content is in
    var panel = $('html', window.parent.document);

    addLayoutThumbnails();

    // replaces radio buttons with images
    function addLayoutThumbnails() {

        // get layout inputs
        var layoutInputs = panel.find('#customize-control-layout, #customize-control-layout_pages, #customize-control-layout_blog, #customize-control-layout_archives').find('input');

        // add the appropriate image to each label
        layoutInputs.each( function() {
            $(this).next().css('background-image', 'url("../wp-content/themes/period/assets/images/' + $(this).val() + '.png")');

            // add initial 'selected' class
            if ($(this).prop('checked')) {
                $(this).next().addClass('selected');
            }
        });

        // watch for change of inputs (layouts)
        panel.on('click', '#customize-control-layout input, #customize-control-layout_pages input, #customize-control-layout_blog input, #customize-control-layout_archives input', function () {
            addSelectedLayoutClass(layoutInputs, $(this));
        });
    }

    // add the 'selected' class when a new input is selected
    function addSelectedLayoutClass(inputs, target) {

        // remove 'selected' class from all labels
        // inputs.next().removeClass('selected');
        target.parent().parent().find('span').find('label').removeClass('selected');

        // apply 'selected' class to :checked input
        if (target.prop('checked')) {
            target.next().addClass('selected');
        }
    }
});