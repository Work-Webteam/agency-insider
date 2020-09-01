<?php
namespace Drupal\insider_events;

use Liliumdev\ICalendar\ZCiCal;
use Liliumdev\ICalendar\ZCiCalDataNode;
use Liliumdev\ICalendar\ZCiCalNode;
use Liliumdev\ICalendar\ZCTimeZoneHelper;
use Liliumdev\ICalendar\ZDateHelper;

class iCalendarBuilder {

  protected $this_event;
  protected $iCal;
  protected $iCal_file;
  protected $iCal_url;
  protected $is_dir;
  protected $timestamp;
  protected $node_id;

  public function __construct($event){
    $this->this_event = $this->setEvent($event);
    $this->node_id = $this->setNid();
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
    // Create event within ical obj.
    $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
    // Set our timezone.
    // add the title.
    $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $event['event_title']));
    // Start date/time.
    // Not sure if Uniq is a typo and will be fixed in any future updates.
    // We must add the Z to the end to let it know this is a UTC timestamp - or else it assumes local.
    $eventobj->addNode(new ZCiCalDataNode('DTSTART:' . ZDateHelper::fromUniqDateTimetoiCal($event['start_datetime']) . 'Z'));
    // End date/time.
    $eventobj->addNode(new ZCiCalDataNode('DTEND:' . ZDateHelper::fromUniqDateTimetoiCal($event['end_datetime']) . 'Z'));

    // Create a unique UID for this. UID is required.
    $uid = date('Y-m-d-H-i-s') .  "@bcpsa.gww.gov.bc.ca";
    $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
    // DTSTAMP is required.
    $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZDateHelper::fromUniqDateTimetoiCal(time())));
    // Description.
    $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
        $event['description']
      )));

    // write iCalendar feed to stdout
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
   * @return mixed
   *   Returns the iCal array/object that will be saved as a file.
   */
  public function getIcalFile() {
    return $this->iCal_file;
  }

  /**
   * @return mixed
   *   Return the NID for the event - mostly used to sort directories.
   */
  private function setNid() {
    $event = $this->getEvent();
    return $event['event_nid'];
  }

}
