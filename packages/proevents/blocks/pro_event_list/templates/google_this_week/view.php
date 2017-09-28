<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('google_merge','proevents');
$gm = new GoogleMerge($bID,$settings['xml_feeds']);
$events = $gm->getEventsArray();

foreach($events as $key=>$event){
	$n++;
	$datetime = explode('_',$key);
	$datetime = $datetime[0];
	?>
	<div class="smallcal">
		<div class="calwrap">
			<div class="img">
				<div class="month" <?php   if($event['color']){echo 'style="background-color: '.$event['color'].'!important;"';}?>>
					<?php     echo date('M', strtotime($datetime)) ; ?>
				</div>
				<div class="day">
					<?php     echo date('d', strtotime($datetime)) ; ?>
				</div>
			</div>
		</div>
		<div class="infowrap">
			<div class="titlehead">	
				<div class="title">
				<?php    
					echo '<a href="'.$event['link'].'" target="_blank">'.$event['title'].'</a>' ; 	
				?>
				</div>
			   	<div class="local">
					<?php     echo $event['where'] ; ?>
				</div>
				<div class="time">
					<?php     
                    print $event['datetime'] ; 
                  	?>
				</div>
			</div>
			<div class="description">
				<?php    
				if($truncateChars){
					print  substr($event['description'],0,$truncateChars);
					if(strlen(substr($event['description'],0,$truncateChars)) > 0){
						print '…';
					}
				}else{
					print  $event['description'];
				}
				?>
			</div>
		</div>
	</div>
	<?php  
	if($num){
		if($n == $num){
			break;
		}
	}
}
?>
