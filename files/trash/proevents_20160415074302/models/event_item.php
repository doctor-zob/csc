<?php     
defined('C5_EXECUTE') or die("Access Denied.");
/**
*
* An object that allows a filtered list of events to be returned.
* @package ProEvents
*
**/
class EventItem extends Model {
	
	var $event;
	var $eID;
	var $num;
	var $dates_array;
	var $date_item;
	var $times_array;
	var $next_dates_array;
	
	
	function __construct($event, $eID = null){
		$this->event = $event;

		if(strlen($eID)>8){
			$date_string = $eID;
			$this->setItemFromString($date_string);
			$eID = null;
		}
		
		$dth = Loader::helper('form/date_time_time','proevents');
		$this->dates_array = $dth->translate_from($event);
		$this->num = count($this->dates_array);
		if($eID && $eID != ''){
			$this->eID = $eID;
			$this->setClickedDate();
		}else{
			$this->setNextNumDates();
		}
	
	}
	
	public function setItemFromString($string){
		$dth = Loader::helper('form/date_time_time','proevents');
		$this->date_array = $dth->translate_from_string($string);
		$this->eID = $this->date_array['eID'];
		$this->date_item = $this->date_array['date'];
		$times[0]['start'] = $this->date_array['start'];
		$times[0]['end'] = $this->date_array['end'];
		$this->times_array = $times;
	}
	
	
	public function getEventItemTimes(){
		/*
		$db = Loader::db();
		$times = array();
		$r = $db->execute("SELECT * FROM btProEventDates WHERE eventID = ? AND date = ?",array($this->event->getCollectionID(),$this->date_item));
		while($row = $r->fetchrow()){
			$i++;
			$times[$i]['start'] = $row['sttime'];
			$times[$i]['end'] = $row['entime'];
		}
		$this->times_array = $times;
		return $this->times_array;
		*/
		return $this->next_dates_array;
		//var_dump(count($this->next_dates_array));
	}
	
	public function getEventDescription(){
		//$db = Loader::db();
		//return $db->getOne("SELECT description FROM btProEventDates WHERE eventID = ? AND date = ?",array($this->event->getCollectionID(),$this->date_item));
		return $this->next_dates_array[0]->description;
	}
	
	private function setClickedDate(){
		$db = Loader::db();
		$this->date_item = $db->getOne("SELECT date FROM btProEventDates WHERE eID = ?",array($this->eID));
		$this->setNextNumDates();
	}

	
	private function setNextNumDates($grouped = false){
		$db = Loader::db();
		
		if($this->event->getAttribute('event_grouped')>0){
			$grouped = $db->getOne("SELECT grouped FROM btProEventDates WHERE eID=?",array($this->eID));
			$r = $db->execute("SELECT * FROM btProEventDates WHERE date >= ? AND eventID = ? AND grouped = ? ORDER BY date,sttime ASC LIMIT ?",array($this->date_item,$this->event->getCollectionID(),$grouped,$this->num));
		}else{
			if(!$this->date_item){
				$r = $db->execute("SELECT * FROM btProEventDates WHERE eventID = ?  ORDER BY date,sttime ASC LIMIT ?",array($this->event->getCollectionID(),$this->num));
			}else{
				$r = $db->execute("SELECT * FROM btProEventDates WHERE date = ? AND eventID = ?  ORDER BY date,sttime ASC LIMIT ?",array($this->date_item,$this->event->getCollectionID(),$this->num));
			}
		}

		while($row = $r->fetchrow()){
			//$dates[] = $row['date'].':^:'.$row['sttime'].':^:'.$row['entime'];
			//if($grouped){ $row['date'] = $this->date_item; }
			$dates[] = new EventItemDate($row);
		}
		$this->next_dates_array = $dates;
	}
	
	public function getEventItemID(){return $this->eID;}
	public function getEventItemDate(){return $this->date_item;}
	public function getEventItemNextDates(){return $this->next_dates_array;}
	public function getEventItemsNum(){return $this->num;}
	public function getEventItemDatesArray(){return $this->dates_array;}
}


class EventItemDate extends model {
	var $title;
	var $date;
	var $start;
	var $end;
	var $description;
	var $status;
	var $event_price;
	var $event_qty;
	
	function __construct($data){
		if(!is_array($data)){
			$eID = $data;
			$db = Loader::db();
			$data = $db->getRow("SELECT * FROM btProEventDates WHERE eID = ?",array($eID));
		}
		$this->eID = $data['eID'];
		$this->title = $data['title'];
		$this->date = $data['date'];
		$this->start = $data['sttime'];
		$this->end = $data['entime'];
		$this->description = $data['description'];
		$this->status = $data['status'];
		$this->event_price = $data['event_price'];
		$this->event_qty = $data['event_qty'];
	}
}


