<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
 
$fmc = Loader::helper('form/color');  
$fm = Loader::helper('form');  

if(!$bordercolor){$bordercolor='#b5b5b5';}
if(!$cellhover){$cellhover='#f4f4f4';}
if(!$cellevent){$cellevent='#f9f9f9';}
if(!$currentdate){$currentdate='#f5f5f5';}
if(!$blankdate){$blankdate='#e7e7e7';}
if(!$$alldaycolor){$alldaycolor='#e6e1de';}

if(!$popupbg){$popupbg='#e6e6e6';}
if(!$popupborder){$popupborder='#999999';}
if(!$popuptext){$popuptext='#787777';}

?>
<style type="text/css">
	.help {font-style: normal; font-weight: normal; border-color: #02890d; border-width: 1px; border-style: solid; max-width: 235px; padding: 16px; MARGIN-left: 85px; background-color: #f5f5f5; position: absolute;-moz-border-radius: 5px; /* this works only in camino/firefox */-webkit-border-radius: 5px; /* this is just for Safari */}
	#dates_wrap div{margin-top: 12px; }
	.small {width: 52px!important;}
	strong label {width: 120px!important; float: left; padding: 10px 0 0 40px!important;}
	.entry-form td,.entry-form th {padding: 12px!important;}
	table.ccm-results-list tbody tr:hover td{background-color: none!important;}
</style>
<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('ProEvent Settings'), false, false, false);?>
	<div class="ccm-pane-body ccm-ui">
	<!--
		<ul class="breadcrumb">
		  <li><a href="/index.php/dashboard/proevents/list/"><?php    echo t('List');?></a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/add_event/"><?php    echo t('Add/Edit');?></a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/preview/monthly/"><?php    echo t('Preview');?></a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/exclude_dates/"><?php    echo t('Exclude Dates');?></a> <span class="divider">|</span></li>
		  <li class="active"><?php    echo t('Settings');?></li>
		</ul>
	-->

		<form method="post" id="settings" action="<?php    echo $this->action('save_settings');?>">
		<h4><?php    echo t('jQuery Calendar Options')?></h4>
		<table id="add_event" class="entry-form ccm-results-list">
			<thead>
				<tr>
					<th class="header" colspan="3">
						<strong><?php    echo t('jQuery Calendar Options')?></strong>
					</th>
				</tr>
				<tr>
<!--
					<td class="subheader">
						<input type="checkbox" name="showHolidays" value="true" <?php    if ($showHolidays == true){echo 'checked';}?>> <?php    echo t('Show Holidays');?><br/>
					</td>
