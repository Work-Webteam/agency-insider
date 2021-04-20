<?php


namespace Drupal\insider_suggestion_form_handler;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\insider_suggestion_form_handler\Logger\InsiderSuggestionFormHandlerLog;

/**
 * Modifies the logger.dblog service.
 *
 * @package Drupal\insider_suggestion_form_handler
 */
class InsiderSuggestionFormHandlerServiceProvider extends ServiceProviderBase {
  /**
   * {@inheritdoc }
   */
  public function alter(ContainerBuilder $container) {
    // Overrides logger.dblog service to exclude some messages that should
    // be anonymous.
    $container->getDefinition('logger.dblog')
      ->setClass(InsiderSuggestionFormHandlerLog::class);
  }

}