class EventItemDates extends model {

	var $event;
	var $title;
	var $description;
	var $allday;
	var $grouped;
	var $category;
	var $location;
	var $section;
	var $recur;
	var $eventID;
	var $dates;
	var $dates_array;
	
	function __construct($p,$save=false){

		Loader::model("collection");
		$cID = $p->getCollectionID();
		$event = Page::getByID($cID);
		
		//set the event object
		$this->event = $event;
		
		//set the event title
		$this->title = $event->getCollectionName(); 
		
		//set the event description
		$this->description = $event->getCollectionDescription(); 
		
		//set the allday
		$akad = CollectionAttributeKey::getByHandle('event_allday');
		$this->allday = $event->getCollectionAttributeValue($akad);
		
		//set grouped
		$akad = CollectionAttributeKey::getByHandle('event_grouped');
		$this->grouped = sprintf($event->getCollectionAttributeValue($akad));
		
		//set the dates array
		$dates_array = Loader::helper('form/date_time_time','proevents')->translate_from($event);
		sort($dates_array);
		$this->dates_array = $dates_array;
		
		//set event recuring
		$akrr = CollectionAttributeKey::getByHandle('event_recur');
		if($event->getCollectionAttributeValue($akrr)){
			$this->recur = $event->getCollectionAttributeValue($akrr)->current()->value;
		}
	
		//set event categories
		$aklc = CollectionAttributeKey::getByHandle('event_category');
		$eventCat_pre = $event->getCollectionAttributeValue($aklc);
		if($eventCat_pre){
			$ec = $event->getCollectionAttributeValue($aklc)->count();
			for($i=0;$i<$ec;$i++){
				$eventCat[]= $event->getCollectionAttributeValue($aklc)->current()->value;
				$event->getCollectionAttributeValue($aklc)->next();
			}
			if(is_array($eventCat_pre)){
				if(count($eventCat_pre)>1){
					$this->category = implode(', ',$eventCat_pre);
				}else{
					$this->category = $eventCat_pre[0];
				}
			}else{
				$this->category = $eventCat_pre;
			}
		}
		
		//set event location
		$aklo = CollectionAttributeKey::getByHandle('event_local');
		$this->location = $event->getCollectionAttributeValue($aklo);

		//set event section
		$sec = Page::getByID($event->getCollectionParentID());
		$this->section = $sec->getCollectionID(); 
		
		//set event price
		$price = CollectionAttributeKey::getByHandle('event_price');
		if($event->getCollectionAttributeValue($price)){
			$this->event_price = sprintf($event->getCollectionAttributeValue($price));
		}else{
			$this->event_price = null;
		}
		
		//set event qty
		$qty = CollectionAttributeKey::getByHandle('event_qty');
		if($event->getCollectionAttributeValue($qty)){
			$this->event_qty = sprintf($event->getCollectionAttributeValue($qty));
		}else{
			$this->event_qty = null;
		}
		

		$this->eventID = $event->getCollectionID();
		
		if($this->recur!=''){
			$this->dateSet();
		}else{
			//$dates = array($c->getCollectionDatePublic('Y-m-d')); 
			for($i=0; $i < count($this->dates_array); $i++){
				$dates[] = array(array('dsID'=> $this->dates_array[$i]['dsID'],'date'=>$this->dates_array[$i]['date']));
			}
			$this->dates = $dates;
		}
		
		//var_dump($this->dates);exit;
		if($save==true){
			$this->saveEventItemDates();
		}
	}
	
