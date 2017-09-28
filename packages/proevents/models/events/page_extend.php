<?php     defined('C5_EXECUTE') or die("Access Denied."); 
class ProeventsPageExtend {

    public function onPageDelete($page)
    {
        //Log::addEntry(json_encode($page));
        $eventID = $page->getCollectionID();
        $db = Loader::db();
        $db->Execute("DELETE from btProEventDates where eventID = ?", array($eventID));
    }

}