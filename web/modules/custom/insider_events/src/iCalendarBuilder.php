<?php
namespace Drupal\insider_events;

use Liliumdev\ICalendar\ZCiCal;
use Liliumdev\ICalendar\ZCiCalDataNode;
use Liliumdev\ICalendar\ZCiCalNode;
use Liliumdev\ICalendar\ZCTimeZoneHelper;
use Liliumdev\ICalendar\ZDateHelper;

/**
 * Class iCalendarBuilder
 * @package Drupal\insider_events
 */
class iCalendarBuilder {

  protected $this_event;
  protected $iCal;
  protected $iCal_file;
  protected $iCal_url;
  protected $timestamp;

  public function __construct($event){
    $this->this_event = $this->setEvent($event);
    $this->setIcal();
  }


  /**
   * Create an iCal file from event data.
   */
  private function setIcal() {
    $tzid = "America/Vancouver";
    $event = $this->getEvent();
    $start_year = gmdate('Y', $event['start_datetime']);
    $end_year = gmdate('Y', $event['end_datetime']);
    // Create the object.
    $icalobj = new ZCiCal();
    ZCTimeZoneHelper::getTZNode($start_year,$end_year,$tzid, $icalobj->curnode);
    // TODO: set up variables for re-used fields here.
    $title = $event['event_title'];
    // Timestamp
    $date_stamp = time();
    // Description.
    $desc = strip_tags($event['description']);
    // Check if there is recurring events.
    $recur_event = $this->get_recur();
    if($recur_event) {
      $i = 0;
      // Firstly, create one with our current start/end dates
      $this->createSingleEvent($icalobj, $title, $date_stamp, $desc, $event);
      // Now add all other events
      $this->createMultiEvent($icalobj, $title, $date_stamp, $desc, $event);
      // then we need to split up all event times foreach each instance.
    } else {
      // We only have a standard event available.
      $this->createSingleEvent($icalobj, $title, $date_stamp, $desc, $event);
    }
    // write iCalendar feed to stdout
    $this->iCal = $icalobj->export();
  }

  private function createSingleEvent(&$icalobj, $title, $date_stamp, $desc, $event) {
    $uid = date('Y-m-d-H-i-s') . mt_rand(0000000001, 9999999999) . "@bcpsa.gww.gov.bc.ca";
    // Create event within ical obj.
    $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
    // add the title.
    $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
    // Start date/time.
    // Not sure if Uniq is a typo and will be fixed in any future updates.
    // We must add the Z to the end to let it know this is a UTC timestamp - or else it assumes local.
    $eventobj->addNode(new ZCiCalDataNode('DTSTART:' . ZDateHelper::fromUniqDateTimetoiCal(
        $event['start_datetime']) . 'Z'
    ));
    // End date/time.
    $eventobj->addNode(new ZCiCalDataNode('DTEND:' . ZDateHelper::fromUniqDateTimetoiCal(
        $event['end_datetime']) . 'Z'
    ));

    $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
    // DTSTAMP is required.
    $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZDateHelper::fromUniqDateTimetoiCal($date_stamp)));
    // Description.
    $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent( $desc )));
  }

  private function createMultiEvent(&$icalobj, $title, $date_stamp, $desc, $event) {
    $i = 0;
    $recurring = $event['recurring'];

    // We need to add a node for each event.
    // The recur tool and smart date module do not work well together.
    foreach($recurring as $recur) {
      // Create a unique UID for this. UID is required.
      $uid = date('Y-m-d-H-i-s') . mt_rand(0000000001, 9999999999) . "@bcpsa.gww.gov.bc.ca";
      // Create event within ical obj.
      $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
      // add the title.
      $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));
      // Start date/time.
      // Not sure if Uniq is a typo and will be fixed in any future updates.
      // We must add the Z to the end to let it know this is a UTC timestamp - or else it assumes local.
      $eventobj->addNode(new ZCiCalDataNode('DTSTART:' . ZDateHelper::fromUniqDateTimetoiCal(
          $recur['value']) . 'Z'
      ));
      // End date/time.
      $eventobj->addNode(new ZCiCalDataNode('DTEND:' . ZDateHelper::fromUniqDateTimetoiCal(
          $recur['end_value']) . 'Z'
      ));
      $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
      // DTSTAMP is required.
      $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZDateHelper::fromUniqDateTimetoiCal($date_stamp)));
      // Description.
      $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent( $desc )));
    }
  }

  /**
   * Allow manual or auto setting of $event variable.
   * @param $event
   * @return mixed
   */
  protected function setEvent($event) {
    return $event;
  }


  /**
   * @return mixed
   *   Return the event array for parsing in various methods.
   */
  private function getEvent() {
    return $this->this_event;
  }

  private function get_recur() {
    return isset($this->this_event['recurring']) ? true : false;
  }

  /**
   * @return mixed
   *   Getter for the iCal object itself.
   */
  public function getIcal() {
    // For testing
    return $this->iCal;
  }
}