	private function dateSet(){		
$cobj = $this->event; $recur = $this->recur; Loader::model("attribute/categories/collection"); $emdd = CollectionAttributeKey::getByHandle('event_multidate'); $date_multi = $cobj->getCollectionAttributeValue($emdd); $date_multi_array = explode(':^:',$date_multi); foreach($date_multi_array as $dated){ $date_sub = explode(' ',$dated); $dates_array[] = $date_sub[0]; } sort($dates_array); $excluded_dates = array(); $eexc = CollectionAttributeKey::getByHandle('event_exclude'); $date_exclude = $cobj->getCollectionAttributeValue($eexc); $date_exclude_array = explode(':^:',$date_exclude); foreach($date_exclude_array as $exclude){ $date_sub = explode(' ',$exclude); $excluded_dates[] = date('Y-m-d',strtotime($date_sub[0])); } $esst = new DateTime($dates_array[0]); $ess = $esst->format('Y-m-d');$evth = CollectionAttributeKey::getByHandle('event_thru'); $eet =new DateTime($cobj->getCollectionAttributeValue($evth)); $ee = $eet->format('Y-m-d'); $d1m = date('n',strtotime($cobj->getCollectionAttributeValue($evth))); $d1d = date('j',strtotime($cobj->getCollectionAttributeValue($evth))); $d1y = date('Y',strtotime($cobj->getCollectionAttributeValue($evth))); $d2m = date('n',strtotime($ess)); $d2d = date('j',strtotime($ess)); $d2y = date('Y',strtotime($ess)); $datetime1 = mktime(0,0,0,$d1m,$d1d,$d1y); $datetime2 = mktime(0,0,0,$d2m,$d2d,$d2y);$interval = floor(($datetime1-$datetime2)/86400);$diff = $eet->diff($esst);$dayspan = $diff->days;/*$dayspan = $interval;*/$dint = new DateInterval('P1D');$dint->invert = 1;$esst->add($dint)->format('Y-m-d');$wk = true;for($d=0;$d<=$dayspan;$d+=1){ $iti++; $year = $cobj->getCollectionDatePublic('Y'); $month = $cobj->getCollectionDatePublic('m'); $day = $cobj->getCollectionDatePublic('d'); $daynum = $esst->add(new DateInterval('P1D'))->format('Y-m-d');if(!in_array($daynum,$excluded_dates)){ if($recur==t('daily')){ $di = 0; foreach($dates_array as $esd){ $di++; $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } }elseif($recur==t('weekly')){ $di = 0; foreach($dates_array as $esd){ $di++; $es = date('Y-m-d',strtotime($esd)); $eventDd = date('D',strtotime($es)); $daynumD = date('D',strtotime($daynum)); if($daynumD == $eventDd){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } } }elseif($recur==t('every other week')){ $di = 0; foreach($dates_array as $esd){ $di++; $es = date('Y-m-d',strtotime($esd)); $eventDd = date('D',strtotime($es)); $daynumD = date('D',strtotime($daynum)); if($daynumD == $eventDd && $wk==true){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; $wc++; if($wc == count($dates_array)){ $wk = false; $wc = 0; } }elseif($daynumD == $eventDd && $wk==false){ $wc++; if($wc == count($dates_array)){ $wk = true; $wc = 0; } } } }elseif($recur==t('monthly')){ $daynumm = date('Y-m',strtotime($daynum)); $daynummDay = date('d',strtotime($daynum)); $bug_array = array('01','08','15','22','29'); $di = 0; foreach($dates_array as $esd){ $di++; $es = date('Y-m-d',strtotime($esd)); $esi = date('Y-m-d',strtotime($esd)); $eventm = date('Y-m',strtotime($es)); $eventD = date('d',strtotime($es)); $eventDa = date('D',strtotime($es)); $eventDaL = date('l',strtotime($es)); $eventFirstDay = date('Y-m-d',strtotime($eventm.'-01')); $eventDa = date('D',strtotime($eventFirstDay)); $monthFirstDay = date('Y-m-d',strtotime($daynumm.'-01')); $monthD = date('d',strtotime($monthFirstDay)); $monthDa = date('D',strtotime($monthFirstDay)); $eventFirstDay = date('Y-m-d',strtotime($eventm.'-01')); $em = date('m',strtotime($monthFirstDay)); $bug_first = 'first '; $bug_second = 'second '; $bug_third = 'third '; $bug_fourth = 'fourth '; $bug_fifth = 'fifth '; if(in_array($daynummDay,$bug_array) && !in_array($eventD,$bug_array)){ if($daynummDay == '01'){ $bug_first = '+0 week '; $bug_second = 'first '; $bug_third = 'second '; $bug_fourth = 'third '; $bug_fifth = 'fourth '; $es = date('Y-m-d',strtotime('+0 week ', strtotime($es))); }else{ $bug_first = 'first '; $bug_second = 'second '; $bug_third = 'third '; $bug_fourth = 'fourth '; $bug_fifth = 'fifth '; $es = date('Y-m-d',strtotime('-1 week ', strtotime($es))); } } if(in_array($eventD,$bug_array) && !in_array($daynummDay,$bug_array)){ $es = date('Y-m-d',strtotime('+1 week ', strtotime($es))); }elseif(in_array($eventD,$bug_array) && $daynummDay == '01'){ $bug_first = '+0 week '; $es = date('Y-m-d',strtotime('+0 week ', strtotime($es))); } if($es == date('Y-m-d',strtotime($bug_first.$eventDaL.'', strtotime($eventFirstDay)))){ if($daynum == date('Y-m-d',strtotime($bug_first.$eventDaL.'', strtotime($monthFirstDay)))){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } }elseif  ($es == date('Y-m-d',strtotime($bug_second.$eventDaL.'', strtotime($eventFirstDay)))){ if($daynum == date('Y-m-d',strtotime($bug_second.$eventDaL.'', strtotime($monthFirstDay)))){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } }elseif  ($es == date('Y-m-d',strtotime($bug_third.$eventDaL.'', strtotime($eventFirstDay)))){ if($daynum == date('Y-m-d',strtotime($bug_third.$eventDaL.'', strtotime($monthFirstDay)))){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } }elseif  ($es == date('Y-m-d',strtotime($bug_fourth.$eventDaL.'', strtotime($eventFirstDay)))){ if($daynum == date('Y-m-d',strtotime($bug_fourth.$eventDaL.'', strtotime($monthFirstDay)))){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } }elseif  ($es == date('Y-m-d',strtotime($bug_fifth.$eventDaL.'', strtotime($eventFirstDay)))){ if($daynum == date('Y-m-d',strtotime($bug_fifth.$eventDaL.'', strtotime($monthFirstDay)))){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } } } }elseif($recur==t('yearly')){ $di = 0; foreach($dates_array as $esd){ $di++; $es = date('Y-m-d',strtotime($esd)); $daynumy = date('m-d',strtotime($daynum)); $eventy = date('m-d',strtotime($es));  if($daynumy == $eventy){ $dates[$di][$iti]['date'] = $daynum; $dates[$di][$iti]['dsID'] = $di; } } } } }$this->dates = $dates;

//exit;
	}



