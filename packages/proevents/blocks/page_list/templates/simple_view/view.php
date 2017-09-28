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
	$eventify = Loader::helper('eventify','proevents');
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php    
	//first we are going to loop through the original page array and
	//update all event pages publishdate to the closest publish date
	//within the multidate array
	//why is this here instead of the controller? because ProEvents can not modify
	//the standard C5 page_list block controller.  instead, we bump the publish date
	//here to prevent a "hack job"
	for ($i = 0; $i < count($cArray); $i++ ) {
			$cobj = $cArray[$i]; 
			$db = Loader::db();
			$cID = $cobj->getCollectionID();
			$date = null;
			$date = $db->GetOne("SELECT date FROM btProEventDates WHERE eventID = ? AND date >= CURDATE() ORDER BY date ASC",array($cID));
			if($date){
				$data = array('cDatePublic' => $date);
				$cobj->update($data);
			}
	}
	
	
	//now we go re-grab the page array from
	//the pagelist block controller
	$ccArray = $controller->getPages();
	
	for ($i = 0; $i < count($ccArray); $i++ ) {
		$cobj = $ccArray[$i]; 

		//Grab the end date of the event recur loop
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
			//then go grab the original dateset details broken
			//out into an array.  Then we go grab the next
			//X number of dates AFTER today (based on the count within the date set array)
			////////////////////////////////////////
			
			$proEventsDateHelper = Loader::helper('form/date_time_time','proevents');
			
			$dates_array = $proEventsDateHelper->translate_from($cobj);
			$n = count($dates_array);
	
			$next_dates_array = $eventify->getNextNumDates($cobj->cID,$n);
			
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
			<div class="simplecal">
				<div class="img">
					<div class="month">
						<?php  
						$date_month = date(t('M'), strtotime($event_start)) ; 
						echo $months[$date_month];
						?>
					</div>
					<div class="day">
						<?php    echo date('d', strtotime($event_start)) ; ?>
					</div>
				</div>
				<div class="titlehead">
					<div class="title">
						<a href="<?php    echo $nh->getLinkToCollection($cobj)?>"><?php    echo $title ; ?></a>
					</div>
					<div class="local">
					<?php    
					if($color){
						print '<div style="background-color: '.$color.';" class="category_color">'.$category.'</div>';
					}
					?>
						<?php    echo $location ; ?>
					</div>
				</div>
				<div class="datespan">
				<?php    
				////////////////////////////////////////////////
				//Loop through our next available dates within
				//the date set after TODAY.
				////////////////////////////////////////////////
				if(is_array($next_dates_array)){
					foreach($next_dates_array as $next_date){
						$date = explode(':^:',$next_date);
						$month_loop_date = date('M',strtotime($date[0]));
						echo $montsh[$month_loop_date];
						echo date(t(' dS '),strtotime($date[0]));
						if ($allday !=1){
						 	echo date(t('g:i a'),strtotime($date[1])).' - '.date(t('g:i a'),strtotime($date[2])).'<br/>';
						}else{
							echo t('All Day');
						}
					}
				}
				?>
				</div>
				<div class="time">
				<?php    
						
				?>
				</div>
				<div class="deswrap">
					<div class="description">
						<?php    
						if($image){
							///////////////////////////////////////////////
							// UNCOMMENT THIS CODE TO SHOW THE THUMBNAIL //

							//echo '<img src="'.$image.'"/>';
						}
						?>
						<?php    echo $description?>
						<br class="clearfloat" />
					<!-- UNCOMMENT THIS CODE TO SHOW TAGS
						<div class="tags">
						<?php    echo t('Tags')?>: <i>
						<?php    
							$ak_t = CollectionAttributeKey::getByHandle('event_tag'); 
							$tag_list = $cobj->getCollectionAttributeValue($ak_t);
							$akc = $ak_t->getController();
			
							if(method_exists($akc, 'getOptionUsageArray') && $tag_list){
								//$tags == $tag_list->getOptions();
						
									foreach($tag_list as $akct){
										$qs = $akc->field('atSelectOptionID') . '[]=' . $akct->getSelectAttributeOptionID();
										echo '<a href="'.BASE_URL.$search.'?'.$qs.'">'.$akct->getSelectAttributeOptionValue().'</a>&nbsp;&nbsp;';
											
									}
								
							}
						?>
						</i>
						</div>
						<br class="clearfloat" />
					-->
					</div>
				</div>
				<br class="clearfloat" />
				<div class="eventfoot">
				<?php    
				if ($cEmail !=''){ ?>
					<a href="mailto:<?php    echo $cEmail ;?>"><?php    echo t('contact: ');?><?php    echo $contact_name?></a>
					<?php    } if ($cEmail !='' && $address !=''){ ?> || <?php    } if($address !=''){ ?><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php    echo $address ;?>" target="_blank"> <?php  echo t('get directions')?></a> <?php    } ?>
				</div>
				<br class="clearfloat" />
			</div>
		<br class="clearfloat" />
<?php     //}
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