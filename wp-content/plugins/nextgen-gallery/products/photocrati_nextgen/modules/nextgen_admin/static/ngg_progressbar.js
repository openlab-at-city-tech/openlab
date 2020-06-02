(function($){

    $.nggProgressBar = function(options){
        var progressBar = {
            defaults: {
                starting_value: 0,
                infinite: false,
                in_progress_text: 'In progress...',
                finished_text: 'Done!'
            },

            // Initializes the progress bar
            init: function(options){

                // Set the options
                this.options = $.extend(this.defaults, options);

                // Display the sticky Gritter notification
                this.gritter_id = this.find_gritter(window).add({
                    progressBar: this,
                    sticky: true,
                    title:  this.options.title,
                    text:   "<div class='ngg_progressbar'><div></div></div>",
                });

                // Find the gritter element added
                this.find_gritter_el(window);

                // Is this an infinite progress bar?
                if (this.options.infinite) {
                    this.gritter_el.find('.ngg_progressbar').addClass('infinite');
                }

                // Set the starting value
                this.set(this.options.starting_value);
            },

            set: function(percent, text){
              // You can optionally just pass in a message, and we'll assume that it's an infinite progress bar
              // and use 100 completion, with the message as the text
              if (isNaN(percent)) {
                  text = percent;
                  percent = 100;
              }
              percent = percent + "%";

                // You can set the percentage of completion, as well as the text message to appear
                if (typeof(text) == 'undefined') text = percent;

              this.status_el.animate({
                  width: percent
              }).text(text);
            },

            // Closes the progress bar
            close: function(delay){
                if (typeof(delay) == 'undefined') delay = 1000;
                var gritter     = this.find_gritter(window);
                var gritter_id  = this.gritter_id;
                setTimeout(function(){
                    gritter.remove(gritter_id);
                }, delay);
            },

            // Finds the parent window
            find_parent: function(win){
                var retval = win;
                try {
                    while (retval.document !== retval.parent.document) retval = retval.parent;
                }
                catch (ex){
                    if (typeof(console) != "undefined") console.log(ex);
                }
                return retval;
            },

            // Finds the gritter library
            find_gritter: function(win){
               return this.find_parent(win).jQuery.gritter
            },


            // Finds the gritter element
            find_gritter_el: function(win){
                var selector = '#gritter-item-'+this.gritter_id;
                this.gritter_el = $(selector);
                if (this.gritter_el.length == 0) {
                    this.gritter_el = this.find_parent(win).jQuery(selector);
                }

                this.status_el = this.gritter_el.find('.ngg_progressbar:first div');
                this.gritter_el.data('nggProgressBar', this);

                return this.gritter_el;
            }
        };

        progressBar.init(options);

        return progressBar;
    };

})(jQuery);