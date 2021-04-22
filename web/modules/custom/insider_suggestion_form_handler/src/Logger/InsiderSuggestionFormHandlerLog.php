<?php

namespace Drupal\insider_suggestion_form_handler\Logger;

use Drupal\dblog\Logger\DbLog as BaseDbLog;

/**
 * Logs events in the watchdog database table.
 *
 * This module prevents any webform logs for webform creation.
 * If we require these, we will need to turn this off.
 */
class InsiderSuggestionFormHandlerLog extends BaseDbLog {

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    if ($context['channel'] !== 'webform' && strpos($message, 'created') !== TRUE) {
      parent::log($level, $message, $context);
    }
  }

}
