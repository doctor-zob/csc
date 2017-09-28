<?php      
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsAddEventController extends Controller {
	

	public $helpers = array('html','form');
	
	public function on_start() {
			Loader::model('page_list');
			$eventify = Loader::helper('eventify','proevents');
			$this->set('eventify',$eventify);
			Loader::model('event_item','proevents');
			$this->error = Loader::helper('validation/error');
	}
	
	public function view() {
		$html = Loader::helper('html');
		$v = View::getInstance();
		$v->addHeaderItem($html->css('ccm.app.css'));
		$v->addHeaderItem($html->javascript('jquery.ui.js'));
		$v->addFooterItem($html->javascript('ccm.dialog.js'));
		$v->addHeaderItem($html->css('ccm.dialog.css'));
		$v->addHeaderItem($html->css('jquery.ui.css'));
		$this->setupForm();
		$this->loadeventSections();
		$eventList = new PageList();
		$eventList->sortBy('cDateAdded', 'desc');
		if (isset($_GET['cParentID']) && $_GET['cParentID'] > 0) {
			$eventList->filterByParentID($_GET['cParentID']);
		} else {
			$sections = $this->get('sections');
			$keys = array_keys($sections);
			$keys[] = -1;
			$eventList->filterByParentID($keys);
		}
	}


	protected function loadeventSections() {
		$eventSectionList = new PageList();
		$eventSectionList->filterByEventSection(1);
		$eventSectionList->sortBy('cvName', 'asc');
		$tmpSections = $eventSectionList->get();
		$sections = array();
		foreach($tmpSections as $_c) {
			$sections[$_c->getCollectionID()] = $_c->getCollectionName();
		}
		$this->set('sections', $sections);
	}


	public function edit($cID) {
		$this->setupForm();
		$event = Page::getByID($cID);
		//var_dump($this->post());
		//exit;
		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$p = Page::getByID($this->post('eventID'));
				$parent = Page::getByID($this->post('cParentID'));
				$ct = CollectionType::getByID($this->post('ctID'));				
				$dates_ak = CollectionAttributeKey::getByHandle('event_multidate');
				$dates_akID = $dates_ak->akID;
				$dates = $_POST['akID'][$dates_akID];
				$date_count = count($dates);
				
				$dates = $_REQUEST['akID'][$dates_akID];
				$dates_renumber = array();
				foreach($dates as $date_item){
					array_push($dates_renumber, $date_item);
				}
				$start_date = date('Y-m-d',strtotime($dates_renumber[1]['value_st_dt']));
				
				$data = array('ctID' =>$ct->getCollectionTypeID(), 'cDescription' => $this->post('eventDescription'), 'cName' => $this->post('eventTitle'), 'cDatePublic' => $start_date);
				$p->update($data);
				if ($p->getCollectionParentID() != $parent->getCollectionID()) {
					$p->move($parent);
				}
				$this->saveData($p);
				$event_item = new EventItemDates($p,true);
				Events::fire('on_prevents_edit', $dates);
				$this->redirect('/dashboard/proevents/list/', 'event_updated');
			}
		}
		
		$sections = $this->get('sections');
		if (in_array($event->getCollectionParentID(), array_keys($sections))) {
			$this->set('event', $event);	
		} else {
			$this->redirect('/dashboard/proevents/add_event/');
		}
	}

	protected function setupForm() {
		$this->loadeventSections();
		Loader::model("collection_types");
		$ctArray = CollectionType::getList('');
		$pageTypes = array();
		foreach($ctArray as $ct) {
			$pageTypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();		
		}
		$this->set('pageTypes', $pageTypes);
		$this->addHeaderItem(Loader::helper('html')->javascript('tiny_mce/tiny_mce.js'));
	}

	public function add() {
		$this->setupForm();
		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$parent = Page::getByID($this->post('cParentID'));
				$ct = CollectionType::getByID($this->post('ctID'));		
				$dates_ak = CollectionAttributeKey::getByHandle('event_multidate');
				$dates_akID = $dates_ak->akID;
				$dates = $_POST['akID'][$dates_akID];
				$date_count = count($dates);
				$dates = $_REQUEST['akID'][$dates_akID];
				$dates_renumber = array();
				foreach($dates as $date_item){
					array_push($dates_renumber, $date_item);
				}
				$start_date = date('Y-m-d',strtotime($dates_renumber[1]['value_st_dt']));
				$data = array('cName' => $this->post('eventTitle'), 'cDescription' => $this->post('eventDescription'), 'cDatePublic' => $start_date);
				
				$p = $parent->add($ct, $data);
				
				//save the other atts
				$this->saveData($p);
				
				//process generated dates
				$event_item = new EventItemDates($p,true);
				
				$this->redirect('/dashboard/proevents/list/', 'event_added');
			}
		}
	}


	protected function validate() {
		$vt = Loader::helper('validation/strings');
		$vn = Loader::Helper('validation/numbers');
		$dt = Loader::helper("form/date_time");
		$dth = Loader::helper('form/date_time_time','proevents');
		
		if (!$vn->integer($this->post('cParentID'))) {
			$this->error->add(t('You must choose a parent page for this event entry.'));
		}			

		if (!$vn->integer($this->post('ctID'))) {
			$this->error->add(t('You must choose a page type for this event entry.'));
		}			
		
		if (!$vt->notempty($this->post('eventTitle'))) {
			$this->error->add(t('Title is required'));
		}
		
		
		//akID['.$ctKey.'][atSelectOptionID][]
		Loader::model("attribute/categories/collection");
		$akdte = CollectionAttributeKey::getByHandle('event_thru');
		$akdteKey = $akdte->getAttributeKeyID();
		$akrc = CollectionAttributeKey::getByHandle('event_recur');
		$akrcKey = $akrc->getAttributeKeyID();
		$akct = CollectionAttributeKey::getByHandle('event_category');
		$ctKey = $akct->getAttributeKeyID();
		foreach($this->post(akID) as $key => $value){
			if($key==$ctKey){
				foreach($value as $type => $values){
					if($type=='atSelectNewOption'){
						foreach($values as $cat => $valued){
							if($valued==''){
								$this->error->add(t('Categories must have a value'));	
							}
						}
					}
				}
			}elseif($key==$akrcKey){
				foreach($value as $type => $values){
					if($type=='atSelectOptionID'){
						foreach($values as $rec => $valued){
							if($valued!=''){
								$db = Loader::db();
								$recur = $db->getone("SELECT value FROM atSelectOptions WHERE ID = $valued");
								//$recur=true;
								//$this->error->add(t('recur = '.$recur));	
							}
						}
					}
				}
			}elseif($key==$akdteKey){
				foreach($value as $type => $values){
					$endDate=$values;
					//$this->error->add(t($values.' - '.$this->post(eventDate)));
				}
			}
		}
		
		//if($endDate<=$this->post('eventDate') && $recur){
		//	$this->error->add(t('Your "End Date" value may not be earlier than or equal to your "Start Date" value whith the "Recuring" option set'));	
		//}
		$dates_ak = CollectionAttributeKey::getByHandle('event_multidate');
		$dates_akID = $dates_ak->akID;
		$dates = $_POST['akID'][$dates_akID];
		
		$date_count = count($dates);
		if($date_count == 1){
			$this->error->add(t('You must have at least one date.'));   
		}
		
		$dates_renumber = array();
		foreach($dates as $date_item){
			array_push($dates_renumber, $date_item);
		}

		list($year, $month, $day) = explode('-', $dth->getReformattedDate($dates_renumber[1]['value_st_dt']));
		$eventDateStart = array(sprintf("%02s", $month),sprintf("%02s", $day),$year);
        
		list($year, $month, $day) = explode('-', $dth->getReformattedDate($_REQUEST['akID'][$akdteKey]['value']));	
		$endDateData = array(sprintf("%02s", $month),sprintf("%02s", $day),$year);

		if($date_count >= 2){
			if($endDateData && $endDateData[2] != '' && $endDateData[0] != ''){
				$_endDate = mktime (0, 0, 0, $endDateData[0], $endDateData[1], $endDateData[2]);
			}
			//$eventDateData = explode('/', $this->post('eventDate'));
			$_eventDate = mktime(0, 0, 0, $eventDateData[0], $eventDateData[1], $eventDateData[2]);

			if( $_endDate <= $_eventDate && $recur){
		   	$this->error->add(t('Your "End Date" value may not be earlier than or equal to your "Start Date" value whith the "Recuring" option set'));   
			}
		}
		
		
		if($recur == 'daily' &&  $date_count > 1  && ($eventDateData[1] - $eventDateStart[1]) > 1){
			$this->error->add(t('Date Set\'s of more than one date may only recur by week or month.'));
		}

		if($recur == 'weekly' && ($eventDateData[1] - $eventDateStart[1])>=7){
			$this->error->add(t('You may not have a Date Set larger than 7 days recurring weekly.'));
		}
		
		if($recur == 'monthly' && ($eventDateData[1] - $eventDateStart[1])>=29){
			$this->error->add(t('You may not have a Date Set larger than 29 days recurring Monthly.'));
		}

		if (!$this->error->has()) {
			Loader::model('collection_types');
			$ct = CollectionType::getByID($this->post('ctID'));				
			$parent = Page::getByID($this->post('cParentID'));				
			$parentPermissions = new Permissions($parent);
			if (!$parentPermissions->canAddSubCollection($ct)) {
				$this->error->add(t('You do not have permission to add a page of that type to that area of the site.'));
			}
		}
		
	}
	
	
	private function saveData($p) {
		$db = Loader::db();
		
		$blocks = $p->getBlocks('Main');
		foreach($blocks as $b) {
			if($b->getBlockTypeHandle()=='content'){
				$b->deleteBlock();
			}
		}
		
		Loader::model("attribute/categories/collection");
		
		$set = AttributeSet::getByHandle('proevent_additional_attributes');
		$setAttribs = $set->getAttributeKeys();
		if($setAttribs){
			foreach ($setAttribs as $ak) {
				$aksv = CollectionAttributeKey::getByHandle($ak->akHandle);
				$aksv->saveAttributeForm($p);
			}	
		}
		
		$dth = Loader::helper('form/date_time_time','proevents');
		$evt = CollectionAttributeKey::getByHandle('event_thru');	
		$thru_date = $dth->getReformattedDate($_REQUEST['akID'][$evt->getAttributeKeyID()]['value']);
		$p->setAttribute($evt,$thru_date);
		
		$cak = CollectionAttributeKey::getByHandle('event_tag');
		$cak->saveAttributeForm($p);	
		
		$cck = CollectionAttributeKey::getByHandle('event_category');
		$cck->saveAttributeForm($p);
		
		$ccc = CollectionAttributeKey::getByHandle('category_color');
		$ccc->saveAttributeForm($p);
		
		$emdd = CollectionAttributeKey::getByHandle('event_multidate');
		$emdd->saveAttributeForm($p);
		
		$eexc = CollectionAttributeKey::getByHandle('event_exclude');
		$eexc->saveAttributeForm($p);
		
		$ead = CollectionAttributeKey::getByHandle('event_allday');
		$ead->saveAttributeForm($p);
		
		$eg = CollectionAttributeKey::getByHandle('event_grouped');
		$eg->saveAttributeForm($p);
		
		$evr = CollectionAttributeKey::getByHandle('event_recur');
		$evr->saveAttributeForm($p);
		
		$cnv = CollectionAttributeKey::getByHandle('exclude_nav');
		$cnv->saveAttributeForm($p);
		
		$ct = CollectionAttributeKey::getByHandle('thumbnail');
		$ct->saveAttributeForm($p);
		
		$cur = CollectionAttributeKey::getByHandle('event_local');
		$cur->saveAttributeForm($p);
		
		$cur = CollectionAttributeKey::getByHandle('address');
		$cur->saveAttributeForm($p);
		
		$cur = CollectionAttributeKey::getByHandle('contact_name');
		$cur->saveAttributeForm($p);
		
		$cur = CollectionAttributeKey::getByHandle('contact_email');
		$cur->saveAttributeForm($p);
		
		$cur = CollectionAttributeKey::getByHandle('event_price');
		$cur->saveAttributeForm($p);
		
		$qty = CollectionAttributeKey::getByHandle('event_qty');
		$qty->saveAttributeForm($p);
			
		$bt = BlockType::getByHandle('content');
		
		$data = array('content' => $this->post('eventBody'));			
					
		$b = $p->addBlock($bt, 'Main', $data);
		$b->setCustomTemplate('event_post');

		$p->reindex();
			
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
}