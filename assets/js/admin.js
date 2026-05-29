/* global jQuery, wp, mpqAdmin */
jQuery(function ($) {
  'use strict';

  var mediaFrame;

  // Open media uploader
  $(document).on('click', '.mpq-media-btn', function (e) {
    e.preventDefault();
    var btn        = $(this);
    var targetId   = btn.data('target-id');
    var targetUrl  = btn.data('target-url');
    var previewId  = btn.data('preview');
    var defaultSrc = btn.data('default-src') || '';

    if (mediaFrame) { mediaFrame.off('select'); }

    mediaFrame = wp.media({
      title:    mpqAdmin.mediaTitle,
      button:   { text: mpqAdmin.mediaButton },
      multiple: false,
      library:  { type: 'image' },
    });

    mediaFrame.on('select', function () {
      var att = mediaFrame.state().get('selection').first().toJSON();
      $('#' + targetId).val(att.id);
      $('#' + targetUrl).val(att.url);

      var $preview = $('#' + previewId);
      $preview.addClass('has-image').html('<img src="' + att.url + '" alt="">');
      btn.text('Change Image');

      // Add or update Restore Default button
      var $clear = btn.next('.mpq-media-clear');
      if (!$clear.length) {
        $clear = $('<button type="button" class="button mpq-media-clear">Restore Default</button>');
        btn.after($clear);
      } else {
        $clear.text('Restore Default');
      }
      $clear
        .data('target-id', targetId)
        .data('target-url', targetUrl)
        .data('preview', previewId)
        .data('default-src', defaultSrc);
    });

    mediaFrame.open();
  });

  // Restore default / remove image
  $(document).on('click', '.mpq-media-clear', function (e) {
    e.preventDefault();
    var btn        = $(this);
    var targetId   = btn.data('target-id');
    var targetUrl  = btn.data('target-url');
    var previewId  = btn.data('preview');
    var defaultSrc = btn.data('default-src') || '';

    $('#' + targetId).val('');
    $('#' + targetUrl).val('');

    var $preview = $('#' + previewId);
    if (defaultSrc) {
      $preview.addClass('has-image').html(
        '<img src="' + defaultSrc + '" alt=""><span class="mpq-default-badge">Default</span>'
      );
      btn.prev('.mpq-media-btn').text('Upload Override');
    } else {
      $preview.removeClass('has-image').html('');
      btn.prev('.mpq-media-btn').text('Upload Image');
    }
    btn.remove();
  });
});
