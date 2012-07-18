/**
 * Original code by Basil Goldman
 * http://blogs.microsoft.co.il/blogs/basil/archive/2008/08/21/jquery-create-jquery-plug-in-to-create-elements.aspx
 *
 * Enhancement to create input and select elements in some browsers by Rick Mans
 * http://www.internetschoon.nl/viewSingleItem/3563/Create-elements-via-DOM-and-jQuery.htm
 */

jQuery.create = function() {
    if (arguments.length == 0) return [];
    var args = arguments[0] || {}, elem = null, elements = null;
    var siblings = null;

    // In case someone passes in a null object,
    // assume that they want an empty string.
    if (args == null) args = "";
    if (args.constructor == String) {
        if (arguments.length > 1) {
            var attributes = arguments[1];
                if (attributes.constructor == String) {
                            elem = document.createTextNode(args);
                            elements = [];
                            elements.push(elem);
                            siblings =
        jQuery.create.apply(null, Array.prototype.slice.call(arguments, 1));
                            elements = elements.concat(siblings);
                            return elements;

                    } else {
                        var buggy                                     =   "No";

                        // create element
                        if(args.toUpperCase() == "INPUT" || args.toUpperCase() == "SELECT"){
                          /**
                           * try catch block for compatibility
                           * http://www.quirksmode.org/bugreports/archives/2006/01/Changing_the_type_of_an_input_field.html
                           */
                          try {
                            if (arguments[1]){
                              var attr = arguments[1];

                              var attributes                            =   "<" + args +" ";

                              for (key in attr) {
                                attributes                              +=  key +"=\""+ attr[key] +"\" ";
                              }

                              attributes                                +=  ">";
                            }

                            var elem                                    =   document.createElement(attributes);
                            var buggy                                 =   "Yep";
                          }
                          catch (element) {

                          }
                        }

                        /**
                         * For normal browsers ;).
                         */
                        if (buggy == "No") {
                            elem = document.createElement(args);

                            // Set element attributes.
                            var attributes = arguments[1];
                            for (var attr in attributes)
                                jQuery(elem).attr(attr, attributes[attr]);
                        }

                        if(arguments[2]){
                            // Add children of this element.
                            var children = arguments[2];
                            children = jQuery.create.apply(null, children);
                            jQuery(elem).append(children);
                        }

                        // If there are more siblings, render those too.
                        if (arguments.length > 3) {
                                siblings =
    jQuery.create.apply(null, Array.prototype.slice.call(arguments, 3));
                                return [elem].concat(siblings);
                        }
                        return elem;
                    }
                    // adding textnode here
            } else return document.createTextNode(args);
      } else {
              elements = [];
              elements.push(args);
              siblings =
        jQuery.create.apply(null, (Array.prototype.slice.call(arguments, 1)));
              elements = elements.concat(siblings);
              return elements;
      }
};