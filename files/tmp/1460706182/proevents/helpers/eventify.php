<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class EventifyHelper {

	public function __construct() {

	}

	public function getSettings(){
		$db= Loader::db();
		$r = $db->execute("SELECT * FROM btProEventSettings");
		while($row=$r->fetchrow()){
			$settings = $row;
		}
		if (empty($settings)){$settings = array();}

		return $settings;

	}

	public function getGoogleEventVars($request){
		$event = $request['event'];

		if($request['startTime']){
			$event_time = $request['startTime'];
		}else{
			$event_time = substr($event,strrpos($event,'_')+1);
		}

		$date = gmdate('Y-m-d h:i a',strtotime($event_time));

		$url = str_replace('@','%40',$event);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

		$result = curl_exec($ch);

		$xml = simplexml_load_string($result);

		$gd = $xml->children('http://schemas.google.com/g/2005');

		//var_dump($endTime = $gd->when->attributes()->endTime);

		if(is_object( $gd->originalEvent->when )){
	   	 	$startTime = sprintf($gd->originalEvent->when->attributes()->startTime);
   	 	}elseif ( is_object($gd->when) ) {
            $startTime = sprintf($gd->when->attributes()->startTime);
        } elseif ( $gd->recurrence ) {

        	//DTSTART;TZID=America/Grand_Turk:20120312T151000 DTEND;
        	// 1-find text bweteen DTSTART <-> DTEND
        	// 2-explode by ':' , second part of array is time stamp [1]
        	// 3-wipe spaces
        	if(preg_match_all('~DTSTART(.*?)DTEND~s',$gd->recurrence,$match)) {
			        $m = explode(':',$match[1][0]);
			        //var_dump($m[1]);
			        $startTime = str_replace(' ','',$m[1]);
			}

			$date = explode('T',$startTime);
	   	 	$date = gmdate('Y-m-d h:i a',strtotime($date[0]));

            //$startTime = $gd->recurrence->when->attributes()->startTime;
            $endTime = $gd->when->attributes()->endTime;
        }


        $startTime = explode('T',$startTime);
        $date = $startTime[0];
        $startTime = explode('.',$startTime[1]);
        $startTime = $date.' '.$startTime[0];

        $endTime = explode('T',sprintf($gd->when->attributes()->endTime));
        $date2 = $endTime[0];
        $endTime = explode('.',$endTime[1]);
        $endTime = $date2.' '.$endTime[0];

        $where = sprintf($gd->where->attributes()->valueString);

		curl_close($ch);

		$vars['eventDate'] = $date;
		$vars['startTime'] = $startTime;
		$vars['endTime'] = $endTime;
		$vars['eventTitle'] = $xml->title;
		$vars['event_local'] = $where?$where:null;
		$vars['eventContent'] = $xml->content;
		$vars['contact_name'] = $xml->author->name;
		$vars['contact_email'] = $xml->author->email;
		$vars['url'] = $xml->link->attributes()->href;

		//var_dump($xml);

		return $vars;

	}

	public function getEventListVars($date_string,$event){
		Loader::model('event_item','proevents');
		$event_item = new EventItem($event,$date_string);

		$vars['origin_date_array'] = Loader::helper('form/date_time_time')->translate_from($event);;

  		$vars['eID'] = $event_item->getEventItemID();

  		$vars['date'] = $event_item->getEventItemDate();

  		$vars['times_array'] = $event_item->getEventItemTimes();

  		$vars['next_dates_array'] = $event_item->getEventItemNextDates();

  		$vars['status'] = $vars['next_dates_array'][0]->status;

  		$vars['title'] =  $event->getCollectionName();

		$vars['url'] = Loader::helper('navigation')->getLinkToCollection($event);
		if ($event->getCollectionAttributeValue('exclude_nav')) {
			$vars['url'] = 'javascript:;';
		}

		$vars['content'] = $event_item->getEventDescription();

		$vars['allday'] = $event->getAttribute('event_allday');

		$vars['grouped'] = $event->getAttribute('event_grouped');

		$vars['location'] = $event->getAttribute('event_local');

		$vars['color'] = $event->getAttribute('category_color');

		$vars['category'] = sprintf($event->getAttribute('event_category'));

		$vars['contact_name'] = $event->getAttribute('contact_name');

		$vars['contact_email'] = $event->getAttribute('contact_email');

		$vars['address'] = $event->getAttribute('address');

		$vars['recur'] = $event->getAttribute('event_recur');

		$vars['thru'] = $event->getAttribute('event_thru');

		$imgHelper = Loader::helper('image');
		$vars['imageF'] = $event->getAttribute('thumbnail');
		if (isset($vars['imageF'])) {
    		$vars['image'] = $imgHelper->getThumbnail($vars['imageF'], 110,85)->src;
		}

		return $vars;
	}

	public function getiCalUrl(){
		$uh = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle('pro_event_list');
		$rssUrl = $uh->getBlockTypeToolsURL($bt)."/iCal.php";
		return $rssUrl;
	}

	public function getiCalImgUrl(){
		$uh = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle('pro_event_list');
		$iCalIconUrl = $uh->getBlockTypeAssetsURL($bt,'/tools/calendar_sml.png');
		return $iCalIconUrl;
	}

	public function getEventVars($c){
		Loader::model('event_item','proevents');
		$vars['u'] = new User();

		Loader::model("attribute/categories/collection");
		$settings = $this->getSettings();
		$vars['settings'] = $settings;
		$vars['eventTitle'] = $c->getCollectionName();
		$vars['eventDate'] = $c->getCollectionDatePublic($settings['date_format']);
		$vars['location'] = $c->getAttribute('event_local');
		$vars['color'] = $c->getAttribute('category_color');
		$vars['category'] = $c->getAttribute('event_category');
		$vars['contact_name'] = $c->getAttribute('contact_name');
		$vars['contact_email'] = $c->getAttribute('contact_email');
		$vars['address'] = $c->getAttribute('address');

		$uh = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle('pro_event_list');
		$iCalIconUrl = $uh->getBlockTypeAssetsURL($bt,'/tools/calendar_sml.png');

		/* function for grabbing all related attributes */
		$atts = $c->getSetCollectionAttributes();
		foreach($atts as $attribute){
			$value = $c->getCollectionAttributeValue($attribute);
			$handle = $attribute->akHandle;
			$vars[$handle] = $value;
		}
		$event_item = new EventItem($c,$_REQUEST['eID']);
		$dates_array = $event_item->getEventItemDatesArray();
		$vars['n'] = $event_item->getEventItemsNum();
		$vars['next_dates_array'] = $event_item->getEventItemNextDates();
		return $vars;
	}

	public function URLfix($link) {
		if (substr($link,-1)!='/'){
	    	return $link.'&';
		}else{
			return $link.'?';
		}
	}


	public function grabURL($cParentID, $fix=0) {
		$c = Page::getByID($cParentID);
		$n = Loader::helper('navigation');
		$page = $n->getLinkToCollection($c);
		if ($fix==1){
			return GrabPosts::URLfix($page);
		}else{
			return $page;
		}
	}

	public function getClickedDate($eID){
		$db = Loader::db();
		$r = $db->execute("SELECT * FROM btProEventDates WHERE eID = ?",array($eID));
		while($row = $r->fetchrow()){
			if($date != $row['date']){
				$dates[] = $row['date'].':^:'.$row['sttime'].':^:'.$row['entime'];
			}
			$date = $row['date'];
		}
		return $dates;
	}


	public function getNextNumDates($cID,$n){
		$db = Loader::db();
		$r = $db->execute("SELECT * FROM btProEventDates WHERE eventID = $cID AND date >= CURDATE() ORDER BY date,sttime ASC LIMIT $n");
		while($row = $r->fetchrow()){
			$dates[] = $row['date'].':^:'.$row['sttime'].':^:'.$row['entime'];
		}
		return $dates;
	}


	public function getDateSpan($category,$start,$end,$section=null){

		$categories = explode(', ',$category);

		if($categories != null && !in_array('All Categories',$categories)  && !in_array('',$categories)){
			$ccount = count($categories);
			$category = "AND ( category LIKE ";
			foreach($categories as $category_item){
				$category .= '\'%'.$category_item.'%\'';
				$cct++;
				if($cct < $ccount){
					$category .= ' OR category LIKE  ';
				}
			}
			$category .= ')';
		}else{
			$category = '';
		}

		if($section != null){$section = "AND section LIKE '$section'";}else{$section='';}
		$db = Loader::db();

		$events = array();
		$r = $db->Query("SELECT * FROM btProEventDates WHERE date >= DATE_FORMAT('$start','%Y-%m-%d') AND date <= DATE_FORMAT('$end','%Y-%m-%d') $category $section ORDER BY date ASC, sttime ASC");

		while($row=$r->fetchrow()){
			if($this->checkDateExclude($row['eID']) == false){
				$cID = $row['eventID'];
				$co = Collection::getByID($cID);
				$cvo = $co->getVersionObject();
				if($cvo->isApproved() != true && $cID){
					$db->execute("DELETE FROM btProEventDates WHERE eventID = $cID");
				}else{
					$events[] = $row;
				}
			}
		}

		return $events;
	}


	public function checkDateExclude($eID){
		$db = Loader::db();

		$excluded = array();
		$r = $db->Query("SELECT * FROM btProEventDatesExclude WHERE eventID = $eID");

		if($r->RecordCount()>0){
		return true;
		}else{
			return false;
		}
	}

	public function getExcludedDates(){
		$db = Loader::db();

		$excluded = array();
		$r = $db->Query("SELECT * FROM btProEventDatesExclude");

		while($row=$r->fetchrow()){
			$excluded[] = $row['eventID'];
		}

		return $excluded;
	}

	public function getSearchEvents($search,$type){


		$db = Loader::db();

		$events = array();

		switch($type){
			case 'title':
				$r = $db->Query("SELECT * FROM btProEventDates WHERE title LIKE '%$search%' AND date >= CURDATE() ORDER BY date");
				break;

			case 'date':
				$date = date('Y-m-d',strtotime($search));
				$r = $db->Query("SELECT * FROM btProEventDates WHERE date = DATE_FORMAT('$date','%Y-%m-%d')");
				break;

			case 'description':
				$r = $db->Query("SELECT * FROM btProEventDates WHERE description LIKE '%$search%' AND date >= CURDATE() ORDER BY date");
				break;
		}

		while($row=$r->fetchrow()){
			$events[] = $row;
		}

		return $events;
	}

	public function getAllEvents(){

		$db = Loader::db();

		$events = array();
		$r = $db->Query("SELECT * FROM btProEventDates WHERE date >= CURDATE() GROUP BY eventID,grouped ORDER BY date");

		while($row=$r->fetchrow()){
			$events[] = $row;
		}

		return $events;
	}


	public function getEvent($eID){
		$db = Loader::db();
		$r = $db->execute("SELECT * FROM btProEventDates WHERE eID = ?",array($eID));
		while($row = $r->fetchrow()){
			$date = $row;
		}
		return $date;
	}

	public function userSaved($cID=null,$uID=null){
		$db = loader::db();
		$seID = $db->getOne("SELECT ueID FROM btProEventUserSaved WHERE eventID = ? AND uID =?",array($cID,$uID));
		if($seID){
			return true;
		}
		return false;
	}

	public function updateDate($vars){
		$db = Loader::db();
		//first get the event.ID and group;
		$row = $db->getRow("SELECT * FROM btProEventDates WHERE eID = ?",array($vars[5]));
		$vals = array($vars[0],$vars[1],$vars[2],$vars[3],$vars[4],$row['eventID'],$row['grouped']);
		$r = $db->execute("UPDATE btProEventDates SET title=?,description=?,status=?,event_price=?,event_qty=? WHERE eventID=? AND grouped=?",$vals);
	}


	public function getEventCats(){
		$db = Loader::db();
		$akID = $db->query("SELECT akID FROM AttributeKeys WHERE akHandle = 'event_category'");
		while($row=$akID->fetchrow()){
			$akIDc = $row['akID'];
		}
		$akv = $db->execute("SELECT value FROM atSelectOptions WHERE akID = $akIDc");
		while($row=$akv->fetchrow()){
			$values[]=$row;
		}
		if (empty($values)){
			$values = array();
		}
		return $values;
	}

	public function getRawEventID($cID,$date,$sttime,$entime){
		$start = strtoupper(date('g:i a',strtotime($sttime)));
		$end =  strtoupper(date('g:i a',strtotime($entime)));
		$db = loader::db();
		$eventID = $db->getOne("SELECT eID FROM btProEventDates WHERE eventID = $cID AND date = '$date' AND DATE_FORMAT(sttime,'%l:%i %p') = '$start' AND DATE_FORMAT(entime,'%l:%i %p') = '$end'");
		return $eventID;
	}

}
