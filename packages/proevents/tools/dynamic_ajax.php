<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$eventify = Loader::helper('eventify','proevents');
$nh = Loader::helper('navigation');
$months = array(
	'Jan'=>t('Jan'),
	'Feb'=>t('Feb'),
	'Mar'=>t('Mar'),
	'Apr'=>t('Apr'),
	'May'=>t('May'),
	'Jun'=>t('Jun'),
	'Jul'=>t('Jul'),
	'Aug'=>t('Aug'),
	'Sep'=>t('Sep'),
	'Oct'=>t('Oct'),
	'Nov'=>t('Nov'),
	'Dec'=>t('Dec'),
);
$days = array(
	'Monday'=>t('Monday'),
	'Tuesday'=>t('Tuesday'),
	'Wednesday'=>t('Wednesday'),
	'Thursday'=>t('Thursday'),
	'Friday'=>t('Friday'),
	'Saturday'=>t('Saturday'),
	'Sunday'=>t('Sunday')
);

if($_REQUEST['state']){
	$c = Page::getByID($_REQUEST['cID'],$_REQUEST['state']);
}else{
	$c = Page::getByID($_REQUEST['cID']);
}


$link = $nh->getLinkToCollection($c);

$euro_cal = 0;

if(!$_REQUEST['year']){
	$_REQUEST['year'] = date('Y');
	$_REQUEST['month'] = date('m');
}

if (isset($_REQUEST['dateset'])){

	$date =time () ;
	$day = date('d', $date) ;
	$year = $_REQUEST['setyear'];
	$month = $_REQUEST['setmo'];
	
}else{
	if($_REQUEST['day']){
		$day = $_REQUEST['day'];
	}else{
		$day = '01';
	}
	$date = $_REQUEST['year'].'-'.$_REQUEST['month'].'-'.$day;

	$month = $_REQUEST['month'] ;

	$year = $_REQUEST['year'] ;

}

$sctID = $_REQUEST['sctID'];
$ctID = $_REQUEST['ctID'];
$bID = $_REQUEST['bID'];

if($_REQUEST['nab_days']){
		$days_in_month = cal_days_in_month(0, $month, $year) ; 

		if($sctID != 'All Sections'){
			$section = $sctID;
		}
		$eventify = Loader::helper('eventify','proevents');

		$blocks = $c->getBlocks();
		foreach($blocks as $b){
			if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
				$controller = $b->getController();
			}
		}
		
		if(!$controller){
			$blocks = $c->getGlobalBlocks();
			foreach($blocks as $b){
				if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
					$controller = $b->getController();
				}
			}
		}

		$events = $controller->getEvents($date);
		$el = $controller->el;

		$first_day = mktime(0,0,0,$month, 1, $year) ; 

		$day_count = 1;

		$day_num = 1;

		while ( $day_num <= $days_in_month ) { 

			if(date('Y-m-d') == date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num))){ $daystyle = 'current';}else{$daystyle = 'day';}
			
			$daynum = date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num));
			
			$itemstyle = 'normal';
			
			if($el->eventIs($daynum,$ctID,$section)==true){
				print '<div class="has_date dyday">';
				print $day_num ; 
				print '</div>';
			}else{
				print '<div class="normal dyday">';
				print $day_num ; 
				print '</div>';
			}
					
			
			$day_num++; 
			$day_count++;
		}
}

if($_REQUEST['nab_date']){
//	$blocks = $c->getBlocks();
//	foreach($blocks as $b){
//		if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
//			$controller = $b->getController();
//		}
//	}
//	$events = $controller->getEvents($date);
	
		print '<div id="dayname">';

		print strtoupper($days[date('l',strtotime($date))]);

		print '</div>';
		print '<div id="day">';
		print '	<div id="month">';
				print strtoupper($months[date('M',strtotime($date))]);
		print '	</div>';

			print date('d',strtotime($date));
		print '</div>';
		print '<br style="clear: both"/>';
}

if($_REQUEST['nab_events']){
	$blocks = $c->getBlocks();
	foreach($blocks as $b){
		if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
			$controller = $b->getController();
		}
	}
	
	if(!$controller){
		$blocks = $c->getGlobalBlocks();
		foreach($blocks as $b){
			if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
				$controller = $b->getController();
			}
		}
	}
	
	$events = $controller->getEvents($date,$date);
	
	//print $date.'<br/>';
	//print count($events).'<br/>';
	if($events){
		foreach($events as $date_string=>$event){
			$i++;
			$dh = Loader::helper('form/date_time_time','proevents');
			$date_array = $dh->translate_from_string($date_string);	
			
			$eID = $date_array['eID'];
	  		$date = $date_array['date'];
	  		$time = $date_array['start'];
	  		
	  		$color = $event->getAttribute('category_color');
	  		
	  		print '<div class="dynamic_event_details" onClick="show_description(\''.$i.'\')">';
	  		print '<div class="dynamic_date_time"';
	  		if(!$color){
	  			$color = '#a1a1a1';
	  		}
	  		print 'style="background-color: '.$color.';"';
	  		print '>'.$time.'</div> ';
			print substr($event->getCollectionName(),0,25);
			print '<br/>';
			print '<div class="description_'.$i.' details" style="display: none;">'.$event->getCollectionDescription().'</div>';
			print '</div>';
			print '<br/>';
		}
	}else{
		print 'There are no events for this day.';
	}
}
			