/* GoFly Flight Deals — Admin JS */
(function ($) {
  'use strict';

  // Datepicker init.
  $(document).ready(function () {
    $('.gfd-datepicker').datepicker({
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
    });

    // IATA uppercase enforcement.
    $(document).on('input', '.gfd-iata', function () {
      this.value = this.value.toUpperCase();
    });

    // Media uploader for airline logo.
    var mediaUploader;
    $(document).on('click', '.gfd-upload-image', function (e) {
      e.preventDefault();
      var $btn     = $(this);
      var targetId = $btn.data('target');

      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      mediaUploader = wp.media({
        title:    'Select Airline Logo',
        button:   { text: 'Use this image' },
        multiple: false,
        library:  { type: 'image' },
      });

      mediaUploader.on('select', function () {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#' + targetId).val(attachment.id);
        var $wrap = $btn.closest('.gfd-image-upload');
        $wrap.find('.gfd-image-preview').html('<img src="' + attachment.url + '" />');
        if (!$wrap.find('.gfd-remove-image').length) {
          $btn.after('<button type="button" class="button gfd-remove-image">Remove</button>');
        }
      });

      mediaUploader.open();
    });

    $(document).on('click', '.gfd-remove-image', function (e) {
      e.preventDefault();
      var $wrap = $(this).closest('.gfd-image-upload');
      $wrap.find('input[type="hidden"]').val('');
      $wrap.find('.gfd-image-preview').html('');
      $(this).remove();
      mediaUploader = null;
    });
  });
})(jQuery);
