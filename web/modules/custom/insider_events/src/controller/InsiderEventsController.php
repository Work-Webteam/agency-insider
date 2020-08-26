<?php
namespace Drupal\insider_events\Controller;
use Liliumdev\ICalendar\ZCiCal; // Uses the iCalendar zapcalendar library - added via composer.
use Liliumdev\ICalendar\ZCiCalDataNode;
use Liliumdev\ICalendar\ZCiCalNode;
use Liliumdev\ICalendar\ZCRecurringDate;
use Liliumdev\ICalendar\ZCTimeZoneHelper;
use Liliumdev\ICalendar\ZDateHelper;

class InsiderEventsController {
  protected $this_event;
  protected $iCal;
  protected $iCal_file;
  protected $iCal_url;
  protected $is_dir;
  protected $timestamp;

  public function __construct($event){
    $this->this_event = $this->setEvent($event);
    $this->is_dir = 0;
    $this->timestamp = date("m-d-Y");
    $this->main();
  }

  public function main() {
    // Create and ical text object.
    $this->setIcal();
    $this->saveIcal();
  }

  /**
   * Allow manual or auto setting of $event variable.
   * @param $event
   * @return mixed
   */
  public function setEvent($event) {
    return $event;
  }


  private function setIcal() {
    $event = $this->getEvent();
    // Create the object.
    $icalobj = new ZCiCal();
    // Create event within ical obj.
    $eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);
    // add the title.
    $eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $event['event_title']));
    // Start date/time.
    $eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event['start_datetime'])));
    // End date/time.
    $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event['end_datetime'])));
    // Create a unique UID for this. UID is required.
    $uid = date('Y-m-d-H-i-s') .  "@bcpsa.gww.gov.bc.ca";
    $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
    // DTSTAMP is required.
    $eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));
    // Description.
    $eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
        $event['description']
      )));

    // write iCalendar feed to stdout
    $this->iCal = $icalobj->export();

  }

  private function setIcalUrl($url) {
    $this->iCal_url = $url;
  }

  private function getEvent() {
    return $this->this_event;
  }


  public function getIcalFile() {
    return $this->iCal_file;
  }

  public function getIcalUrl() {
    return $this->iCal_url;
  }

  public function getIcal() {
    // For testing
    return $this->iCal;
  }

  private function saveIcal() {
    // create a directory if it does not exist
    $this->create_directory();
    if($this->is_dir){
      $event_name = $this->getEvent();
      $this->iCal_url = file_save_data($this->iCal, 'public://ical/' . $this->timestamp . '/' . $event_name['event_title'] . '.ics');
    }
    // Create a file name and make sure it does not exist.
    // save as an .ical file.
    // return the .ical file location.
  }

  private function create_directory() {
    $new_dir = 'public://ical/' . $this->timestamp;

    Try{
      $this->is_dir = file_prepare_directory($new_dir, FILE_CREATE_DIRECTORY);
      if (!$this->is_dir) {
        throw new \exception("Could not create a directory for the file.");
      }
    }
    catch (Exception $e) {
      // Generic exception handling if something else gets thrown.
      \Drupal::logger('insider_event')->error($e->getMessage());
    }

  }



}




