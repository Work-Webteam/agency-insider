/**
 * @file
 * JavaScript behaviors for CKeditor textarea boxes.
 *   We want description to show after label.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Move description handler.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.insiderTextFormatFix = {
    attach: function (context) {
      $('div.js-text-format-wrapper.text-format-wrapper.js-form-item.form-item .js-form-type-textarea .form-textarea-wrapper', context).once('moveDescription').each(function () {
        // For each ckeditor block, locate the closest description div and append it below the label.
        $('.js-text-format-wrapper.text-format-wrapper.js-form-item.form-item > .description').each(function () {
          $(this).siblings('fieldset').children('.form-textarea-wrapper').prepend($(this));
        });
      });
    }
  };

})(jQuery, Drupal);
