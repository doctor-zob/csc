<?php        
defined('C5_EXECUTE') or die(_("Access Denied."));

class GoogleMerge Extends Model{

	var $events_array = array();
	var $i;
		
	function __construct($bID,$xml_feeds=null,$date=null,$date2=null,$dateFormat='l jS \of F Y - h:i A'){
	
		$this->i = rand(0,2000000);
		
		$this->dateformat = $dateFormat;
		
		Loader::model('block');
		$b = Block::getByID($bID);
		$controller = $b->getController();
		if($_REQUEST['ctID'] != ''){
			$ctID = $_REQUEST['ctID'];
			$controller->ctID = $ctID;
		}

		$this->parseEventsToArray($b,$date,$date2);
		
		$feeds = $this->getFeeds($b,$xml_feeds,$date);
		
		
		if($feeds){
			foreach($feeds as $feed){
				$this->parseFeedToArray($feed);
			}
		}

		$this->mergeFeeds();
	}
	
	function getFeeds($b,$xml_feeds=null,$date=null){
		if(!$date){
			$date = date('Y-m-d');
		}
		
		$controller = $b->getController();
		if($_REQUEST['ctID'] != ''){
			$ctID = $_REQUEST['ctID'];
			$controller->ctID = $ctID;
		}else{
			$ctID = $controller->ctID;
		}
		
		if($xml_feeds){
			$template = strtolower($b->getBlockFilename());
			
			//automatically determin span of dates to retrieve based on themplate name
			if(substr_count($template,'day') > 0){
				$span = date('Y-m-d\T',strtotime('+1 day',strtotime(date('Ymd',strtotime($date)))));
			}elseif(substr_count($template,'week') > 0){
				$span = date('Y-m-d\T',strtotime('+1 week',strtotime(date('Ymd',strtotime($date)))));
			}else{
				$span = date('Y-m-d\T',strtotime('+1 month',strtotime(date('Ymd',strtotime($date)))));
			}
		
			$feeds = explode(':^:',$xml_feeds);
			
			$ctIDarray = explode(",", str_replace(' ','%20',str_replace(', ',',',$ctID)));
			
			foreach($feeds as $feed_data){
				foreach($ctIDarray as $ctIDstring){
					if(strpos($feed_data, str_replace(' ','%20',$ctIDstring)) > 0 || $ctID == 'All Categories'){
						$feed_data = str_replace('#',urlencode('#'),$feed_data);
						if($feed_data != ''){
							$rArray[] = $feed_data.'&start-max='.$span.'00:01:00-05:00&start-min='.date('Y-m-d\T',strtotime($date)).'00:01:00-05:00';
						}
					}
				}
			}
		}
		
		return $rArray;
	}
	
	function parseFeedToArray($feed,$date=null){
	
		$dateFormat = $this->dateformat;
		
		$url = $feed;
		
		$vars_array = explode('&',$feed);
		$vars = array();
		if(is_array($vars_array)){
			foreach($vars_array as $var){
				$var_set = explode('=',$var);
				$vars[$var_set[0]] = $var_set[1];
			}
		}
		
		$feed_array = $this->events_array;
		
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 4); // times out after 4s
		$result = curl_exec($ch); // run the whole process
		//$feed_array[] = json_decode(json_encode((array) simplexml_load_string($result)),1);

		//if(!is_string($result)){
			$s = new SimpleXMLElement($result); 

