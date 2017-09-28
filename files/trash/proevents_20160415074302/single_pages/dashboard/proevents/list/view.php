<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
?>
<style type="text/css">
a:hover {text-decoration:none;} /*BG color is a must for IE6*/
a.eventtooltip span {display:none; padding:2px 3px; margin-left:8px; margin-top: -20px;}
a.eventtooltip:hover span{display:inline; position:absolute; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}
th {text-align: left;}
.align_top{vertical-align: top;}
.ccm-results-list tr td{ border-bottom-color: #dfdfdf; border-bottom-width: 1px; border-bottom-style: solid;}
.icon {
display: block;
float: left;
height:20px;
width:20px;
background-image:url('<?php    echo ASSETS_URL_IMAGES?>/icons_sprite.png'); /*your location of the image may differ*/
}
.edit {background-position: -22px -2225px;margin-right: 6px!important;}
.copy {background-position: -22px -439px;margin-right: 6px!important;}
.delete {background-position: -22px -635px;}
</style>
<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View/Search Events'), false, false, false);?>
	<div class="ccm-pane-body">
		<?php   
		if($remove_name){
		?>
		<div class="alert-message block-message error">
		  <a class="close" href="<?php    echo $this->action('clear_warning');?>">×</a>
		  <p><strong><?php    echo t('Holy guacamole! This is a warning!');?></strong></p><br/>
		  <p><?php    echo t('Are you sure you want to delete ').t($remove_name).'?';?></p>
		  <p><?php    echo t('This action may not be undone!');?></p>
		  <div class="alert-actions">
		    <a class="btn small" href="<?php    echo BASE_URL.DIR_REL;?>/index.php/dashboard/proevents/list/delete/<?php    echo $remove_cid;?>/<?php    echo $remove_name;?>/"><?php    echo t('Yes Remove This');?></a> <a class="btn small" href="<?php    echo $this->action('clear_warning');?>"><?php    echo t('Cancel');?></a>
		  </div>
		</div>
		<?php   
		}
		?>

<!--
		<ul class="breadcrumb">
		  <li class="active">List <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/add_event/">Add/Edit</a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/preview/monthly/">Preview</a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/exclude_dates/">Exclude Dates</a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/settings/">Settings</a></li>
		</ul>
-->

		<form method="get" action="<?php    echo $this->action('view')?>">
		<?php    
		$sections[0] = '** All';
		asort($sections);
		?>
		<table class="ccm-results-list">
			<tr>
				<th><strong><?php    echo $form->label('cParentID', t('Section'))?></strong></th>
				<th><strong><?php    echo t('by Name')?></strong></th>
				<th><strong><?php    echo t('by Category')?></strong></th>
				<th><strong><?php    echo t('by Tag')?></strong></th>
				<th></th>
			</tr>
			<tr>
				<td><?php    echo $form->select('cParentID', $sections, $cParentID)?></td>
				<td><?php    echo $form->text('like', $like)?></td>
				<td>
				<select name="cat" style="width: 110px!important;">
					<option value=''>--</option>
				<?php    
				foreach($cat_values as $cat){
					if($_GET['cat']==$cat['value']){$selected = 'selected="selected"';}else{$selected=null;}
					echo '<option '.$selected.'>'.$cat['value'].'</option>';
				}	
				?>
				</select>
				</td>
				<td>
				<select name="tag" style="width: 110px!important;">
					<option value=''>--</option>
				<?php    
				foreach($tag_values as $tag){
					if($_GET['tag']==$tag['value']){$selected = 'selected="selected"';}else{$selected=null;}
					echo '<option '.$selected.'>'.$tag['value'].'</option>';
				}	
				?>
				</select>
				</td>
				<td>
				<?php    echo $form->submit('submit', t('Search'))?>
				</td>
			</tr>
		</table>
		
		</form>
		<br/>
		<?php    
		$nh = Loader::helper('navigation');
		$fm = Loader::helper('form');
		if ($eventList->getTotal() > 0) { 
			$eventList->displaySummary();
			?>
			
		<table border="0" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
				<th>&nbsp;</th>
				<th class="<?php    echo $eventList->getSearchResultsClass('cvName')?>"><a href="<?php    echo $eventList->getSortByURL('cvName', 'asc')?>"><?php    echo t('Name')?></a></th>
				<th class="<?php    echo $eventList->getSearchResultsClass('cvDatePublic')?>"><a href="<?php    echo $eventList->getSortByURL('cvDatePublic', 'asc')?>"><?php    echo t('Dates')?></a></th>
				<th class="<?php    echo $eventList->getSearchResultsClass('start_time')?>"><a href="<?php    echo $eventList->getSortByURL('start_time', 'asc')?>"><?php    echo t('Times')?></a></th>
				<th><?php    echo t('Recurring')?></th>
				<th><?php    echo t('Event Category')?></th>
			</tr>
			<?php    
			foreach($eventResults as $cobj) { 

				if(is_object($cobj)){
			
					Loader::model('attribute/categories/collection');
				
					$event_start = $cobj->getCollectionDatePublic('Y-m-d'); 
					
					$akve = CollectionAttributeKey::getByHandle('event_thru');
					$event_thru = $cobj->getCollectionAttributeValue($akve);
					
					$dates_array = Loader::helper('form/date_time_time','proevents')->translate_from($cobj);
					
					$akdp = CollectionAttributeKey::getByHandle('event_recur');
					$event_recur = $cobj->getCollectionAttributeValue($akdp); 

					
					$akad = CollectionAttributeKey::getByHandle('event_allday');
					$event_allday = $cobj->getCollectionAttributeValue($akad); 
					
					$event_section_id = $cobj->getCollectionParentID();
					$sec_page= Page::getByID($event_section_id);
					$event_section = $sec_page->getCollectionName();
					
					
					$akct = CollectionAttributeKey::getByHandle('event_category');
					$event_category = $cobj->getCollectionAttributeValue($akct);
					
					$color = $cobj->getAttribute('category_color');
				
					$pkt = Loader::helper('concrete/urls');
					$pkg= Package::getByHandle('proevents');
				}
			?>
			<tr>
				<td width="88px" class="align_top">
				<a href="<?php     echo $this->url('/dashboard/proevents/add_event', 'edit', $cobj->getCollectionID())?>" class="eventtooltip icon edit"><span><?php  echo t('Edit this Event')?></span></a> &nbsp;
				<a href="<?php    echo $this->url('/dashboard/proevents/list', 'duplicate', $cobj->getCollectionID())?>" class="eventtooltip icon copy"><span><?php  echo t('Duplicate this Event')?></span></a> &nbsp;
				<a href="<?php    echo $this->url('/dashboard/proevents/list', 'delete_check', $cobj->getCollectionID(),$cobj->getCollectionName())?>" class="eventtooltip icon delete"><span><?php  echo t('Remove this Event')?></span></a>
	
				</td>
				<td class="align_top"><a href="<?php     echo $nh->getLinkToCollection($cobj)?>"><?php     echo $cobj->getCollectionName()?></a></td>
				<td class="align_top">
				<?php    
				if(is_array($dates_array)){
					foreach($dates_array as $var){
						echo date(t('M d'),strtotime($var['date']));
						echo '<br/>';
					}
				}else{
					echo $event_start;
				}
				?>
				</td>
				<td class="align_top">
				<?php     
				if($event_allday=='1'){
					echo t('All Day');
				}else{
					if($dates_array[1]['date']!=''){
						foreach($dates_array as $var){
							echo date('g:i a',strtotime($var['start']));
							echo ' - ';
							echo date('g:i a',strtotime($var['end']));
							echo '<br/>';
						}
					}else{
						echo date('g:i a',strtotime($start_time));
						echo ' - ';
						echo date('g:i a',strtotime($end_time));
					}
				}
				?>
				</td>
				<td class="align_top">
					<?php    
						if($event_recur != ''){
							echo $event_recur ;
							echo '<br/>';
							echo t('thru ').date('M d',strtotime($event_thru));
						}else{
							echo t('none');
						}
					?>
					
				</td>
				<td class="align_top">
				<?php    
				$aklc = CollectionAttributeKey::getByHandle('event_category');
				$eventCat_pre = $cobj->getCollectionAttributeValue($aklc);
				$eventCat = array();
				if(is_a($eventCat_pre,'SelectAttributeTypeOptionList')){
					$ec = $cobj->getCollectionAttributeValue($aklc)->count();
					for($i=0;$i<$ec;$i++){
						$eventCat[] = $cobj->getCollectionAttributeValue($aklc)->current()->value;
						$cobj->getCollectionAttributeValue($aklc)->next();
					}
					
					if(count($eventCat)>1){
						$eventCat = implode(', ',$eventCat);
					}else{
						$eventCat = $eventCat[0];
					}	
					
				}else{
				
					$eventCat = $cobj->getCollectionAttributeValue($aklc)->value;
		
				}
				if($color){
					echo '<div style="width: 20px; height: 20px; float: right; background-color: '.$color.';"></div>';
				}
				echo $eventCat	;
				?>
				</td>
			</tr>
			<?php      } ?>
			
			</table>
			<br/>
			<?php     
			$eventList->displayPaging();
		} else {
			print t('No event entries found.');
		}
		?>
	</div>
	<div class="ccm-pane-footer">

    </div>
