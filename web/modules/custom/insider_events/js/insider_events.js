
/**
 * @file
 * Insider events module.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * JQuery to handle events - we want to restrict them to one for now.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.insiderEvents = {
    attach: function (context, drupalSettings) {
      $(document).ready( function () {
        $("#field-when-values > tbody:last").children('tr:not(:first)').remove();
        $(".button.button--small.manage-instances").remove();
        $(".field-add-more-submit.button.js-form-submit.form-submit").remove();
      } );
    }
  };

})(jQuery, Drupal);
