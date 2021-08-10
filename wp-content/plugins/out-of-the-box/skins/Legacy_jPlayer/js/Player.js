'use strict';

var oftb_playlists = {};


(function ($) {
  'use strict';

  window.init_out_of_the_box_media_player = function (listtoken) {
    var container = document.querySelector('.media[data-token="' + listtoken + '"]');
    var extensions = container.querySelector('.jp_container').getAttribute('data-extensions');
    var mode = $(container).hasClass('audio') ? 'audio' : 'video';

    /* Load Playlist via Ajax */
    var data = {
      action: 'outofthebox-get-playlist',
      account_id: container.getAttribute('data-account-id'),
      lastFolder: container.getAttribute('data-id'),
      sort: container.getAttribute('data-sort'),
      listtoken: listtoken,
      _ajax_nonce: OutoftheBox_vars.getplaylist_nonce
    };

    jQuery.ajaxQueue({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: data,
      success: function (data) {
        var playlist = []

        $.each(data, function () {
          var track = this;

          if (mode === 'video') {
            var extension = track.extension.replace("mp4", "m4v").replace("ogg", "ogv");
          }

          if (mode === 'audio') {
            var extension = track.extension.replace("mp4", "m4a").replace("ogg", "oga");
          }
          track[extension] = track.source;

          delete track.duration;

          playlist.push(track);
        });

        if (mode === 'audio') {
          load_audio_player($(container), listtoken, playlist);
        }

        if (mode === 'video') {
          load_video_player($(container), listtoken, playlist);
        }

      },
      error: function () {

      },
      dataType: 'json'
    });
  }


  function load_audio_player(self, listtoken, playlist) {

    var extensions = self.find('.jp_container').attr('data-extensions');
    var autoplay = self.find('.jp_container').attr('data-autoplay');
    var $jPlayer = self.find('.jp-jplayer');
    var jPlayerSelector = '#' + self.find('.jp-jplayer').attr('id');
    var cssSelector = '#' + self.find('.jp-video').attr('id');

    oftb_playlists[listtoken] = new jPlayerPlaylist({
      jPlayer: jPlayerSelector,
      cssSelectorAncestor: cssSelector
    }, [], {
      playlistOptions: {
        autoPlay: (autoplay === '1' ? true : false)
      },
      swfPath: OutoftheBox_vars.js_url,
      supplied: extensions,
      solution: "html,flash",
      volume: 1,
      wmode: "window",
      errorAlerts: false,
      warningAlerts: false,
      size: {
        width: "100%",
        height: "0px"
      },
      ready: function (e) {

        oftb_playlists[listtoken].setPlaylist(playlist);

        if (!self.find(".jp-playlist").hasClass('hideonstart')) {
          self.find(".jp-playlist").slideDown("slow");
        }

        self.find(".jp-playlist-item-dl").off('click');
        self.find(".jp-playlist-item-dl").on('click', function (e) {
          e.stopPropagation();
          var href = $(this).attr('href') + '&dl=1',
            dataname = self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-title").html() +
            " - " + self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-artist").html();

          sendAnalyticsOFTB('Download', dataname);

          // Delay a few milliseconds for Tracking event
          setTimeout(function () {
            window.location = href;
          }, 300);

          return false;

        });

        animatePlaylist(listtoken);
      },
      loadstart: function (e) {

      },
      play: function (e) {
        $jPlayer.one('click', function () {
          oftb_playlists[listtoken].pause();
        });


        var dataname = self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-title").html() +
          " - " + self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-artist").html();
        sendAnalyticsOFTB('Play Music', dataname);
      }
    });

    createAudioSlider(listtoken);

    /* Block Context Menu*/
    $(self).on('contextmenu', 'video, audio, object, .jp-video-play, .jp_container', function (e) {
      return false;
    });

  }

  function load_video_player(self, listtoken, playlist) {

    var extensions = self.find('.jp_container').attr('data-extensions');
    var autoplay = self.find('.jp_container').attr('data-autoplay');
    var $jPlayer = self.find('.jp-jplayer');
    var jPlayerSelector = '#' + self.find('.jp-jplayer').attr('id');
    var cssSelector = '#' + self.find('.jp-video').attr('id');


    oftb_playlists[listtoken] = new jPlayerPlaylist({
      jPlayer: jPlayerSelector,
      cssSelectorAncestor: cssSelector
    }, [], {
      playlistOptions: {
        autoPlay: (autoplay === '1' ? true : false)
      },
      autohide: {
        full: false
      },
      swfPath: OutoftheBox_vars.js_url,
      supplied: extensions,
      solution: "html,flash",
      volume: 1,
      audioFullScreen: true,
      errorAlerts: false,
      warningAlerts: false,
      size: {
        width: "100%",
        height: "100%"
      },
      setmedia: function (e) {
        var currentItem = oftb_playlists[listtoken].currentItem()
        updateLayout(self, currentItem.width, currentItem.height);
      },
      ready: function (e) {

        oftb_playlists[listtoken].setPlaylist(playlist);

        if (!self.find(".jp-playlist").hasClass('hideonstart')) {
          self.find(".jp-playlist").slideDown("slow");
        }

        self.find(".jp-playlist-item-dl").off('click');
        self.find(".jp-playlist-item-dl").on('click', function (e) {
          e.stopPropagation();
          var href = $(this).attr('href') + '&dl=1',
            dataname = self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-title").html() +
            " - " + self.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-artist").html();

          sendAnalyticsOFTB('Download', dataname);

          // Delay a few milliseconds for Tracking event
          setTimeout(function () {
            window.location = href;
          }, 300);

          return false;

        });

        animatePlaylist(listtoken);
      },
      ended: function (e) {

      },
      pause: function (e) {
        self.find(".jp-video-play").show();
      },
      loadstart: function (e) {

      },
      loadedmetadata: function (e) {


        if (e.jPlayer.status.videoHeight !== 0 && e.jPlayer.status.videoWidth !== 0) {
          updateLayout(self, e.jPlayer.status.videoWidth, e.jPlayer.status.videoHeight)
        }

        if (e.jPlayer.status.paused) {
          self.find(".jp-video-play").show();
        }

      },
      waiting: function (e) {

      },
      resize: function (e) {
        if (e.jPlayer.status.videoHeight !== 0 && e.jPlayer.status.videoWidth !== 0) {
          updateLayout(self, e.jPlayer.status.videoWidth, e.jPlayer.status.videoHeight)
        } else {
          var currentItem = oftb_playlists[listtoken].currentItem()
          updateLayout(self, currentItem.width, currentItem.height);
        }
      },
      play: function (e) {
        self.find(".jp-video-play").hide();

        $jPlayer.one('click', function () {
          oftb_playlists[listtoken].pause();
        });

        var dataname = $jPlayer.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-title").html() +
          " - " + $jPlayer.find(".jp-playlist-item.jp-playlist-current  .jp-playlist-item-song-artist").html();
        sendAnalyticsOFTB('Play Video', dataname);

      }

    });

    createAudioSlider(listtoken);

    /* Block Context Menu*/
    $(self).on('contextmenu', 'video, audio, object, .jp-video-play, .jp_container', function (e) {
      return false;
    });

    /* Resize handler */
    $(window).resize(function () {
      if (window.jp_orgininal_width === self.width()) {
        return;
      }
      window.jp_orgininal_width = self.width();

      $('.OutoftheBox .jp-jplayer').each(function () {
        if (typeof $(this).data().jPlayer !== 'undefined') {
          var status = ($(this).data().jPlayer.status);
          if (status.videoHeight !== 0 && status.videoWidth !== 0) {
            updateLayout($(this), status.videoWidth, status.videoHeight)
          }
        }
      })
    });

    var guivisable = false;
    self.on({
      mouseenter: function () {
        self.find('.jp-gui').show();
        guivisable = true;
      },
      mouseleave: function () {
        self.find('.jp-gui').hide();
        guivisable = false;
      },
      mousemove: function () {
        if (!guivisable) {
          self.trigger('mouseenter');
        }
      }
    })
  }

  function updateLayout(self, width, height) {

    var $jPlayer = self.find('.jp-jplayer');

    setTimeout(function () {
      var ratio = $jPlayer.width() / width;
      var new_height = height * ratio;

      $jPlayer.height(new_height);
      self.find(".jp-video-play").height(new_height);
      $jPlayer.find("img").height(new_height);
    }, 100);
  }

  function createAudioSlider(listtoken) {
    var self = $('.media.video[data-token="' + listtoken + '"]');
    var $jPlayer = self.find('.jp-jplayer');

    // Create the volume slider control
    self.find(".currentVolume").slider({
      range: [0, 1],
      step: 0.01,
      start: 1,
      handles: 1,
      slide: function () {
        var value = $(this).val();
        $jPlayer.jPlayer("option", "muted", false);
        $jPlayer.jPlayer("option", "volume", value);
      }
    });

    self.find(".seekBar").slider({
      range: [0, 100],
      step: 0.01,
      start: 0,
      handles: 1,
      slide: function () {
        var value = $(this).val();
        $jPlayer.jPlayer("playHead", value);
      }

    });
  }

  function animatePlaylist(listtoken) {
    var self = $('.media[data-token="' + listtoken + '"]');
    var $jpPlaylist = self.find(".jp-playlist");
    var $jpPlaylistToggle = self.find(".jp-playlist-toggle");

    $jpPlaylistToggle.on('click', function () {
      $jpPlaylist.toggle();
    })
  }

  var ajaxQueue = $({});

  $.ajaxQueue = function (ajaxOpts) {

    var oldComplete = ajaxOpts.complete;

    ajaxQueue.queue(function (next) {

      ajaxOpts.complete = function () {
        if (oldComplete)
          oldComplete.apply(this, arguments);

        next();
      };

      $.ajax(ajaxOpts);
    });
  };
})(jQuery);