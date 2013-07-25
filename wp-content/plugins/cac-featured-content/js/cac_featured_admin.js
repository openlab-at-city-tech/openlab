/**
 * The document.ready() function is responsible for setting up all our
 * event bindings and running the initial call to init()
 */
jQuery(document).ready(function() {

  // cache some useful variables to make scripting a little easier
  var $dummyWidget = jQuery('div[id*="cac_featured_content_widget-__i__"]');
  var $dummyCheckbox = jQuery('.cfcw_checkbox', $dummyWidget);
  var $dummyPostInput = jQuery('.featured_post', $dummyWidget);

  /**
   * We need the dummy (__i__) widget to make sure the "display images" checkbox
   * is checked by default, the post name input is disabled by default and we need
   * it to run the init() function when a new widget is created.
   */

  // make sure the dummy widget's "display images" checkbox is checked by default
  $dummyCheckbox.attr('checked', 'checked');

  // make sure the dummy widget's post input is disabled by default
  $dummyPostInput.attr('disabled', true);

  // run init() on dragstop event (new widget creation)
  $dummyWidget.bind('dragstop', function() {
    cacFeaturedContent.init();
  });

  /**
   * Because widgets are created and updated thru ajax it's difficult to know how
   * many widgets exist or when one is being created/updated while we're in the
   * document.ready() function. So instead we will delegate some actions to the
   * widgets sidebar that will respond to updates and changes to the content type.
   */

  // delegate the ajaxComplete event so we can run init() after a widget is submitted
  jQuery('#widgets-right').on('ajaxComplete', function(event) {
    cacFeaturedContent.init();
  });

  // delegate the select box change event so we can hide/show the appropriate input(s)
  jQuery('#widgets-right').on('change', '.featured_select', function(event) {
    // only call typeChange() if we're getting an event from our widget
    if (event.target.id.match(/cac_featured_content/)) {
      cacFeaturedContent.typeChange(jQuery(this).closest('div.widget'));
    }
  });

  // delegate a focusout event to the blog name input. If featured post has been selected
  // then call click on the widget's submit button. This ensures the blog name is stored
  // in the DB when the user tries to autocomplete a post name.
  jQuery('#widgets-right').on('focusout', '.featured_blog', function() {
    // only call click() if we're getting an event from our widget
    if (event.target.id.match(/cac_featured_content/)) {
      var $widget = jQuery(this).closest('div.widget');
      if ($widget.find('.featured_select').val() == 'post') {
        setTimeout(function() { $widget.find(':submit').click(); }, 500);
      }
    }
  });

  // delegate a click event to the display images checkbox to ensure that the image inputs
  // are properly hidden or shown when the checkbox changes state
  jQuery('#widgets-right').on('click', '.cfcw_checkbox', function(event) {
    // only toggleImageInputs() if we're getting an event from our widget
    if (event.target.id.match(/cac_featured_content/)) {
      var $widget = jQuery(this).closest('div.widget');
      cacFeaturedContent.toggleImageInputs($widget);
    }
  });

  // initialize all our widgets on initial page load
  cacFeaturedContent.init();

});

/*
 * The cacFeaturedContent object contains the functions used to control the
 * layout of all CAC Featured Content Widgets in the admin section.
 */
var cacFeaturedContent = {

  // this function initializes the display status of all the widgets' inputs
  init: function() {

    // gather all the widgets
    var $widgets = jQuery('[id*="_cac_featured_content_widget"]');

    // initialize all widgets except the dummy widget (we did that when the page loaded)
    $widgets.each(function() {
      
      // cache some variables to make scripting easier
      var $currentWidget = jQuery(this);
      var widgetID = $currentWidget.find('input[name="widget-id"]').val();

      if (widgetID !== "cac_featured_content_widget-__i__") {

        // just use typeChange to properly initialize the featured content inputs
        cacFeaturedContent.typeChange($currentWidget);

        // initialize 'Display Images' inputs
        cacFeaturedContent.toggleImageInputs($currentWidget);

      }

    });
  
  },

  // this function runs when the content type select box changes
  typeChange: function($widget) {

    var contentType = $widget.find('.featured_select').val();

    // start by hiding all inputs
    cacFeaturedContent.hideAll($widget);

    // initialize post name input
    cacFeaturedContent.togglePostInput($widget)

    // if contentType == post, we have to show both blog AND post fields
    if (contentType == 'post') {
      cacFeaturedContent.showSelected($widget, 'blog');
      cacFeaturedContent.showSelected($widget, contentType);
      cacFeaturedContent.initAutocomplete($widget, 'blog');
      cacFeaturedContent.initAutocomplete($widget, contentType);
    } else if (contentType == 'resource') {
      // if contentType == resource, we have to show both title AND link fields
      cacFeaturedContent.showSelected($widget, 'resource_title');
      cacFeaturedContent.showSelected($widget, 'resource_link');
    } else { // show only the specific content type input
      cacFeaturedContent.showSelected($widget, contentType);
      cacFeaturedContent.initAutocomplete($widget, contentType);
    }

  },

  // this function shows the input for the passed content type
  showSelected: function($widget, type) {
    $widget.find('[id*="featured_' + type + '"]').closest('p').show();
  },

  // this function hides the input for the passed content type
  hideAll: function($widget) {
    $widget.find('input[name*="[featured_"]').closest('p').hide();
  },

  // this function initializes autocomplete for a given content type
  initAutocomplete: function($widget, type) {
    // widget numbers are weird when you first drop a new widget in the admin
    // so we have to use a different attr when a widget is first created
    if ($widget.find('[name="multi_number"]').val()) {
      var num = $widget.find('[name="multi_number"]').val();
    } else {
      var num = $widget.find('[name="widget_number"]').val();
    }
    
    $widget.find('input.featured_' + type + '_ac').autocomplete({
      source: ajaxurl + '?action=cfcw_query_' + type + '&num=' + num,
    });
  },

  // this function checks the value of the blog input and disables/enables post input
  togglePostInput: function($widget) {
    var $postInput = $widget.find('.featured_post');
    if ($widget.find('.featured_blog').val() == '') {
      $postInput.val('').attr('disabled', true);
    } else {
      $postInput.attr('disabled', false);
    }
  },

  // this function shows or hides the 3 'p' elements that come after the display images checkbox
  toggleImageInputs: function($widget) {
    var $checkbox = $widget.find('.cfcw_checkbox');
    if ($checkbox.is(':checked')) {
      $checkbox.closest('p').next().show().next().show().next().show();
    } else {
      $checkbox.closest('p').next().hide().next().hide().next().hide();
    }
  }

}
