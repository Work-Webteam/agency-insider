<?php
namespace Drupal\insider_events;

use DateTime;
use Drupal;
use Drupal\Core\File\FileSystemInterface;
use Exception;
use RuntimeException;

/**
 * Class InsiderEventsIcalFile
 * @package Drupal\insider_events
 */
class InsiderEventsIcalFile {
  protected $iCal_file;
  protected $iCal_url;
  protected $is_dir;
  protected $timestamp;
  protected $node_id;
  protected $this_event;

  /**
   * InsiderEventsIcalFile constructor.
   * @param $iCal
   * @param $event
   */
  public function __construct($iCal, $event){
    $this->iCal_file = $iCal;
    $this->timestamp = $this->setTimestamp();
    $this->this_event = $event;
    $this->node_id = $this->setNid();
    $this->create_directory();
    $this->saveIcal();
  }

  /**
   * @param $url
   *   The variable that we will pass back to the event variable so we can create a download link.
   */
  private function setIcalUrl($url) {
    $this->iCal_url = $url;
  }

  /**
   * @return mixed
   *   Return the NID for the event - mostly used to sort directories.
   */
  private function setNid() {
    $event = $this->getEvent();
    return $event['event_nid'];
  }


  private function getIcalFile(){
    return $this->iCal_file;
  }

  /**
   * @return int
   */
  private function setTimestamp() {
    $timestamp = new DateTime();
    return $timestamp->getTimestamp();
  }

  /**
   * @return int
   */
  protected function getTimestamp() {
    return $this->timestamp;
  }

  /**
   * @return mixed
   *   Getter for the URL of the iCal file.
   */
  public function getIcalUrl() {
    return $this->iCal_url;
  }

  /**
   * Getter for any field we have set in the event object.
   * @param null $field
   *  The string indicating which field is requested. If no match, then return null.
   * @return mixed|null
   *  Return the matching event object field - if no match then return null.
   */
  protected function getEventField($field = null) {
    $event = $this->getEvent();
    return isset($event[$field]) ? $event[$field] : null;
  }

  /**
   * @return mixed
   *   Return the event array for parsing in various methods.
   */
  private function getEvent() {
    return $this->this_event;
  }

  /**
   *  Getter for Nid field.
   */
  private function getNid() {
    return $this->node_id;
  }

  /**
   * Helper function to save the iCal object as an iCal file.
   */
  private function saveIcal() {
    // create a directory if it does not exist
    $this->create_directory();
    if($this->is_dir){
      $this->setIcalUrl(file_save_data($this->getIcalFile(), 'public://ical/' . $this->getNid() . '/' . str_replace(' ', '', $this->getEventField('event_title')) . '.ics', FileSystemInterface::EXISTS_REPLACE));
    }
  }

  /**
   * Helper function to help create the directory for the iCal file, based on node id.
   */
  private function create_directory() {
    // We don't want multiple icals for every event- just one, so use node_id to separate these.
    $new_dir = 'public://ical/' . $this->getNid();

    Try{
      $this->is_dir = Drupal::service('file_system')->prepareDirectory($new_dir, FileSystemInterface::CREATE_DIRECTORY);
      if (!$this->is_dir) {
        throw new RuntimeException("Could not create a directory for the file.");
      }
    }
    catch (Exception $e) {
      // Generic exception handling if something else gets thrown.
      Drupal::logger('insider_event')->error($e->getMessage());
    }

  }
}
