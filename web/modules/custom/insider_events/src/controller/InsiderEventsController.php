<?php
namespace Drupal\insider_events\Controller;
use Drupal;
use Drupal\insider_events\iCalendarBuilder;
use Drupal\insider_events\InsiderEventsIcalFile;


/**
 * Class InsiderEventsController
 * @package Drupal\insider_events\Controller
 */
class InsiderEventsController
{
  protected $ical_url;
  private $ical_save;

  /**
   * InsiderEventsController constructor.
   * @param $event
   *  $event param is an array passed from the event node. Contains:
   *    - ['description']: The body field of the node with info about the event.
   *    - ['event_title']: The title field of the node for the event.
   *    - ['event_location']: The location field for the event node. Text only.
   *    - ['event_online_link_uri']: URL location for any online meeting events.
   *    - ['event_online_link_text']: The text to show that houses the link to any online meeting.
   *    - ['event_nid']: The nid of the event node.
   *    - ['recurring']: Set only if there are more than one date for this event. An array that contains rr rules and dates.
   *    - ['ical_url']: The location of the new ical file.
   *    - ['iCal_save']: The ical save location
   */
  public function __construct($event) {
    $this->main($event);
  }

  /**
   * @param $event
   *   The event array passed from insider_events.module.
   */
  public function main($event)
  {
    $ical = new iCalendarBuilder($event);
    $ical_file = $ical->getIcal();
    $this->ical_save = new InsiderEventsIcalFile($ical_file, $event);
    $this->set_ical_url();
  }

  private function set_ical_url() {
    $this->ical_url = $this->ical_save->getIcalUrl();
  }

  public function get_ical_url() {
    return $this->ical_url;
  }
}
