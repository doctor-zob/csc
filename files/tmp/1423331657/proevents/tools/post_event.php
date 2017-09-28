<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
Loader::model('event_item','proevents');
Loader::model('page');
Loader::model('collection_types');
Loader::model("attribute/categories/collection");
Loader::model('block_types');
Loader::model('block');

	function action_add() {
		$_POST = $_REQUEST;
		

		if($_REQUEST['front_side']){
			$error = Loader::helper('validation/error');

			
			$error = validate($error);
	
			if (!$error->has()) {

				$parent = Page::getByID($_REQUEST['cParentID']);
				$ct = CollectionType::getByID($_REQUEST['ctID']);	
				$dates_ak = CollectionAttributeKey::getByHandle('event_multidate');
				$dates_akID = $dates_ak->akID;
				
				$dates = $_REQUEST['akID'][$dates_akID];
				$dates_renumber = array();
				foreach($dates as $date_item){
					array_push($dates_renumber, $date_item);
				}
				$start_date = date('Y-m-d',strtotime($dates_renumber[1]['value_st_dt']));
				
				$data = array('cName' => str_replace('\\','',$_REQUEST['eventTitle']), 'cDescription' => str_replace('\\','',$_REQUEST['eventDescription']), 'cDatePublic' => $start_date);
				if($_REQUEST['eventID']){
					$p = Page::getByID($_REQUEST['eventID']);
					$p->update($data);
					if ($p->getCollectionParentID() != $parent->getCollectionID()) {
						$p->move($parent);
					}
				}else{
					$p = $parent->add($ct, $data);	
				}
				saveData($p);
				
				$event_item = New EventItemDates($p,true);
				return array('success');
			}else{
				$errors = $error->getList();
				return $errors;
			}
		}
	}
	
	
	function validate($error) {
		$vt = Loader::helper('validation/strings');
		$vn = Loader::Helper('validation/numbers');
		$dt = Loader::helper("form/date_time");
		$dth = Loader::helper('form/date_time_time','proevents');
		//$er = Loader::helper('validation/error');
		
		if (!$vn->integer($_REQUEST['cParentID'])) {
			$error->add(t('You must choose a parent page for this event entry.'));
		}			

		if (!$vn->integer($_REQUEST['ctID'])) {
			$error->add(t('You must choose a page type for this event entry.'));
		}			
		
		if (!$vt->notempty($_REQUEST['eventTitle'])) {
			$error->add(t('Title is required'));
		}
		
		
		//akID['.$ctKey.'][atSelectOptionID][]
		Loader::model("attribute/categories/collection");
		$akdte = CollectionAttributeKey::getByHandle('event_thru');
		$akdteKey = $akdte->getAttributeKeyID();
		$akrc = CollectionAttributeKey::getByHandle('event_recur');
		$akrcKey = $akrc->getAttributeKeyID();
		$akct = CollectionAttributeKey::getByHandle('event_category');
		$ctKey = $akct->getAttributeKeyID();
		if(is_array($_REQUEST['akID'])){
			foreach($_REQUEST['akID'] as $key => $value){
				if($key==$ctKey){
					foreach($value as $type => $values){
						if($type=='atSelectNewOption'){
							foreach($values as $cat => $valued){
								if($valued==''){
									$error->add(t('Categories must have a value'));	
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
									//$error->add(t('recur = '.$recur));	
								}
							}
						}
					}
				}elseif($key==$akdteKey){
					foreach($value as $type => $values){
						$endDate=$values;
						//$error->add(t($values.' - '.$_REQUEST[eventDate)));
					}
				}
			}
		}
		

		$dates_ak = CollectionAttributeKey::getByHandle('event_multidate');
		$dates_akID = $dates_ak->akID;
		$dates = $_REQUEST['akID'][$dates_akID];
		
		$date_count = count($dates);
		if($date_count == 1){
			$error->add(t('You must have at least one date.'));   
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
			//$eventDateData = explode('/', $_REQUEST['eventDate'));
			$_eventDate = mktime(0, 0, 0, $eventDateData[0], $eventDateData[1], $eventDateData[2]);
			
			if( $_endDate <= $_eventDate && $recur){
		   		$error->add(t('Your "End Date" value may not be earlier than or equal to your "Start Date" value whith the "Recuring" option set'));   
			}
		}
		
		
		if($recur == 'daily' &&  $date_count > 1  && ($eventDateData[1] - $eventDateStart[1])>1){
			$error->add(t('Date Set\'s of more than one date may only recur by week or month.'));
		}	

		if($recur == 'weekly' && ($eventDateData[1] - $eventDateStart[1])>=7){
			$error->add(t('You may not have a Date Set larger than 7 days recurring weekly.'));
		}
		
		if($recur == 'monthly' && ($eventDateData[1] - $eventDateStart[1])>=29){
			$error->add(t('You may not have a Date Set larger than 29 days recurring Monthly.'));
		}

		return $error;
	}
	
	
	function saveData($p) {
		$_POST = $_REQUEST;
		$blocks = $p->getBlocks('Main');
		foreach($blocks as $b) {
			if($b->getBlockTypeHandle()=='content'){
				$b->deleteBlock();
			}
		}
		
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
		
		$emdd = CollectionAttributeKey::getByHandle('event_multidate');
		$emdd->saveAttributeForm($p);
		
		$eexc = CollectionAttributeKey::getByHandle('event_exclude');
		$eexc->saveAttributeForm($p);
		
		$ead = CollectionAttributeKey::getByHandle('event_allday');
		$ead->saveAttributeForm($p);
		
		$eg = CollectionAttributeKey::getByHandle('event_grouped');
		$eg->saveAttributeForm($p);
		
		$ccc = CollectionAttributeKey::getByHandle('category_color');
		$ccc->saveAttributeForm($p);
		
		
		//$eet = CollectionAttributeKey::getByHandle('end_time');
		//$eet->saveAttributeForm($p);
		
		//$est = CollectionAttributeKey::getByHandle('start_time');
		//$est->saveAttributeForm($p);
		
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
		
		$data = array('content' => str_replace('\\','',$_REQUEST['eventBody']));			
					
		$b = $p->addBlock($bt, 'Main', $data);
		$b->setCustomTemplate('event_post');
		
		$p->reindex();
			
	}
	
	print json_encode(action_add());