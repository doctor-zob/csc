<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
extract($settings);	
global $c;
?>
	<h1><?php    echo $rssTitle?></h1>

	<div class="ccm-page-list">
	<?php    
	if (count($eArray) > 0) { 
	
	foreach($eArray as $date_string=>$event) {
			  		
			  		extract($eventify->getEventListVars($date_string,$event));
			  		
					//###################################################################################//
					//here we lay out they way the page looks, html with all our vars fed to it	     //
					//this is the content that displays.  it is recommended not to edit anything beyond  //
					//the content parse.  Feel free to structure and re-arrange any element and adjust   //
					//CSS as desired.							 	     //
					//###################################################################################//
					
					if($date != $dateP){
					?>
					<div class="smallcal">
						<div class="calwrap">
							<div class="img">
								<div class="month">
									<?php    
									$date_month = date('M', strtotime($date)) ; 
									echo $months[$date_month];
									?>
								</div>
								<div class="day">
									<?php    echo date('d', strtotime($date)) ; ?>
								</div>
							</div>
						</div>
						<div class="infowrap">
							<div class="titlehead">
								<?php    
								if($color){
									print '<div style="background-color: '.$color.';" class="category_color">'.$category.'</div>';
								}
								?>
								<div class="title">
								<?php    
									echo '<a href="'.$url.'?eID='.$eID.'">'.$title.'</a>' ; 	
								?>
								</div>
							   	<div class="local">
									<?php    echo $location ; ?>
								</div>
								<div class="time">
									<?php     
									if(is_array($next_dates_array)){
										foreach($next_dates_array as $next_date){
											echo date(t('M dS '),strtotime($next_date->date));
											if($recur=='daily' && $grouped){
						            			echo t(' - ').date(t('M dS '),strtotime($thru));
						            		}
											if ($allday !=1){
											 	echo date(t('g:i a'),strtotime($next_date->start)).' - '.date(t('g:i a'),strtotime($next_date->end)).'<br/>';
											}else{
												echo ' - '.t('All Day').'<br/>';
											}
										}
									}  
	                              	?>
								</div>
							</div>
							<div class="description">
								<?php    
								if($truncateChars){
									print  substr($content,0,$truncateChars).'.....';
								}else{
									print  $content;
								}
								?>
							</div>
						</div>
					</div>
					<?php    
					$dateP = $date;
					}else{
					?>
					<div class="smallcal sameday">
						<br class="clearfloat" />
						<div class="calwrap">
							
						</div>
						<div class="infowrap">
							<div class="titlehead">
								<div class="title">
								<?php    
									echo '<a href="'.$url.'">'.$title.'</a>' ; 	
								?>
								</div>
							   	<div class="local">
									<?php    echo $location ; ?>
								</div>
								<div class="time">
									<?php     
									if(is_array($next_dates_array)){
										foreach($next_dates_array as $next_date){
											echo date('M dS ',strtotime($next_date->date));
											if($recur=='daily' && $grouped){
						            			echo t(' - ').date('M dS ',strtotime($thru));
						            		}
											if ($allday !=1){
											 	echo date(t('g:i a'),strtotime($next_date->start)).' - '.date(t('g:i a'),strtotime($next_date->end)).'<br/>';
											}else{
												echo ' - '.t('All Day').'<br/>';
											}
										}
									}  
	                              	?>
								</div>
							</div>
							<div class="description">
								<?php    
								if($truncateChars){
									print  substr($content,0,$truncateChars).'.....';
								}else{
									print  $content;
								}
								?>
							</div>
						</div>
					</div>
					<?php    	 			
					//#####################################################################################//
					//this is the end of the recommended content area.  please do not edit below this line //
					//#####################################################################################//
					}
				}
		//is rss feed option is sellected, show it
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
	?>
	</div>
<?php     
	
if ($isPaged && $num > 0 && is_object($el)) {
	if(count($eArray)>0){
		$el->displayPaging();
	}
}
	
?>