-->
					<td>
					<input type="checkbox" name="showTooltips" value="true" <?php    if ($showTooltips == true){echo 'checked';}?>> <?php    echo t('Show ToolTips');?>
					</td>
					<td>
					<input type="checkbox" name="themed" value="true" <?php    if ($themed == 'true'){echo 'checked';}?>> <?php    echo t('Use jQuery Theme');?>
					</td>
				</tr>
				<tr>
					<td>
					<?php    echo t('ToolTip Color ');?>
						<select name="tooltipColor">
							<option value="light" <?php    if ($tooltipColor == 'light'){echo 'selected';}?>><?php    echo t('light');?></option>
							<option value="dark" <?php    if ($tooltipColor == 'dark'){echo 'selected';}?>><?php    echo t('dark');?></option>
							<option value="red" <?php    if ($tooltipColor == 'red'){echo 'selected';}?>><?php    echo t('red');?></option>
							<option value="blue" <?php    if ($tooltipColor == 'blue'){echo 'selected';}?>><?php    echo t('blue');?></option>
							<option value="green" <?php    if ($tooltipColor == 'green'){echo 'selected';}?>><?php    echo t('green');?></option>
							<option value="cream" <?php    if ($tooltipColor == 'cream'){echo 'selected';}?>><?php    echo t('cream');?></option>
						</select>
					</td>
					<td>
					<?php    echo t('Time Formatting ');?>
						<select name="time_formatting">
							<option value="us" <?php    if ($time_formatting == 'us'){echo 'selected';}?>><?php    echo t('us');?></option>
							<option value="euro" <?php    if ($time_formatting == 'euro'){echo 'selected';}?>><?php    echo t('euro');?></option>
						</select>
					</td>
					<td>
					<?php    echo t('Default View ');?>
						<select name="defaultView">
							<option value="month" <?php    if ($defaultView == 'month'){echo 'selected';}?>><?php    echo t('month');?></option>
							<option value="basicWeek" <?php    if ($defaultView == 'basicWeek'){echo 'selected';}?>><?php    echo t('basicWeek');?></option>
							<option value="basicDay" <?php    if ($defaultView == 'basicDay'){echo 'selected';}?>><?php    echo t('basicDay');?></option>
							<option value="agendaWeek" <?php    if ($defaultView == 'agendaWeek'){echo 'selected';}?>><?php    echo t('agendaWeek');?></option>
							<option value="agendaDay" <?php    if ($defaultView == 'agendaDay'){echo 'selected';}?>><?php    echo t('agendaDay');?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="addeventfeed"><img src="<?php    echo ASSETS_URL_IMAGES?>/icons/add_small.png" alt="add"> <?php  echo t('Add Google XML feeds')?>
						<img src="<?php     echo ASSETS_URL_IMAGES?>/icons/tooltip.png" width="16" height="16" onmouseover="showHelp('exclude_date');" onmouseout="hideHelp('exclude_date');"><div id="exclude_date" class="help" style="display: none;position: absolute;"><?php   echo t('Paste your Google calendar XML feed address: <ul><li>replace \'basic\' with \'full?1=1&singleevents=true\'</li>  <li>To designate a color for the feed add \'&color=#cccccc\'</li><li>To add a category to the feed add \'&category_filter=Category%20Name\'. (be sure to replace spaces with %20)</li></ul>')?></div>
						</div>
						<div id="xmlfeeds">
							<?php  
							if(is_array($xml_feeds)){
								foreach($xml_feeds as $feed){
									if($feed){
										echo '<div><br/><input type="text" name="xml_feeds[]" value="'.$feed.'" class="input-xxlarge"/> <img src="'.ASSETS_URL_IMAGES.'/icons/delete_small.png" alt="remove" onClick="$(this).parent().remove();"></div>';
									}
								}
							}
							?>
						</div>
						<script type="text/javascript">
							$(document).ready(function(){
								$('.addeventfeed').click(function(){
									var item = '<div><br/><input type="text" name="xml_feeds[]" class="input-xxlarge"/> <img src="<?php    echo ASSETS_URL_IMAGES?>/icons/delete_small.png" alt="remove" onClick="$(this).parent().remove();"></div>';
									$('#xmlfeeds').append(item);
								});
							});
						</script>
					</td>
				</tr>
			</thead>
		</table>

		<br/>
		
		
		<h4><?php    echo t('Additional Settings');?></h4>
		
		<table id="settings2" class="entry-form ccm-results-list">
			<thead>
				<tr>
					<th class="header" colspan="4">
						<strong><?php    echo t('iCal Timezone')?></strong>
					</th>
				</tr>
				<tr>
					<td colspan="4">
						<?php    echo $fm->text('tz_format',$tz_format,array('size'=>'12'));?>
						<br/><br/>
						<p>Please refer to the "TZ" column in the list found at: <a href="http://en.wikipedia.org/wiki/List_of_tz_database_time_zones">http://en.wikipedia.org/wiki/List_of_tz_database_time_zones</a></p>
					</td>
				</tr>
				<tr>
					<th class="header" colspan="2">
						<strong><?php    echo t('Search Path')?></strong>
					</th>
					<th class="header" colspan="2">
						<strong><?php    echo t('Default Page Type')?></strong>
					</th>
				</tr>
				<tr>
					<td colspan="2">
						<?php    
						$pgp=Loader::helper('form/page_selector');
						echo $pgp->selectPage('search_path',$search_path);
						?>
					</td>
					<td colspan="2">
						<?php    echo $fm->select('ctID', $pageTypes, $ctID)?>
					</td>
				</tr>
				<tr>
					<th colspan="4"><strong><?php   echo t('Show')?></strong></th>
				</tr>
				<tr>
					<td>
						<input name="tweets" type="checkbox" value="1" <?php    if($tweets==1){echo ' checked';}?> /> <?php   echo t('Twitter')?>
					</td>
					<td>
						<input name="google" type="checkbox" value="1" <?php    if($google==1){echo ' checked';}?> /> <?php   echo t('Google +1')?>
					</td>
					<td>
						<input name="fb_like" type="checkbox" value="1" <?php    if($fb_like==1){echo ' checked';}?> /> <?php   echo t('Facebook Like')?>
					</td>
					<td>
						<input name="invites" type="checkbox" value="1" <?php    if($invites==1){echo ' checked';}?> /> <?php   echo t('Allow Invites')?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<input name="user_events" type="checkbox" value="1" <?php    if($user_events==1){echo ' checked';}?> /> <?php   echo t('Allow Users to Save Events')?>
					</td>
				</tr>
				<tr>
					<th colspan="4"><strong><?php   echo t('ShareThis Key')?> *</strong><i><?php  echo t('required for social sharing')?></i></th>
				</tr>
				<tr>
					<td colspan="4"><?php    echo $fm->text('sharethis_key',$sharethis_key,array('size'=>'12'));?></td>
				</tr>
			</thead>
		</table>
		</div>
		<div class="ccm-pane-footer">
	    	<?php    $ih = Loader::helper('concrete/interface'); ?>
	        <?php    print $ih->submit(t('Save Settings'), 'settings', 'right', 'primary'); ?>
	        </form>
	    </div>
		<script type="text/javascript">
			showHelp = function(v){
				$('#'+v).show();
			};
			
			hideHelp = function(v){
				$('#'+v).hide();
			};
		</script>