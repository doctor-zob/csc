<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
?>
<style type="text/css">
	.help {font-style: normal; font-weight: normal; border-color: #02890d; border-width: 1px; border-style: solid; max-width: 235px; padding: 16px; MARGIN-left: 85px; background-color: #f5f5f5; position: absolute;-moz-border-radius: 5px; /* this works only in camino/firefox */-webkit-border-radius: 5px; /* this is just for Safari */}
	#dates_wrap div{margin-top: 12px; }
	.small {width: 52px!important;}
	strong label {width: 120px!important; float: left; padding: 10px 0 0 40px!important;}
	.entry-form td {padding: 6px!important;}
	.allday_form{padding-top: 12px!important;}
	select{margin-bottom:  6px!important;}
</style>
	<?php     
	$df = Loader::helper('form/date_time');
	$dti = Loader::helper('form/time','proevents');
	Loader::model("attribute/categories/collection");
	$settings = $eventify->getSettings();
	if (is_object($event)) { 
		$eventTitle = $event->getCollectionName();
		$eventDescription = $event->getCollectionDescription();
		$eventDate = $event->getCollectionDatePublic();
		$cParentID = $event->getCollectionParentID();
		$ctID = $event->getCollectionTypeID();
		$eventBody = '';
		$eb = $event->getBlocks('Main');
		foreach($eb as $b) {
			if($b->getBlockTypeHandle()=='content'){
				$eventBody = $b->getInstance()->getContent();
			}
		}
		$task = 'edit';
		$buttonText = t('Update Entry');
		$title = 'Update';
	} else {
		$task = 'add';
		$buttonText = t('Add Event Entry');
		$title = 'Add';
	}
	
	?><em></em>
<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t($title.' Event').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
<div class="ccm-pane-body">

<!--
	<ul class="breadcrumb">
	  <li><a href="/index.php/dashboard/proevents/list/">List</a> <span class="divider">|</span></li>
	  <li class="active">Add/Edit <span class="divider">|</span></li>
	  <li><a href="/index.php/dashboard/proevents/preview/monthly/">Preview</a> <span class="divider">|</span></li>
	  <li><a href="/index.php/dashboard/proevents/exclude_dates/">Exclude Dates</a> <span class="divider">|</span></li>
	  <li><a href="/index.php/dashboard/proevents/settings/">Settings</a></li>
	</ul>
-->

	<ul class="tabs">
		<li class="active"><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.info').show();"><?php   echo t('Info')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.post').show();"><?php   echo t('Post')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.dates').show();"><?php   echo t('Dates')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.exclude').show();"><?php   echo t('Exclude Dates')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.links').show();"><?php   echo t('Links')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.booking').show();"><?php   echo t('Booking')?></a>
		</li>
		<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.additional').show();"><?php   echo t('Additional Attributes')?></a>
		</li>
	</ul>

	<?php     if ($this->controller->getTask() == 'edit') { ?>
		<form method="post" action="<?php    echo $this->action($task,$event->getCollectionID())?>" id="event-form">
		<?php    echo $form->hidden('eventID', $event->getCollectionID())?>
	<?php     }else{ ?>
		<form method="post" action="<?php    echo $this->action($task)?>" id="event-form">
	<?php    } ?>
	<br style="clear: both;"/>
	<div id="proevent-tab-settings" class="pane info" style="display: block;">
		<table id="add_event" class="entry-form">
			<tr>
				<td  class="subheader">
					<div class="clearfix">
						<strong><?php    echo $form->label('eventTitle', t('Event Title'))?> *</strong>
						<div class="input">
						 <?php    echo $form->text('eventTitle', $eventTitle, array('style' => 'width: 230px'))?>
						</div>
					</div>
				</td>
				<td class="subheader"  colspan="1">
					<div class="clearfix">
						<strong><?php    echo $form->label('cParentID', t('Calendar'))?></strong>
						<div class="input">
						<?php     if (count($sections) == 0) { ?>
						<div><?php    echo t('No sections defined. Please create a page with the attribute "calendar" set to true.')?></div>
						<?php     } else { ?>
							<div><?php    echo $form->select('cParentID', $sections, $cParentID)?></div>
						<?php     } ?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="subheader"  colspan="1">
					<div class="clearfix">
						<strong><?php    echo $form->label('eventCategory', t('Category'))?></strong>
						<div class="input">
							<?php     
							$akct = CollectionAttributeKey::getByHandle('event_category');
							if (is_object($event)) {
								$tcvalue = $event->getAttributeValueObject($akct);
							}
							?>
							<div class="event-attributes">
								<div>
									<?php    echo $akct->render('form', $tcvalue, true);?>
								</div>
							</div>
						</div>
					</div>
				</td>
				<td class="subheader"  colspan="1">
					<div class="clearfix">
						<strong><?php    echo $form->label('category_color', t('Color'))?></strong>
						<div class="input">
							<?php     
							$akct = CollectionAttributeKey::getByHandle('category_color');
							if (is_object($event)) {
								$tcvalue = $event->getAttributeValueObject($akct);
							}
							?>
							<div class="event-attributes" style="position: absolute;">
								<div>
									<?php    echo $akct->render('form', $tcvalue, true);?>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td  class="subheader">
					<div class="clearfix">
						<strong><label><?php    echo t('Exclude Link to Event Item?')?></label></strong>
						<div class="input">
							<?php    
						
							$aknv = CollectionAttributeKey::getByHandle('exclude_nav');
							if (is_object($event)) {
								$nvvalue = $event->getAttributeValueObject($aknv);
							}
							?>
							<?php    
							
							echo $aknv->render('form', $nvvalue, 1, array('size'=>'50'));
				
							?>
							<img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('exclude_date');" onmouseout="hideHelp('exclude_date');"><div id="exclude_date" class="help" style="display: none;position: absolute;"><?php  echo t('This option turns event titles into non-linked titles.  This is also the same attribute for excluding event pages from navigation. Thus, if this attribute is set event pages will not show up in any navigation.')?></div>
						</div>
					</div>
				</td>
				<td  class="subheader">
					<div class="clearfix">
						<strong><?php    echo $form->label('ctID', t('Page Type'))?></strong>
						<div class="input">
							<?php    
								if(!$ctID){
									$ctID = $settings['ctID'];
								}
							?>
							<?php    echo $form->select('ctID', $pageTypes, $ctID)?>
						</div>
					</div>
				</td>
			</tr>
		</table>
		</div>
		
		<div id="proevent-tab-post" style="display: none" class="pane dates">
		<table id="add_event" class="entry-form">
			<tr>
				<td class="header">
					<strong><?php    echo t('Date Info')?></strong> <img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('date_set');" onmouseout="hideHelp('date_set');"><div id="date_set" class="help" style="display: none;"><?php  echo t('Add as many dates as you like here.  This grouping of dates represents all dates related to this event.')?></div>
				</td>
			</tr>
			<tr>
				<td>
					<?php    
						$svth = CollectionAttributeKey::getByHandle('event_multidate');
						if (is_object($event)) {
							$stvalue = $event->getAttributeValueObject($svth);
						}
					?>
					<div style="display: none;"><?php    echo $df->date('eventDate', $eventDate)?></div>
					<?php    echo $svth->render('form', $stvalue);?>	
				</td>
			</tr>
			<tr>
				<td  class="subheader">
					<div class="clearfix">
						<?php    
	
						$akad = CollectionAttributeKey::getByHandle('event_allday');
						if (is_object($event)) {
							$advalue = $event->getAttributeValueObject($akad);
						}
						?>
						<strong><?php    echo $akad->render('label');?></strong>
						<div class="input allday_form">
						<?php    echo $akad->render('form', $advalue, true);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>(<?php  echo t('times will be ignored if this is checked')?>)</i>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td  class="subheader">
					<div class="clearfix">
						<?php    
	
						$akad = CollectionAttributeKey::getByHandle('event_grouped');
						if (is_object($event)) {
							$advalue = $event->getAttributeValueObject($akad);
						}
						?>
						<strong><?php    echo $akad->render('label');?></strong>
						<div class="input grouped_form">
						<?php    echo $akad->render('form', $advalue, true);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>(<?php  echo t('multi-date items will be grouped')?>)</i>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<table id="add_event" class="entry-form">
			<tr>
				<td class="header" colspan="2">
					<strong><?php    echo t('Recurring Info')?></strong>
				</td>
			</tr>
			<tr>
				<td  class="subheader">
					<?php    
						$evth = CollectionAttributeKey::getByHandle('event_thru');
						if (is_object($event)) {
							$etvalue = $event->getAttributeValueObject($evth);
						}
						$akrr = CollectionAttributeKey::getByHandle('event_recur');
						if (is_object($event)) {
							$rrvalue = $event->getAttributeValueObject($akrr);
						}
					?>
					<div class="clearfix">
						<strong><?php    echo $evth->render('label');?></strong>
						<div class="input">
							<?php    echo $evth->render('form', $etvalue);?> <img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('recur_date');" onmouseout="hideHelp('recur_date');"><div id="recur_date" class="help" style="display: none;"><?php  echo t('This date field is used in tandem with the Recurring Option.  If recurring is set, this date marks the end of the recurring loop.')?></div>
							<script type="text/javascript">
								showHelp = function(v){
									$('#'+v).show();
								};
								
								hideHelp = function(v){
									$('#'+v).hide();
								};
							</script>
						</div>
					</div>
				</td>
				<td  class="subheader">
					<div class="clearfix">
						<strong><?php    echo $akrr->render('label');?></strong>
						<div class="input">
						 <?php    echo $akrr->render('form', $rrvalue, true);?> <img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('recur_setting');" onmouseout="hideHelp('recur_setting');"><div id="recur_setting" class="help" style="display: none;"><?php  echo t('The Recurring option takes a date "set", and loops those dates every day/week/month until the end Recurring End Date.')?></div>
							<br/><i style="padding-left: 15px;">(<?php  echo t('End date will be ignored if recurring is set to "none".')?>)</i>
						</div>
					</div>
				</td>
			</tr>
		</table>
		</div>
		
		<div id="proevent-tab-post" style="display: none" class="pane exclude">
			<table id="add_event" class="entry-form">
				<tr>
					<td class="header">
						<strong><?php    echo t('Exclude Certain Dates')?></strong> <img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('exclude_dates');" onmouseout="hideHelp('exclude_dates');"><div id="exclude_dates" class="help" style="display: none;"><?php  echo t('This date field is used in tandem with the Recurring Option.  If recurring is set, any instance of recurrance falling on these dates will be excluded')?></div>
					</td>
				</tr>
				<tr>
					<td>
						<?php    
							$svth = CollectionAttributeKey::getByHandle('event_exclude');
							if (is_object($event)) {
								$stvalue = $event->getAttributeValueObject($svth);
							}
						?>
						<?php    echo $svth->render('form', $stvalue);?>	
					</td>
				</tr>
			</table>
		</div>	
		
		<div id="proevent-tab-post" style="display: none" class="pane post">
	
		<p><?php    echo t('Event Description')?></p>
		<div><?php    echo $form->textarea('eventDescription', $eventDescription, array('style' => 'width: 98%; height: 90px; font-family: sans-serif;'))?></div>
		<br/>	
	
		<p><?php    echo t('Event Content')?></p>
		<?php     Loader::element('editor_init'); ?>
		<?php     Loader::element('editor_config'); ?>
		<?php     Loader::element('editor_controls', array('mode'=>'full')); ?>
		<?php    echo $form->textarea('eventBody', $eventBody, array('style' => 'width: 100%; font-family: sans-serif;', 'class' => 'ccm-advanced-editor'))?>
		<br/>
		</div>
		
		
		<div id="proevent-tab-links" style="display: none" class="pane links">
		<?php    
	
		$akt = CollectionAttributeKey::getByHandle('thumbnail');
		if (is_object($event)) {
			$tvalue = $event->getAttributeValueObject($akt);
		}
		?>
		
		<td  class="subheader">
			<div class="clearfix">
				<strong><?php    echo $akt->render('label');?></strong>
				<div class="input">
					<table class="bordered-table" style="width: 230px;">
						<tr>
							<td>
							<?php    echo $akt->render('form', $tvalue, true);?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		
		
		<?php    
	
		$aku = CollectionAttributeKey::getByHandle('event_local');
		if (is_object($event)) {
			$uvalue = $event->getAttributeValueObject($aku);
		}
		?>
		<div class="clearfix">
			<strong><?php    echo $aku->render('label');?></strong>
			<div class="input">
			 <?php    
				
				echo $aku->render('form', $uvalue, array('size'=>'50'));
	
			?>
			</div>
		</div>
		
		
		<?php    
	
		$aku = CollectionAttributeKey::getByHandle('address');
		if (is_object($event)) {
			$uvalue = $event->getAttributeValueObject($aku);
		}
		?>
		
		<div class="clearfix">
			<strong><?php    echo $aku->render('label');?></strong>
			<div class="input">
			 <?php    
				
				echo $aku->render('form', $uvalue, array('size'=>'50'));
	
				?>
			</div>
		</div>

		<?php    
	
		$aku = CollectionAttributeKey::getByHandle('contact_name');
		if (is_object($event)) {
			$uvalue = $event->getAttributeValueObject($aku);
		}
		?>
		<div class="clearfix">
			<strong><?php    echo $aku->render('label');?></strong>
			<div class="input">
			<?php    
				
				echo $aku->render('form', $uvalue, array('size'=>'50'));
	
				?>
			</div>
		</div>
		<?php    
	
		$aku = CollectionAttributeKey::getByHandle('contact_email');
		if (is_object($event)) {
			$uvalue = $event->getAttributeValueObject($aku);
		}
		?>
		<div class="clearfix">
			<strong><?php    echo $aku->render('label');?></strong>
			<div class="input">
				<?php    
				
				echo $aku->render('form', $uvalue, array('size'=>'50'));
	
				?>
			</div>
		</div>

		<?php     
		Loader::model("attribute/categories/collection");
		$akt = CollectionAttributeKey::getByHandle('event_tag');
		if (is_object($event)) {
			$tvalue = $event->getAttributeValueObject($akt);
		}
		?>
		<div class="clearfix">
			<strong><?php    echo $akt->render('label');?></strong>
			<div class="input">
			<?php    echo $akt->render('form', $tvalue, true);?>
			</div>
		</div>
	</div>
	<div id="proevent-tab-settings" class="pane booking" style="display: none;">
		<div class="clearfix">
			<?php  
			$status = array(
				''=>t('none'),
				'available'=>t('Available'),
				'booked'=>t('Booked')
			);
			?>
			<strong><label><?php  echo t('Status')?></label></strong>
			<div class="input grouped_form">
			<?php  echo Loader::helper('form')->select('status',$status,null);?><br/><i>(<?php  echo t('Status of all dates. "none" will skip any existing status data.')?>)</i>

			</div>
		</div>
		<?php       
			$set = AttributeSet::getByHandle('proevent_booking_attributes');
			$setAttribs = $set->getAttributeKeys();
			if($setAttribs){
				foreach ($setAttribs as $ak) {
					if(is_object($event)) {
						$aValue = $event->getAttributeValueObject($ak);
					}
					?>
					<div class="clearfix">
						<?php       echo $ak->render('label');?>
						<div class="input">
							<?php       echo $ak->render('form', $aValue)?>
						</div>
					</div>
					<?php     
				}
			}
		?>
	</div>
	<div id="proevent-tab-settings" class="pane additional" style="display: none;">
		<table id="add_event" class="entry-form">
			<tr>
				<td class="header" colspan="2">
					<strong><?php    echo t('Additional Attributes')?></strong>
					<img src="<?php    echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('additionals');" onmouseout="hideHelp('additionals');"><div id="additionals" class="help shift" style="display: none;"><?php  echo t('Any attribute added to the "ProEvents Additional Attributes" attribute set within your Pages & Themes/Attributes area will be available here.')?></div>
				</td>
			</tr>
		<?php    
			$set = AttributeSet::getByHandle('proevent_additional_attributes');
			$setAttribs = $set->getAttributeKeys();
			if($setAttribs){
				foreach ($setAttribs as $ak) {
					echo '<tr>';
					if(is_object($event)) {
						$aValue = $event->getAttributeValueObject($ak);
					}
					echo '<td class="subheader">';
					echo $ak->render('label');
					echo '</td>';
					echo '<td>';
					echo $ak->render('form', $aValue);
					echo '</td>';
					echo '</tr>';
				}
			}else{
				?>
				<tr>
					<td colspan="2">
						<?php    echo t('none')?>
					</td>
				</tr>
				<?php    
			}
		?>
		</table>
	</div>
	</div>
    <div class="ccm-pane-footer">
    	<?php    $ih = Loader::helper('concrete/interface'); ?>
        <?php    print $ih->submit(t($title.' Event'), 'blog-form', 'right', 'primary'); ?>
        <?php    print $ih->button(t('Cancel'), $this->url('/dashboard/proevents/list/'), 'left'); ?>
    </div>