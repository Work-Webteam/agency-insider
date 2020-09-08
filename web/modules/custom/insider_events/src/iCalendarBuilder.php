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

  public function __construct($event){
    $this->this_event = $this->setEvent($event);
    $this->setIcal();
  }


  /**
   * Create an iCal file from event data.
   */
  private function setIcal() {
    // Build ical object.
    $icalobj = $this->buildiCal();
    // write iCalendar feed to stdout.
    $this->iCal = $icalobj->export();
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

  /**
   * @return mixed
   *   Getter for the iCal object itself.
   */
  public function getIcal() {
    // For testing
    return $this->iCal;
  }

  /**
   * Helper function to build ical object for setter.
   * @return mixed
   */
  private function buildiCal() {
    $tzid = "America/Vancouver";
    $event = $this->getEvent();
    $start_year = gmdate('Y', $event['start_datetime']);
    $end_year = gmdate('Y', $event['end_datetime']);
    // Create the object.
    $icalobj = new ZCiCal();
    ZCTimeZoneHelper::getTZNode($start_year,$end_year,$tzid, $icalobj->curnode);
    // TODO: set up variables for re-used fields here.
    // Timestamp
    $date_stamp = time();
    $uid = date('Y-m-d-H-i-s') . mt_rand(0000000001, 9999999999) . "@bcpsa.gww.gov.bc.ca";
    // Create event within ical obj.
    $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
    // add the title.
    $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $event['event_title']));
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
    $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
        strip_tags($event['description'])
      )));
    // Recur rules.
    if(isset($event['recurring']) && !is_null($event['recurring'])) {
      $eventobj->addnode(new ZCiCalDataNode($event['recurring']));
    }
    // Location.
    if(isset($event['event_location']) && !is_null($event['event_location'])) {
      $eventobj->addnode(new ZCiCalDataNode("LOCATION:" . $event['event_location']));
    }
    // URL
    if(isset($event['event_online_link_uri']) && !is_null($event['event_online_link_uri'])) {
      $eventobj->addnode(new ZCiCalDataNode("URI:" . $event['event_online_link_uri']));
    }
    return $icalobj;
  }
}
