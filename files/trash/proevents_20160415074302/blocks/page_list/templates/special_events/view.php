<?php    
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$textHelper = Loader::helper("text"); 
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
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php    
	//first we are going to loop through the original page array and
	//update all event pages publishdate to the closest publish date
	//withing the multidate array
	for ($i = 0; $i < count($cArray); $i++ ) {
			$cobj = $cArray[$i]; 
			if($cobj->getCollectionDatePublic('Y-m-d') >= date('Y-m-d')){
				$db = Loader::db();
				$cID = $cobj->getCollectionID();
				$date = null;
				$date = $db->GetOne("SELECT date FROM btProEventDates WHERE eventID = ? AND date >= CURDATE() ORDER BY date ASC",array($cID));
				if($date){
					$data = array('cDatePublic' => $date);
					$cobj->update($data);
				}
				
				$ccArray[] = $cobj;
			}
	}
	
	
	//now we go re-grab the page array from
	//the pagelist block controller
	//$ccArray = $controller->getPages();
	
	for ($i = 0; $i < count($ccArray); $i++ ) {
		$cobj = $ccArray[$i]; 
		//Grab the end date of the event recur loo
		$evth = CollectionAttributeKey::getByHandle('event_thru');
		$event_end = date(t('Y-m-d'),strtotime($cobj->getCollectionAttributeValue($evth)));
		
		//Grab the recurring type
		$evrc = CollectionAttributeKey::getByHandle('event_recur');
		$recur = $cobj->getCollectionAttributeValue($evrc);
		if($recur == ''){
			//if recur is none, then set the thru date to the public date
			$event_end = $cobj->getCollectionDatePublic(t('Y-m-d')); 
		}
			
		//if($event_end > date('Y-m-d')){
			$title = $cobj->getCollectionName(); 
			$description = $cobj->getCollectionDescription(); 
			$event_start = $cobj->getCollectionDatePublic(t('Y-m-d')); 
			
			////////////////////////////////////////
			//here we load the multidate helper
			//then go grab the original dateset detials broken
			//out into an array.  Then we go grab the next
			//X number of dates AFTER today (based on the count within the date set array)
			////////////////////////////////////////
			
			$proEventsDateHelper = Loader::helper('form/date_time_time','proevents');
			
			$dates_array = $proEventsDateHelper->translate_from($cobj);
			$n = count($dates_array);
			
			$eventify = Loader::helper('eventify','proevents');
			$next_dates_array = $eventify->getNextNumDates($cobj->cID,$n);
			
			$date_thru = $cobj->getAttribute('event_thru');
			
			$starttime = $dates_array[1]['start'];
			$endtime = $dates_array[1]['end'];
			
			//is this event an "all day"?
			$allday = $cobj->getAttribute('event_allday');
			//event location
			$location = $cobj->getAttribute('event_local');
			//actual event address
			$address = $cobj->getAttribute('address');
			//event point of contact
			$contact_name = $cobj->getAttribute('contact_name');			
			//event point of contact email address
			$cEmail = $cobj->getAttribute('contact_email');
			//category
			$category = $cobj->getAttribute('event_category');
			//category color
			$color = $cobj->getAttribute('category_color');
			
			
			//event thumbnail
			$imgHelper = Loader::helper('image'); 
			$imageF = $cobj->getAttribute('thumbnail');
			if (isset($imageF)) { 
	    		$image = $imgHelper->getThumbnail($imageF, 150,90)->src; 
			} 
		?>
		<div class="smallcal">
			<div class="calwrap">
				<div class="img">
					<div class="month">
						<?php    
						$date_month = date(t('M'), strtotime($event_start)) ; 
						echo $months[$date_month];
						?>
					</div>
					<div class="day">
						<?php    
						///////////////////////////////////////////////////
						//If the next_dates_array is a valid array, then
						//we loop through them and pull just the "date" (no time)
						//into a new array called dates_prem. If it's not,
						//then that means this is a single date, and we assign
						//the publish date to the dates_prem as a single item
						//array.
						///////////////////////////////////////////////////
						
						$date_prem = array();
						if(is_array($next_dates_array)){
							foreach($next_dates_array as $next_date){
								$date = explode(':^:',$next_date);
								$date_prem[] = date(t('M dS '),strtotime($date[0]));
								
							}
						}else{
						$date_prem = array($event_start);
						}
						
						
						///////////////////////////////////////////////////
						//now we cheeck for recur status to determine 
						//how we display the date. single or staggered
						///////////////////////////////////////////////////
						
						if($recur == ''){
							echo '<span class="big_date">';
							echo date('d', strtotime($date_prem[0])) ; 
							echo '</span>';
						}else{
							echo '<span class="small_date">';
							echo date('d', strtotime($date_prem[0])) ; 
							echo '</span>';
						}
						if($recur != ''){
							echo '<div class="subdate">';
							$date_loop_month = date('M', strtotime($date_prem[($n-1)]));
							echo '<span>'.$months[$date_loop_month].'</span>';
							echo '</br>';
							echo date('d', strtotime($date_thru));
							echo '</div>';
						}
						?>
					</div>
				</div>
			</div>
			<div class="infowrap">
				<div class="titlehead">
					<div class="title">
					<?php    
						echo '<a href="'.$nh->getLinkToCollection($cobj).'">'.$title.' </a> ';
					?>
					</div>
				   	<div class="local">
				   		<?php    
						if($color){
							print '<div style="background-color: '.$color.';" class="category_color">'.$category.'</div>';
						}
						?>
						<?php    echo $location ; ?>
					</div>
					<div class="time">
						<?php    echo date(t('g:i a'),strtotime($starttime))?> - <?php    echo date(t('g:i a'),strtotime($endtime))?>
						<?php    
						if($recur != ''){
							echo ' ::  '.t('Recurring').' '.$recur;
						}	
						?>
					</div>
				</div>
				<div class="description">
					<?php    echo $description; ?>
				</div>
			</div>
		</div>
		<br class="clearfloat" />
<?php    
	unset($image);
	} 
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="rssIcon" style="clear: both;">
				<a href="<?php    echo $rssUrl?>" target="_blank"><img src="<?php    echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
				
			</div>
			<link href="<?php    echo $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php    echo $controller->rssTitle?>" />
		<?php    
	}
	?>
</div>
<?php    
} 
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>