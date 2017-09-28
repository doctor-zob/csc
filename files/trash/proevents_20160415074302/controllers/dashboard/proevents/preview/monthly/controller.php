<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsPreviewMonthlyController extends Controller {

	public function view(){
		$this->set('controller',$this);
		$eventify = Loader::helper('eventify','proevents');
		$this->set('eventify',$eventify);
	}
	
	public function on_page_view() {
		Loader::model('event_list','proevents');
		$this->loadeventSections();
		$eventList = new EventList();
		$sections = $this->get('sections');
		$keys = array_keys($sections);
		$keys[] = -1;
		$eventList->filterByParentID($keys);
		$event_ids = array();
		if(count($eventList->getPage())>0){
			foreach($eventList->getPage() as $event){
				$event_ids[] = $event->cID;
			}
		}
	}
	
	protected function loadeventSections() {
		$eventSectionList = new PageList();
		$eventSectionList->setItemsPerPage($this->num);
		$eventSectionList->filterByEventSection(1);
		$eventSectionList->sortBy('cvName', 'asc');
		$tmpSections = $eventSectionList->get();
		$sections = array();
		foreach($tmpSections as $_c) {
			$sections[$_c->getCollectionID()] = $_c->getCollectionName();
		}
		$this->set('sections', $sections);
	}
	
}