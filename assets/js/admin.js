/* global jQuery, wp, mpqAdmin */
jQuery(function ($) {
  'use strict';

  var mediaFrame;

  // Open media uploader
  $(document).on('click', '.mpq-media-btn', function (e) {
    e.preventDefault();
    var btn      = $(this);
    var targetId  = btn.data('target-id');
    var targetUrl = btn.data('target-url');
    var previewId = btn.data('preview');

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

      // Show/enable a Remove button if not already there
      if (!btn.next('.mpq-media-clear').length) {
        btn.after(
          $('<button type="button" class="button mpq-media-clear">Remove</button>')
            .data('target-id', targetId)
            .data('target-url', targetUrl)
            .data('preview', previewId)
        );
      }
    });

    mediaFrame.open();
  });

  // Remove image
  $(document).on('click', '.mpq-media-clear', function (e) {
    e.preventDefault();
    var btn       = $(this);
    var targetId  = btn.data('target-id');
    var targetUrl = btn.data('target-url');
    var previewId = btn.data('preview');

    $('#' + targetId).val('');
    $('#' + targetUrl).val('');
    $('#' + previewId).removeClass('has-image').html('');
    btn.remove();
  });
});
