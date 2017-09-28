<h1 class="upcoming_events"><?php  echo $rssTitle?></h1>
<?php    
if (count($eArray) > 0) { 

	foreach($eArray as $date_string=>$event) {

		extract($eventify->getEventListVars($date_string,$event));
		?>
		<div class="event_super_clean">
			<h5 class="event_title">
			<?php  
			echo date(t('m.d'), strtotime($date));
			echo ' - ';
		 	echo '<a href="'.$url;
		 	if(!$grouped){ echo '?eID='.$eID;}
			echo '">'.$title.'</a>' ; 	
			?>
			</h5>
			<div class="event_times">
			<?php     
	    	if(is_array($next_dates_array)){
				foreach($next_dates_array as $next_date){
					if ($allday !=1){
					 	echo date(t('g:i a'),strtotime($next_date->start)).' - '.date(t('g:i a'),strtotime($next_date->end)).'<br/>';
					}else{
						echo ' - '.t('All Day').'<br/>';
					}
				}
			}
	      	?>
			</div>
			<?php   if($address){ ?>
			<div class="event_local">
				<a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php    echo $address ;?>" target="_blank"> <?php  echo t('get directions')?></a>
			</div>
			<?php   } ?>
		</div>
		<?php    			
		}
	
		if($showfeed==1){
			?>
		   	<div class="iCal">
				<p><img src="<?php     echo $rss_img_url ;?>" width="25" alt="iCal feed" />&nbsp;&nbsp;
				<a href="<?php     echo $rss_url;?>?ctID=<?php    echo $ctID ;?>&bID=<?php    echo $bID ; ?>&ordering=<?php    echo $ordering ;?>" id="getFeed">
				<?php     echo t('get iCal link');?></a></p>
				<link href="<?php     echo $rss_url;?>" rel="alternate" type="application/rss+xml" title="<?php     echo t('RSS');?>" />
			</div>
			<?php    
		}	
	
}else{
	echo '<p>'.$nonelistmsg.'</p>';
}

	
if ($isPaged && $num > 0 && is_object($el)) {
	if(count($eArray)>0){
		$el->displayPaging();
	}
}