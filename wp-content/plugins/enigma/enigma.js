(function() {
  function Enigma() { }

  Enigma.prototype.GetValue = function(element) {
    return jQuery(element).attr('data-enigmav');
  };
  
  Enigma.prototype.GetType = function(element) {
    var type = jQuery(element).attr('data-enigmat');
    if (typeof type !== 'undefined' && type !== false) {
      return parseInt(type, 10);
    }
    return 0;
  };
  
  Enigma.prototype.Decode = function(value, type) {
    if (type === 1) {
      value = value.replace(/-/g, '\\u00');
      value = value.replace(/=/g, '\\u');
    }
    return jQuery.parseJSON('"' + value + '"');
  };
  
  Enigma.prototype.Replace = function(element) {
    var value = this.GetValue(element);
    if (!value) {
      return;
    }
    var type = this.GetType(element);
    jQuery(element).replaceWith(this.Decode(value, type));
    jQuery(document).trigger('Enigma:Changed');
  };
  
  Enigma.prototype.Clickable = function(element) {
    var thisEnigma = this;
    var text = jQuery(element).text();
    var link = jQuery("<a />", {
      href : "#",
      text : text,
      'data-enigmav' : this.GetValue(element),
      'data-enigmat' : this.GetType(element)
    });
    jQuery(element).replaceWith(link);
    jQuery(link).on('click', function() {
      thisEnigma.Replace(link);
      thisEnigma.PostGAEvent('Enigma', text, 'Click', 1);
      return false;
    });
  };
  
  Enigma.prototype.PostGAEvent = function(category, label, action, value) {
    /** global Leona */
    /** global ga */
    if (typeof Leona !== 'undefined' && Leona.analytics) {
      var aData = Leona.analytics.getCoreData();
      aData.t = 'event';
      aData.ec = category;
      aData.ea = action;
      aData.el = label;
      aData.ev = value;
      Leona.analytics.post(aData);
    } else if (typeof ga === 'function') {
      ga('send', 'event', category, action, label, value);
    }
  };
  
  Enigma.prototype.IfReplied = function(element) {
    var pattern = /comment_author_[^=]*=([^;]+);/g;
    var cookie = document.cookie;
    var nameCap = [];
    var names = {};
    while ((nameCap = pattern.exec(cookie)) !== null) {
      names[nameCap[1]] = true;
      var decodedName = decodeURIComponent(nameCap[1]);
      if (decodedName !== nameCap[1]) {
        names[decodedName] = true;
      }
    }
    if (jQuery.isEmptyObject(names)) {
      return;
    }
    var found = false;
    jQuery('*').each(function(idx, element) {
      var text = jQuery(element).text();
      if (names[text]) {
        found = true;
        return false;
      }
    });
    if (found) {
      this.Replace(element);
    }
  };
  
  Enigma.prototype.Run = function() {
    var thisEnigma = this;
    jQuery('span[id^="engimadiv"]').each(function(idx, element) {
      if (jQuery(element).attr('data-enigmad') === 'y') {
        return thisEnigma.Clickable(element);
      } else if (jQuery(element).attr('data-enigmad') === 'replied') {
        return thisEnigma.IfReplied(element);
      }
      thisEnigma.Replace(element);
    });
  };
  
  jQuery(function() {
    new Enigma().Run();
    setInterval(function() { new Enigma().Run();}, 2000);
  });
})();
