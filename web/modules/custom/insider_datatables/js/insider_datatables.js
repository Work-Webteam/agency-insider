
/**
 * @file
 * The Agency theme.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * JQuery Agency theme.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.dataTables = {
    attach: function (context, drupalSettings) {
      $(document).ready( function () {
        $('#agency-datatable').DataTable();
      } );
    }
  };

})(jQuery, Drupal);