	public function saveEventItemDates(){
		$db = Loader::db();
		//$db->Execute("DELETE from btProEventDates where eventID = ?",array($this->eventID));
		$qi = ("INSERT INTO btProEventDates (title,category,section,eventID,date,allday,sttime,entime,description,status,location,grouped,additional_data,updated,event_qty,event_price) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		
		$qu = ("UPDATE btProEventDates SET title=?, category=?, section=?, eventID=?, date=?, allday=?, sttime=?, entime=?, description=?,status=?,location=?,grouped=?,additional_data=?,updated=?,event_qty=?,event_price=? WHERE eID=?");
		$g = 0;
		//var_dump($this->dates);exit;
		foreach($this->dates as $date_days){
			//var_dump($date_days);exit;	
			$t = 0;
			foreach($date_days as $date){
				if($this->recur != 'daily'){
					$t++;
				}
				foreach($this->dates_array as $da){
					if($da['dsID'] == $date['dsID']){
						if($this->grouped < 1){
							$g++;
						}else{
							$g = $t;
						}
										
						$edate = $date['date'];
						$start = date('H:i:s',strtotime($da['start']));
						$end = $da['end'];
						
						$generated = $db->getRow("SELECT eID,status,event_qty from btProEventDates where eventID = ? AND date=? ANd sttime=?",array($this->eventID,$edate,$start));
						
						$eID = $generated['eID'];
						
						$args = array(
							'title'=> $this->title,
							'category'=> $this->category,
							'section'=> $this->section,
							'eventID'=> $this->eventID,
							'date'=> $edate,
							'allday'=> $this->allday,
							'sttime'=> $start,
							'entime'=> date('H:i:s',strtotime($end)),
							'description'=> $this->description,
							'status'=>$_REQUEST['status'],
							'location'=> $this->location,
							'grouped'=>$g,
							'additional_data'=>'',
							'updated'=> 1,
							'event_qty'=>$this->event_qty,
							'event_price'=>$this->event_price
						);
			
						if($eID){
							if($_REQUEST['status']==''){
								$args['status'] = $generated['status'];
								$args['event_qty'] = $generated['event_qty'];
							}
							$args['eID'] = $eID;
							$db->Execute($qu,$args);
						}else{
							$db->Execute($qi,$args);
						}
						
					}
					
				}
				
			}
		}
		$db->Execute("DELETE from btProEventDates where eventID = ? AND updated <> ?",array($this->eventID,1));
		$db->Execute("UPDATE btProEventDates SET updated = ? where eventID = ?",array(0,$this->eventID));
	}
	
	
	public function getEventDates(){
		return $this->dates;
	}
	
}