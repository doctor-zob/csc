<?php     
	defined('C5_EXECUTE') or die(_("Access Denied."));
	Loader::model('event_item','proevents');
	$eventify = Loader::helper('eventify','proevents');
	global $c;
	$vars = $eventify->getGoogleEventVars($_REQUEST);
	//var_dump($vars);
	extract($vars);
	
	if($endTime){
		$startTime = date(t('g:i a'),strtotime($startTime));
		$endTime = date(t('g:i a'),strtotime($endTime));
	}else{
    	$thru = true;
	}
	
	if($startTime == $endTime){
		$allday = true;
	}
	//extract($settings);
	?>
	<?php    
	if($u->isLoggedIn() && $invites ==1){
	?>
	<div class="ccm-ui">
		<a href="<?php    echo Loader::helper('concrete/urls')->getToolsURL('invite_dialog.php','proevents');?>?ccID=<?php echo $c->getCollectionID()?>&uID=<?php echo $u->getUserID()?>" id="event_invite" alt="event_envite" class="dialog-launch btn info invite" dialog-width="300" dialog-height="120" dialog-modal="true" dialog-title="Event Invite" dialog-on-close="" onClick="javascript:;"><?php   echo t('Invite Others');?></a>
	</div>
	<?php    
	}
	?>

	<div class="event-attributes">
		<div>
			<h2><?php     echo $eventTitle; ?> </h2>
			<div class="date-times">
			<?php    
			$fromDate = date(t('M dS '),strtotime($eventDate)) ;
			echo $fromDate ;
			if($thru){
				$toDate = date(t('M dS '),strtotime($endTime));
				if($toDate != $fromDate){
					echo ' - '.$toDate ;
				}
			}
			if($allday){
    			echo '<br/> All Day';
			}else{
				echo ': '.$startTime. ' - ' .$endTime;
			}
			?> 
			</div>
			<div class="date-social">
			<?php    
			if($tweets){
			?>
			<span class='st_twitter_hcount' displayText='Tweet'></span>
			<?php    
			}
			if($fb_like){
			?>
			<span class='st_facebook_hcount' displayText='Facebook'></span>
			<?php    
			}
			?>
			<script type="text/javascript">var switchTo5x=true;</script>
			<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
			<script type="text/javascript">stLight.options({publisher:"<?php     echo $sharethis_key;?>"});</script>
			</div>
			<h5><?php     echo $event_local; ?>
			<?php     if($event_local && $address){?> - <?php     } echo $address; ?>
			</h5>
			<div id="deswrap">
				<div id="description">
				<?php     
				print $eventContent;
				?>
				</div>
			</div>
			<div id="eventfoot">
			<?php     if ($contact_email !=''){ echo '<a href="mailto:'.$contact_email.'">'.$contact_name.'</a>'; }?> <?php    if($contact_email && $address){ echo '|| ';}?> <?php    if($address !=''){ ?><a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;&saddr=<?php     echo $address ;?>" target="_blank"> <?php     echo t('get directions')?></a> <?php     } ?>
			</div>
			<div id="tags">
			<?php     
			if (!empty($url)){
			
			echo '<i><u><a href="'.$url.'">'.t('View Google Event').'</a></u></i>';
			
			}
			?>
			</div>
		</div>
	</div>
	<?php    
	if($u->isLoggedIn() || $invites){ ?>
		<script type="text/javascript">
		/*<![CDATA[*/
		$(document).ready(function(){
			$('#event_invite').dialog();
		});
		/*]]>*/
		</script>
	<?php  } ?>
	