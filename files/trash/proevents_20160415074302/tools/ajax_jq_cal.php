<?php     
defined('C5_EXECUTE') or die(_("Access Denied.")); 
	$dth = Loader::helper('form/date_time_time','proevents');
	$nh = Loader::helper('navigation');
	$bID = $_REQUEST['bID'];
	Loader::model('page');
	
	if($_REQUEST['state']){
		$c = Page::getByID($_REQUEST['cID'],$_REQUEST['state']);
	}else{
		$c = Page::getByID($_REQUEST['cID']);
	}
	
	$date =  date('Y-m-d',$_REQUEST['start']);
	$date2 = date('Y-m-d',$_REQUEST['end']);
	
	if(!$_REQUEST['start']){
		$days_in_month = cal_days_in_month(0, date('m'), date('Y')) ; 
		$date = date('Y-m-1');
		$date2 =  date('Y-m-'.$days_in_month);
	}
	
	$date_origin = $date;

	$blocks = $c->getBlocks();

	foreach($blocks as $b){
		if($b->getBlockTypeHandle()=='pro_event_list' && $b->bID == $bID){
			$controller = $b->getController();
		}
	}
	if($_REQUEST['ctID'] != ''){
		$ctID = $_REQUEST['ctID'];
		$controller->ctID = $ctID;
	}
	
	$settings = $controller->settings;
	$xml_feeds = $settings['xml_feeds'];
	
	if($xml_feeds){
		Loader::model('google_merge','proevents');
		$gm = new GoogleMerge($bID,$xml_feeds,$date,$date2,'Y-m-d H:i');
		$events = $gm->getEventsArray();
		//var_dump($events);
		$evs = $controller->getEvents($date);
		$el = $controller->el;
	}else{
		$events = $controller->getEvents($date,$date2);
		$el = $controller->el;
	}

	$recured_array = array();
	$events_array = array();

	foreach($events as $date_string => $ep){
		$i++;

		if($xml_feeds){
			$google_event = Page::getByPath('/google-event');
			//https://www.googleapis.com/calendar/v3/calendars/calendarId/events/eventId
			$date = date('Y-m-d',strtotime($ep['eventDate']));
			$id = $i;
			if(strpos($ep['link'], 'google')){
				$url = $nh->getLinkToCollection($google_event).'?1=1&event='.$ep['ID'];
			}else{
				$url = $ep['link'];
			}
			$location = $ep['where'];
			$title = $ep['title'];
			$content = $ep['description'];
			
			$st = date(t('h:i a'),strtotime($ep['starttime']));
			$et = date(t('h:i a'),strtotime($ep['endtime']));
								
			if($ep['allday'] == 1 || $st[1] == $et[1]){
				$allday_text =  true;
			}else{
				$allday_text = false;
			}
			
			$color = null;
			if($ep['color']){
				$color = $ep['color'];
			}
			$time = $ep['datetime'];
			$event_item = array(
				'id' => $id,
				'title'=> $title,
				'allDay' => $allday_text,
				'start'=> $ep['starttime'],
				'end' => $ep['endtime'],
				'color' => $color,
				'url' => $url,
				'description' => $content
			);
			
			array_push($events_array,$event_item);
			
		}else{
	  		$date_array = $dth->translate_from_string($date_string);
	  		
	  		Loader::model('event_item','proevents');
			$event_item = new EventItem($ep,$date_string);
	  		
	  		$eID = $date_array['eID'];
	  		$date = $date_array['date'];
	  		$stime = $date_array['start'];
	  		$etime = $date_array['end'];
	  		
	  		$id = $ep->getCollectionID();
	  			
	  		$title =  $ep->getCollectionName();
	  		
			$url = $nh->getLinkToCollection($ep).'?eID='.$eID;
			if ($ep->getCollectionAttributeValue('exclude_nav')) {
				$url = '';
			}
			
			$content =  $event_item->getEventDescription();
			if($controller->truncateSummaries){
				$content = substr($content,0,$controller->truncateChars).'...';
			}
			
			$exclude = explode(':^:',$ep->getAttribute('event_exclude'));
			sort($exclude);
	
			$location = $ep->getAttribute('event_local');
			
			$color = $ep->getAttribute('category_color');
			
			loader::model("attribute/categories/collection");
			$akrr = CollectionAttributeKey::getByHandle('event_recur');
			$recur = $ep->getCollectionAttributeValue($akrr)->current()->value;
			
			$allday = $ep->getAttribute('event_allday');
			
			$dates = $dth->translate_from($ep);
			$thru = $location = $ep->getAttribute('event_thru');
			if($dates[1]['date']){
				$from = $dates[1]['date'];
			}else{
				$from = $date_origin;
			}
			$to = date('Y-m-d',strtotime($thru));
			
			
			if($recur == 'daily' && !in_array($id,$recured_array)){
	
				array_push($recured_array,$id);
				
				if($allday == 1){
					$allday_text =  true;
				}else{
					$allday_text = false;
				}
				
				if($exclude[0]){
					foreach($exclude as $exend){
						if($exend != date('Y-m-d',strtotime('+1 day',strtotime($lastnode))) || !$lastnode){
							if(date('Y-m-d',strtotime($exend)) > $date && date('Y-m-d',strtotime($exend)) < $date2){
								$exto = date('Y-m-d',strtotime('-1 day',strtotime($exend)));
								if(!$exfrom){
									$exfrom = $from;
								}
								$ex++;
		
								$event_item = array(
									'id' => $id.'_'.$ex,
									'title'=> $title,
									'allDay' => $allday_text,
									'start'=> $exfrom.' '.date('H:i',strtotime($stime)).':00',
									'end' => $exto.' '.date('H:i',strtotime($etime)).':00',
									'color' => $color,
									'url' => $url,
									'description' => $content
								);
								
								array_push($events_array,$event_item);
								
								$exfrom = date('Y-m-d',strtotime('+1 day',strtotime($exend)));
								
								$lastnode = $exend;
							}
						}else{
							$exfrom = date('Y-m-d',strtotime('+1 day',strtotime($exend)));
							$lastnode = $exend;
						}
					}
					if(!$exfrom){
						$exfrom = $from;
					}
					$event_item = array(
						'id' => $id,
						'title'=> $title,
						'allDay' => $allday_text,
						'start'=> $exfrom.' '.date('H:i',strtotime($stime)).':00',
						'end' => $to.' '.date('H:i',strtotime($etime)).':00',
						'color' => $color,
						'url' => $url,
						'description' => $content
					);
					
					array_push($events_array,$event_item);
			
					
				}else{
					$event_item = array(
						'id' => $id,
						'title'=> $title.' - '.$exclude[0],
						'allDay' => $allday_text,
						'start'=> $from.' '.date('H:i',strtotime($stime)).':00',
						'end' => $to.' '.date('H:i',strtotime($etime)).':00',
						'color' => $color,
						'url' => $url,
						'description' => $content
					);
					
					array_push($events_array,$event_item);
				}
				
			}elseif(!in_array($id,$recured_array)){
				
				if($allday == 1){
					$allday_text =  true;
				}else{
					$allday_text = false;
				}
				
				$event_item = array(
					'id' => $id,
					'title'=> $title,
					'allDay' => $allday_text,
					'start'=> $date.' '.date('H:i',strtotime($stime)).':00',
					'end' => $date.' '.date('H:i',strtotime($etime)).':00',
					'color' => $color,
					'url' => $url
				);
				
				if($content){
					$event_item['description'] = $content;
				}
				
				array_push($events_array,$event_item);
			
			}
		}
	}

   echo json_encode($events_array);


?>