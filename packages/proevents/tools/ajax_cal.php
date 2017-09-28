<?php     
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$eventify = Loader::helper('eventify','proevents');
$nh = Loader::helper('navigation');

if($_REQUEST['state']){
	$c = Page::getByID($_REQUEST['cID'],$_REQUEST['state']);
}else{
	$c = Page::getByID($_REQUEST['cID']);
}
	
$link = $nh->getLinkToCollection($c);

$euro_cal = 0;


$date =time () ;
$day = 1 ;
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];

$date = $year.'-'.$month.'-'.$day;


$bID = $_REQUEST['bID'];
Loader::model('block');
$b = Block::getByID($bID);
$controller = $b->getController();
$sctID = $controller->sctID;
if($_REQUEST['ctID'] != ''){
	$ctID = $_REQUEST['ctID'];
	$controller->ctID = $ctID;
}
$settings = $controller->settings;
$xml_feeds = $settings['xml_feeds'];

	print '<h1>'.$_REQUEST['title'].'</h1>' ;

		$days_in_month = cal_days_in_month(0, $month, $year) ; 

		if($sctID != 'All Sections'){
			$section = $sctID;
		}

		if($xml_feeds){
			Loader::model('google_merge','proevents');
			$gm = new GoogleMerge($bID,$xml_feeds,$date);
			$events = $gm->getEventsArray();
			//var_dump($events);
			$evs = $controller->getEvents($date);
			$el = $controller->el;
		}else{
			$events = $controller->getEvents($date);
			$el = $controller->el;
		}

		$first_day = mktime(0,0,0,$month, 1, $year) ; 

		$title = date('F', $first_day) ; 

		$day_of_week = date('D', $first_day) ; 

	
	switch($day_of_week){ 
			case 'Mon' : if($euro_cal >= 1){$blank = 0;}else{$blank = 1;} break; 
			case 'Tue' : if($euro_cal >= 1){$blank = 1;}else{$blank = 2;} break; 
			case 'Wed' : if($euro_cal >= 1){$blank = 2;}else{$blank = 3;} break; 
			case 'Thu' : if($euro_cal >= 1){$blank = 3;}else{$blank = 4;} break; 
			case 'Fri' : if($euro_cal >= 1){$blank = 4;}else{$blank = 5;} break; 
			case 'Sat' : if($euro_cal >= 1){$blank = 5;}else{$blank = 6;} break; 
			case 'Sun' : if($euro_cal >= 1){$blank = 6;}else{$blank = 0;} break; 
	}
		
			print "<table  class='event_cal'>";
			print "<tr>";
			print "<th class='select' align='left'><span onClick=\"prev_month();\" class=\"button\"><span class=\"ui-icon ui-icon-circle-triangle-w\"></span></span></th>";
			   
			print "<th colspan=5 class='year' align='center'>".t($title)." $year</th>";
			
			print "<th><span onClick=\"next_month();\" class=\"button\" style=\"float: right;\"><span class=\"ui-icon ui-icon-circle-triangle-e\"></span></span></th></tr>";
	
			if($euro_cal >= 1){
			
			print '<tr class="header"><td>'.t('Mon').'</td><td>'.t('Tue').'</td><td>'.t('Wed').'</td><td>'.t('Thu').'</td><td>'.t('Fri').'</td><td>'.t('Sat').'</td><td>'.t('Sun').'</td></tr>';
			
			}else{
			
			print '<tr class="header"><td>'.t('Sun').'</td><td>'.t('Mon').'</td><td>'.t('Tue').'</td><td>'.t('Wed').'</td><td>'.t('Thu').'</td><td>'.t('Fri').'</td><td>'.t('Sat').'</td></tr>';
			
			}

			$day_count = 1;

			print "<tr>";

			while ( $blank > 0 ) { 
				print "<td class='cal_blank'></td>"; 
				$blank = $blank-1; 
				$day_count++;
			}

			$day_num = 1;

			while ( $day_num <= $days_in_month ) { 
		
			$daynum = date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num));
			
			$ei = $el->eventIs($daynum,$ctID,$section);
			
			if(date('Y-m-d') == date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num))){ $daystyle = 'current';}else{$daystyle = 'day';}
				
				print '<td  valign="top" class="'.$daystyle.' '.$el->status.'">';
				print '<div class="cal_day">';
				
				$daydo = false;
				if($xml_feeds){
					$day_flat = $year.str_pad($month, 2, '0', STR_PAD_LEFT).str_pad($day_num, 2, '0', STR_PAD_LEFT);
					foreach($events as $key=>$garb){
						if(date('Ymd',strtotime($garb['eventDate'])) == $day_flat){
							$daydo = true;
							break;
						}
					}
				}
				
				if($ei || $daydo == true){
				
					
						print '<div class="daynum hasevent">';

						print $day_num ; 

						print '<div class="infoPreview myDialogContent'.$day_num.'">';
				
					foreach($events as $date_string => $ep){
							
							if($xml_feeds){
								$google_event = Page::getByPath('/google-event');
								//https://www.googleapis.com/calendar/v3/calendars/calendarId/events/eventId
								$date = date('Y-m-d',strtotime($ep['eventDate']));
								$url = $ep['link'];
								$location = $ep['where'];
								$title = $ep['title'];
								$content = $ep['description'];
								$color = null;
								if($ep['color']){
									$color = $ep['color'];
								}
								
								$st = date(t('h:i a'),strtotime($ep['starttime']));
                    			$et = date(t('h:i a'),strtotime($ep['endtime']));
                    								
                    			if($ep['allday'] == 1 || $st[1] == $et[1]){
                    				$allday =  1;
                    			}else{
                    				$allday = false;
                    			}
                                    
								$time = $st .' - '.$et;
								if($date == $daynum){
									$events_item = $day_num;
								}
							}else{
								$dh = Loader::helper('form/date_time_time','proevents');
						  		$date_array = $dh->translate_from_string($date_string);
						  		
						  		Loader::model('event_item','proevents');
						  		$event_item = new EventItem($ep,$date_string);
						  		
						  		$eID = $date_array['eID'];
						  		$date = $date_array['date'];
						  		$time = $date_array['start'].' - '.$date_array['end'];
						  			
						  		$title =  $ep->getCollectionName();
						  		
								$url = $nh->getLinkToCollection($ep);
								
								$content =  $event_item->getEventDescription();
								
								$location = $ep->getAttribute('event_local');
								
								$color = $ep->getAttribute('category_color');
								
								$category = $ep->getAttribute('event_category');
								
								$allday = $ep->getAttribute('event_allday');
				
								if($date == $daynum){
									$events_item = $day_num;
								}
							}
							

								$i += 1;

								if($events_item==$day_num){
									
									if(!$xml_feeds){
										if ($ep->getCollectionAttributeValue('exclude_nav')) {
											$url = 'javascript:;';
										}
										$url = $url.'?eID='.$eID;
									}
	
									if($allday != 1){ $itemstyle = 'normal';}else{$itemstyle = 'allday';}
								
									print '<div class="'.$itemstyle.'">';
									print '<a  href="'.$url.'" class="eventtooltip">';
									if($color){
										print '<div style="background-color: '.$color.'!important;" class="category_color">';
										print substr($title,0,12) ;
										print '</div>';
									}else{
										print substr($title,0,12) ;
									}
									
									print '<span>';
									print '<h3>'.$title.'</h3>';
									if($location!=''){	
										print '<strong>'.$location.'</strong>';
										print '<br/>';
									}
									print '<strong>';
									if ($allday !=1){
											print $time;
										}else{
											print t('All Day');
										}

										print '</strong>';
										print '<br/>';
		
										if($controller->truncateSummaries){
	  										print  '<p>'.substr($content,0,$controller->truncateChars).'…..</p>';
	  									}else{
	  										print  '<p>'.$content.'</p>';
	  									}
								
									print '</span>';
									print '</a>';
								print '</div>';
				
								}
							unset($events_item);
						}
					print '	</div>';
					print '</div>';
				}else{
					print '<div class="daynum">';
					print $day_num ; 
				}
	print '</div>';
print '</td>';

$day_num++; 
$day_count++;


	if ($day_count > 7){
		print "</tr><tr>";
		$day_count = 1;
	}
}


while ( $day_count >1 && $day_count <=7 ) { 
	print "<td class='cal_blank'> </td>"; 
	$day_count++; 
} 

print "</tr></table>";

print '<script type="text/javascript">
$(function(){
	 $(".eventtooltip").mouseover(function(){
          $(this).find("span").show();
          $(this).mouseout(function(){
          	$(this).find("span").hide();
          });
     });
});
</script>';