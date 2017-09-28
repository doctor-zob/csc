<?php     defined('C5_EXECUTE') or die("Access Denied."); 
class ProeventsFormStatusExtend {

/*  
/ ProEvents Attribute Handles to match

event_thru 		- date
event_tag		- select
event_category	- select
category_color	- color
event_multidate	- multidate
event_exclude	- multidate
event_allday	- checkbox
event_grouped	- checkbox
event_recur		- date
exclude_nav		- checkbox
thumbnail		- image/file
event_local		- text
address			- text
contact_name	- text
contact_email	- text

*/
	//$form = ProformsItem() object;

	public function onFormApprove($form,$handle=null){
		if($handle=='event_status'){
			Loader::model('event_item','proevents');
			$pfID = $form->getProformsItemID();
			$asID = $form->getAttributeSetID();
			$as = AttributeSet::getByHandle('internal');
			$setAttribs = $as->getAttributeKeys();
			foreach($setAttribs as $ak){
				$at = $ak->getAttributeType();
				$handle = $ak->getAttributeKeyHandle();
				if($handle=='event_publish'){
					$cnt = $ak->getController();
					$cnt->type_form();
					$dpage = Page::getByID($cnt->location);
					$dpath = $dpage->getCollectionPath();
					$p = Page::getByPath($dpath.'/'.$pfID.'/');

					$dates_array = Loader::helper('form/date_time_time','proevents')->translate_from($p);
					$dates_renumber = array();
					foreach($dates_array as $date_item){
						array_push($dates_renumber, $date_item);
					}
					
					$start_date = date('Y-m-d',strtotime($dates_renumber[1]['date']));
					$description = $form->getAttribute('event_description');
					$title = $form->getAttribute('event_title');
					
					$data = array('cDescription' => $description, 'cName' => $title, 'cDatePublic' => $start_date);
					$p->update($data);
				}
			}
			if($p && $p->getCollectionID()>0){
				$event_item = new EventItemDates($p,true);
			}
		}
	}

	public function onPaymentSuccess($pfo){
			$question_set = $pfo->asID;
			$setAttribs = AttributeSet::getByID($question_set)->getAttributeKeys();
			foreach ($setAttribs as $ak) {
				if($ak->getAttributeType()->getAttributeTypeHandle() == 'price_qty'){
					$value = $pfo->getAttributeValueObject($ak);
					if(is_object($value)) {
                		$qty = $value->getValue();
                	}
				}
			}
			if(!$qty){
				$qty = 1;
			}
			foreach ($setAttribs as $ak) {
				if($ak->getAttributeType()->getAttributeTypeHandle() == 'price_event'){
					$value = $pfo->getAttributeValueObject($ak);
					if(is_object($value)){
						$db = Loader::db();
						$eventIDs = explode(',',$value->getValue());
						foreach($eventIDs as $eID){
							$db->Execute("UPDATE btProEventDates SET event_qty = (event_qty - $qty) WHERE eID = ?",array($eID));
						}
						$db->Execute("UPDATE btProEventDates SET status = 'booked' WHERE status = 'available' AND event_qty = 0");
					}
				}
			}
	}
	
}