		    foreach ($s->entry as $item) {
		    	
		    	//var_dump(sprintf("%s",$item->content));
		    	//exit;
		    	
		   	 	$gd = $item->children('http://schemas.google.com/g/2005');
		   	 	
		   	 	
		   	 	$startTime = '';

		   	 	if(is_object( $gd->originalEvent->when )){
			   	 	$startTime = sprintf($gd->originalEvent->when->attributes()->startTime);
		   	 	}elseif ( is_object($gd->when) ){
		            $startTime = sprintf($gd->when->attributes()->startTime);
		        }elseif ( $gd->recurrence ){
		            var_dump($gd);exit;
		        	//DTSTART;TZID=America/Grand_Turk:20120312T151000 DTEND;
		        	// 1-find text bweteen DTSTART <-> DTEND
		        	// 2-explode by ':' , second part of array is time stamp [1]
		        	// 3-wipe spaces
		        	if(preg_match_all('~DTSTART(.*?)DTEND~s',$gd->recurrence,$match)) {            
					        $m = explode(':',$match[1][0]);    
					        //var_dump($m[1]); 
					        $startTime = str_replace(' ','',$m[1]);    
					}
		            //$startTime = $gd->recurrence->when->attributes()->startTime; 
		        } 

		        $startTime = explode('T',$startTime);
		        $date = $startTime[0];
		        $startTime = explode('.',$startTime[1]);
		        $startTime = $date.' '.$startTime[0];

		        $endTime = explode('T',sprintf($gd->when->attributes()->endTime));
		        $date2 = $endTime[0];
		        $endTime = explode('.',$endTime[1]);
		        $endTime = $date2.' '.$endTime[0];

		        $d1 = strtotime($startTime);
		        $d2 = strtotime($endTime);
		        
		        $span = round(($d2 - $d1)/84600);
		        
		        if(!$date){
		        	$date = date('Ym').'01';
		        }else{
		        	$date = date('Ymd',strtotime($date));
		        }
		        
		        
		        $span_dates = array();
		        	
		        if($span > 1){
					for($it=0;$it<=$span;$it++){
						$span_dates[] = date('Y-m-d',strtotime('+'.$it.' days',strtotime($startTime)));
					}
				}else{
					$span_dates = array($startTime);
				}

				
		        foreach($span_dates as $date_spanned){
		        	
			        //if(date('Ymd',strtotime($startTime)) >= $date_spanned){
			        
			       		$this->i += 1;
			        
						//$eventID = substr($id,strrpos($id, '/_')+2);
						
				        //$key = date('YmdHis',strtotime($startTime)).date('YmdHis',strtotime($endTime));
				        $key = $this->i;	
				        
				        $id = sprintf("%s",$item->id);
				        
				        $feed_array[$key]['ID'] = urlencode($id);
				        
				        $feed_array[$key]['eventDate'] = date('Y-m-d', strtotime($date_spanned) );
				
						$feed_array[$key]['starttime'] = $startTime;
						
						$feed_array[$key]['endtime'] = $endTime;
						
						$feed_array[$key]['title'] = sprintf("%s",$item->title);
						
						$feed_array[$key]['description'] = sprintf("%s",$item->content);
						
						$nh = Loader::helper('navigation');
						$google_event = Page::getByPath('/google-event');
		
						$url = $nh->getLinkToCollection($google_event).'?1=1&startTime='.$startTime.'&event='.$id;
						$feed_array[$key]['link'] =  $url;
						
						$feed_array[$key]['category'] =  sprintf("%s",$item->category->attributes()->term);
				
				        if ( $gd->where ) {
				        	$feed_array[$key]['where'] = sprintf("%s",$gd->where->attributes()->valueString); 
				        }
				        
				        if($vars['color']){
				        	$feed_array[$key]['color'] = str_replace('%23','#',$vars['color']);
				        }
				    //}
			    }
		   // }
		}
       
	    $this->events_array = $feed_array;
	}
	
	function parseEventsToArray($b,$date=null,$date2=null){

		$dateFormat = $this->dateformat;
		$dth = Loader::helper('form/date_time_time','proevents');
		$nh = Loader::helper('navigation');
		$controller = $b->getController();
		if(!$date){
			 $date = date('Y-m-d');
		}
		
		if(!$date2){
			$year = date('Y',strtotime($date));
			$month = date('m',strtotime($date));
			$days_in_month = cal_days_in_month(0, $month, $year) ; 
			
			$date2 = $year.'-'.$month.'-'.$days_in_month;
		}
		
		//var_dump($date.' - '.$date2);
		
		if($_REQUEST['ctID'] != ''){
			$ctID = $_REQUEST['ctID'];
			$controller->ctID = $ctID;
		}
		
		$events = $controller->getEvents($date,$date2);
		
		$feed_array = $this->feeds_array;
		
		if($events){
			foreach($events as $date_string=>$e){
				$this->i += 1;
				$location = $e->getAttribute('event_local');
				$date_array = $dth->translate_from_string($date_string);
				$eID = $date_array['eID'];
				$startTime = date('Ymd',strtotime($date_array['date'])).'T'.date('His',strtotime($date_array['start']));
				$endTime = date('Ymd',strtotime($date_array['date'])).'T'.date('His',strtotime($date_array['end']));
				$key = $this->i;
				$feed_array[$key]['eventDate'] = date('Y-m-d', strtotime($startTime) );
				$feed_array[$key]['starttime'] = $startTime;
				$feed_array[$key]['endtime'] = $endTime;
				$feed_array[$key]['title'] = $e->getCollectionName();
				$feed_array[$key]['description'] = $e->getCollectionDescription();
				$feed_array[$key]['link'] = $nh->getLinkToCollection($e).'?eID='.$eID;
				$feed_array[$key]['color'] = $e->getAttribute('category_color');
				$feed_array[$key]['allday'] = $e->getAttribute('event_allday');
				if ( $location ) {
		        	$feed_array[$key]['where'] = $location; 
		        }
			}

			$this->events_array = $feed_array;
		}
	}
	
	function mergeFeeds(){
		ksort($this->events_array);
	}
	
	function getEventsArray(){
		return $this->events_array;
	}
}