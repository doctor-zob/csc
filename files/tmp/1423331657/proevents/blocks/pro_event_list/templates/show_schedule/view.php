<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
extract($settings);	
global $c;
	
	///////////////////////////////////////////////////////
	//This can be removed.  This is really here to explain
	//to newer users of C5 that they can easily change
	//how PE is displayed. One less support ticket!
	///////////////////////////////////////////////////////
	
	if ($c->isEditMode()) { 
		echo '<i style="color: orange;max-width: 400px;display: block;">'.t('Don\'t forget, you can quickly change this to Calendar view by clicking on this area, and selecting "Custom Template". <br/> Also, if you want to view date sets in "blocks" instead of a listing for each date, try using the standard Page List block and changing the Custom Template to the "Special Events" template.').'</i>';
	}
	
	///////////////////////////////////////////////////////
	///////////////////////////////////////////////////////
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
					
					?>
					<div class="smallcal">
						<div class="infowrap">
							<div class="titlehead">	
								
								<?php    
								if($color){
									print '<div style="background-color: '.$color.';" class="category_color">'.$category.'</div>';
								}
								?>
								<div class="title">
									<div class="time">
									<?php     
					            	if(is_array($next_dates_array)){
										foreach($next_dates_array as $next_date){
											echo date(t('M dS '),strtotime($next_date->date));
											if ($allday !=1){
											 	echo date(t('g:i a'),strtotime($next_date->start)).' - '.date(t('g:i a'),strtotime($next_date->end)).'<br/>';
											}else{
												echo ' - '.t('All Day').'<br/>';
											}
										}
									}
	                              	?>
									</div>
								<?php     echo date(t('d.m.y'), strtotime($date)) ; ?> - <?php    echo '<a href="'.$url.'?eID='.$eID.'">'.$title.'</a>' ; ?>
								</div>
							   	<div class="local">
									<?php   
									if($address !=''){ 
									?>
										<a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php    echo $address ;?>" target="_blank"> <?php  echo $location?></a> 
									<?php    
									}else{ 
										echo $location ; 
									} 
									?>
								</div>
							</div>
						</div>
					</div>
					<?php    			
					//#####################################################################################//
					//this is the end of the recommended content area.  please do not edit below this line //
					//#####################################################################################//

				}
		//is iCal feed option is sellected, show it
		if($showfeed==1){
			?>
		   	<div class="iCal">
				<p><img src="<?php     echo $ical_img_url ;?>" width="25" alt="iCal feed" />&nbsp;&nbsp;
				<a href="<?php     echo $ical_url;?>?ctID=<?php    echo $ctID ;?>&bID=<?php    echo $bID ; ?>&ordering=<?php    echo $ordering ;?>" id="getFeed">
				<?php     echo t('get iCal link');?></a></p>
				<link href="<?php     echo $ical_url;?>" rel="alternate" type="application/rss+xml" title="<?php     echo t('RSS');?>" />
			</div>
    		<?php    
		}	
	}else{
		echo '<p>'.$nonelistmsg.'</p>';
	}
	?>
	</div>
	<br style="clear: both;"/>
<?php    
	
if ($isPaged && $num > 0 && is_object($el)) {
	if(count($eArray)>0){
		$el->displayPaging();
	}
}
	
?>