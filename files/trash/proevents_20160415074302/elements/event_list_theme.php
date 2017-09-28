<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
$eventify = Loader::helper('eventify','proevents');
Loader::model('event_item','proevents');
$nh = Loader::helper('navigation');

$event_item = new EventItem($event,$date_string);

extract($eventify->getEventListVars($date_string,$event));

$imgHelper = Loader::helper('image'); 
$imageF = $event->getAttribute('thumbnail');
if (isset($imageF)) { 
	$image = $imgHelper->getThumbnail($imageF, 110,85)->src; 
} 

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
//###################################################################################//
//here we lay out they way the page looks, html with all our vars fed to it	     //
//this is the content that displays.  it is recommended not to edit anything beyond  //
//the content parse.  Feel free to structure and re-arrange any element and adjust   //
//CSS as desired.							 	     //
//###################################################################################//

if($joinDays){
?>
<div class="smallcal">
	<div class="calwrap">
		<div class="img">
			<div class="month">
				<?php    
				$date_month = date('M', strtotime($date	)) ; 
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
				if($imageF){
					echo '<div class="thumbnail">';
					echo '<img src="'.$image.'"/>';
					echo '</div>';
				}	
			?>
			<?php    
			if($truncateChars){
				print  substr($content,0,$truncateChars).'.....';
			}else{
				print  $content;
			}
			?>
		</div>
		<div class="eventfoot">
		<?php    
		if ($contact_email !=''){ ?>
			<a href="mailto:<?php    echo $contact_email ;?>"><?php    echo t('contact: ');?><?php    echo $contact_name?></a>
			<?php    } if ($contact_email !='' && $address !=''){ ?> || <?php    } if($address !=''){ ?><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php    echo $address ;?>" target="_blank"> <?php  echo t('get directions')?></a> <?php    } ?>
		</div>
	</div>
</div>
<?php    
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
				if($imageF){
					echo '<div class="thumbnail">';
					echo '<img src="'.$image.'"/>';
					echo '</div>';
				}	
			?>
			<?php    
			if($truncateChars){
				print  substr($content,0,$truncateChars).'.....';
			}else{
				print  $content;
			}
			?>
		</div>
		<div class="eventfoot">
		<?php    
		if ($contact_email !=''){ ?>
			<a href="mailto:<?php    echo $contact_email ;?>"><?php    echo t('contact: ');?><?php    echo $contact_name?></a>
			<?php    } if ($contact_email !='' && $address !=''){ ?> || <?php    } if($address !=''){ ?><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php    echo $address ;?>" target="_blank"> <?php  echo t('get directions')?></a> <?php    } ?>
		</div>
	</div>
</div>
<?php    	 			
//#####################################################################################//
//this is the end of the recommended content area.  please do not edit below this line //
//#####################################################################################//
}