/* global soliloquy_editor */
/* ==========================================================
 * editor.js
 * http://soliloquywp.com/
 *
 * This file can be used by 3rd party plugins to integrate
 * with their custom field systems. It allows the selection
 * process to be standardized so that 3rd party plugins can
 * trigger modal selection windows and receive the corresponding
 * selected data objects.
 *
 * Using this file requires three actions for the 3rd party plugin.
 *
 * 1. The media modal HTML output needs to be inserted directly
 *    after the option/dropdown/button that is to be used to
 *    trigger the modal. This can be done by placing the following
 *    code after the output (first to return, latter to echo):
 *
 *    Soliloquy_Editor::get_instance()->slider_selection_modal();
 *
 * 2. This file should be enqueued on the page where the field resides.
 *    You should add the class ".soliloquy-modal-trigger" to the
 *    option/dropdown/button that will trigger the modal. This will
 *    be used as a reference point for showing, hiding and passing data
 *    between the modal and your plugin.
 *
 * 3. Attaching to a global event that is fired once the data for the
 *    selection has been retrieved. You should listen on the document
 *    object for the "soliloquySliderModalData" event, like this:
 *
 *    $(document).on("soliloquySliderModalData", function(e){
 *        //console.log(e.slider);
 *    });
 *
 *    This will give you access to the entire array of slider data that
 *    the user has selected, including ID, title, slug and settings.
 * ==========================================================
 * Copyright 2013 Soliloquy Team.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
(function ($) {
  $(function () {
    // Close the modal window on user action.
    var soliloquy_trigger_target = (soliloquy_editor_frame = false);
    var soliloquy_append_and_hide = function (e) {
      e.preventDefault();
      $('.soliloquy-default-ui .selected').removeClass('details selected');
      $('.soliloquy-default-ui')
        .appendTo('.soliloquy-default-ui-wrapper')
        .hide();
      soliloquy_trigger_target = soliloquy_editor_frame = false;
    };

    $(document).on(
      'click',
      '.soliloquy-choose-slider, .soliloquy-modal-trigger',
      function (e) {
        e.preventDefault();

        // Store the trigger target.
        soliloquy_trigger_target = e.target;

        // Show the modal.
        soliloquy_editor_frame = true;
        $('.soliloquy-default-ui').appendTo('body').show();

        $(document).on(
          'click',
          '.media-modal-close, .media-modal-backdrop, .soliloquy-cancel-insertion',
          soliloquy_append_and_hide
        );
        $(document).on('keydown', function (e) {
          if (27 == e.keyCode && soliloquy_editor_frame) {
            soliloquy_append_and_hide(e);
          }
        });
      }
    );

    $(document).on(
      'click',
      '.soliloquy-default-ui .thumbnail, .soliloquy-default-ui .check, .soliloquy-default-ui .media-modal-icon',
      function (e) {
        e.preventDefault();
        if ($(this).parent().parent().hasClass('selected')) {
          $(this).parent().parent().removeClass('details selected');
          $('.soliloquy-insert-slider').attr('disabled', 'disabled');
        } else {
          $(this)
            .parent()
            .parent()
            .parent()
            .find('.selected')
            .removeClass('details selected');
          $(this).parent().parent().addClass('details selected');
          $('.soliloquy-insert-slider').removeAttr('disabled');
        }
      }
    );

    $(document).on('click', '.soliloquy-default-ui .check', function (e) {
      e.preventDefault();
      $(this).parent().parent().removeClass('details selected');
      $('.soliloquy-insert-slider').attr('disabled', 'disabled');
    });

    $(document).on(
      'click',
      '.soliloquy-default-ui .soliloquy-insert-slider',
      function (e) {
        e.preventDefault();

        // Either insert into an editor or make an ajax request.
        if ($(soliloquy_trigger_target).hasClass('soliloquy-choose-slider')) {
          wp.media.editor.insert(
            '[soliloquy id="' +
              $('.soliloquy-default-ui .selected').data('soliloquy-id') +
              '"]'
          );
        } else {
          // Make the ajax request.
          var req_data = {
            action: 'soliloquy_load_slider_data',
            nonce: soliloquy_metabox.load_slider,
            post_id: $('.soliloquy-default-ui:first .selected').data(
              'soliloquy-id'
            ),
          };
          $.post(
            ajaxurl,
            req_data,
            function (res) {
              // Trigger the event.
              $(document).trigger({
                type: 'soliloquySliderModalData',
                slider: res,
              });

              // Close the modal.
              soliloquy_append_and_hide(e);
            },
            'json'
          );
        }

        // Hide the modal.
        soliloquy_append_and_hide(e);
      }
    );
  });
})(jQuery);
