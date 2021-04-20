<?php

namespace Drupal\insider_suggestion_form_handler\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Create an anonymous webform submission.
 *
 * @WebformHandler(
 *   id = "Save as anonymous",
 *   label = @Translation("Save as anonymous"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Saves webform with no identifying information"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class SaveAsAnonymousHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // On submit, remove user association before we save -
    // so there is no record of who submitted this.
    $webform_submission->setOwnerId("0");
    // $webform_submission->save();
    return TRUE;
  }

}
