<?php     
defined('C5_EXECUTE') or die(_("Access Denied."));
$fm = Loader::helper('form'); 
global $c;
$cParentID =  $c->getCollectionID(); 
$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('pro_event_list');
?>
<style type="text/css">
#ccm-block-fields .ccm-pane-body {padding: 0 12px!important;}
</style>
<input type="hidden" name="pageListToolsDir" value="<?php    echo $uh->getBlockTypeToolsURL($bt)?>/" />
<div class="ccm-ui">
	<div class="ccm-pane-body">
		<ul class="tabs">
			<li class="active"><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.content').show();"><?php      echo t('Content')?></a>
			</li>
			<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.filters').show();"><?php      echo t('Filters')?></a>
			</li>
			<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.sorting').show();"><?php      echo t('Sorting')?></a>
			</li>
			<li><a href="javascript:void(0)" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.options').show();"><?php      echo t('Options')?></a>
			</li>
		</ul>
		
		<div class="pane content" style="display: block;">
			<h4><?php     echo t('Event List Title');?></h4>
			<?php    echo $fm->text('rssTitle', $rssTitle);?>
			<br/><br/>
			<span>
			  	<div><strong><?php     echo t('Number of Events');?></strong></div>
			  	<input type="text" name="num" value="<?php     if(isset($num)){echo $num;}else{echo '3';}?>" style="width: 30px">
			  	<span>
			  	 <input name="isPaged" type="checkbox" value="1" <?php    if ($isPaged == 1) {echo 'checked' ;}  ?> /> 
			  	 <?php     echo t('show pagination?')?>
			  	</span>
			</span>
			<br/><br/>
			<div><strong><?php     echo t('Choose Event List Type');?></strong></div>
		 	<?php  echo $fm->select('listType',array(''=>t('Use Custom View Name'),'month'=>t('By This Month'),'following month'=>t('By Next Month'),'week'=>t('By This Week'),'following week'=>t('By Next Week'),'today'=>t('By Today')),$listType)?>
		 	
		 	<br/><br/>
			<span>
			   	<div><strong><?php     echo t('Truncate Descriptions');?></strong></div>  
			  	<input name="truncateSummaries" type="checkbox" value="1" <?php    if ($truncateSummaries == 1) {echo 'checked' ;}  ?> /> 
			   		<?php     echo t('Truncate Event Descriptions after');?> 
					<input type="text" name="truncateChars" size="3" value="<?php     if ($truncateChars){echo $truncateChars;}?>" /> 
					<?php     echo t('characters');?>
			</span>
		</div>
		<div class="pane filters" style="display: none;">
		
		 	<div><strong><?php     echo t('Choose Event Category');?></strong></div>
	    	<?php    
	    		function getCat($ctID) {
	    		
	    		$selected_cat = explode(', ',$ctID);
				
				if(in_array('All Categories', $selected_cat) || empty($selected_cat)){
					echo '<input type="checkbox" name="ctID[]" value="All Categories" checked/> All Categories</br>';
				}else{
					echo '<input type="checkbox" name="ctID[]" value="All Categories"/> All Categories</br>';
				}
				$eventify = Loader::helper('eventify','proevents');
				$options = $eventify->getEventCats();
				
				foreach($options as $option){
					echo '<input type="checkbox" name="ctID[]" value="'.$option['value'].'"';
					if(in_array($option['value'], $selected_cat)){
						echo ' checked';
					}
					echo '/> '.$option['value'].' </br>';
				}
				
			}
			echo getCat($ctID);
			?>	
		 	<br/>
		 	
		 	<div><strong><?php     echo t('Show Category Filtering?');?></strong></div>
		 	<input name="showfilters" type="checkbox" value="1" <?php    if ($showfilters == 1) {echo 'checked' ;}  ?> /> <?php     echo t('yes');?>
			<br/>
		 	<br/>

		 	<div><strong><?php     echo t('Choose Calendar');?></strong></div>
	    	<select name="sctID">
	    	<?php    
	    	function getSec($sctID) {
				$db = Loader::db();
				Loader::model('page_list');
				$eventSectionList = new PageList();
				$f = "ak_event_section = 1";
				
				$ak = CollectionAttributeKey::getByHandle('homegroup_section');
				if(is_object($ak) && $ak->getAttributeKeyID() > 0){
					$f .= " OR ak_homegroup_section = 1";
				}
				$ak = CollectionAttributeKey::getByHandle('meeting_section');
				if(is_object($ak) && $ak->getAttributeKeyID() > 0){
					$f .= " OR ak_meeting_section = 1";
				}
				
				$eventSectionList->filter(false,$f);
				$eventSectionList->sortBy('cvName', 'asc');
				//$eventSectionList->debug();
				$tmpSections = $eventSectionList->get();
				$sections = array();
				foreach($tmpSections as $_c) {
					$section = $_c->getCollectionName();
	
					if ($_c->getCollectionID() == $sctID && $section != 'All Sections'){
				  		echo '<option  value="'.$_c->getCollectionID().'"  selected>'.$section.'</option>';
					} elseif($section != 'All Sections') {
						echo  '<option value="'.$_c->getCollectionID().'">'.$section.'</option>';
					}
				}
				if ($sctID == 'All Sections' || $sctID == '' || !isset($sctID)){
					echo '<option value="All Sections" selected>'.t('All Calendars').'</option>';
				} elseif($sctID != 'All Sections') {
					echo '<option value="All Sections">'.t('All Calendars').'</option>';
				}
			}
			echo getSec($sctID);
			?>	
			</select>
			<br/>
            <?php     
			  Loader::model('attribute/categories/collection');
			  $cadf = CollectionAttributeKey::getByHandle('is_featured');
            ?>
			<br/>
			<h4><?php       echo t('Filter Featured Events')?></h4>
			<div class="input">
				<input <?php      if (!is_object($cadf)) { ?> disabled <?php      } ?> type="checkbox" name="displayFeaturedOnly" value="1" <?php      if ($displayFeaturedOnly == 1) { ?> checked <?php      } ?> style="vertical-align: middle" value="1" />
				<?php     echo t('Featured events only.')?>
				<?php      if (!is_object($cadf)) { ?>
				<?php     echo t('(<strong>Note</strong>: You must create the "is_featured" page attribute first.)');?>
				<?php      } ?>
			</div>
			
			<br/><br/>
			<span>
			   	<div><strong><?php     echo t('Filter By Viewing User?');?></strong></div>  
			  	<input name="filter_by_user" type="checkbox" value="1" <?php    if ($filter_by_user == 1) {echo 'checked' ;}  ?> /> 
			  	
			  	 <?php     echo t('Yes, only list events the currently viewing user has saved.')?>
			</span>
		</div>
		<div class="pane sorting" style="display: none;">
			<span>
				<div><strong><?php    echo t('Ordering') ?></strong></div>
				<select name="ordering">
					<option value="ASC" <?php    if($ordering=='ASC'){echo 'selected' ;} ?> >Ascending</option>
					<option value="DESC" <?php    if($ordering=='DESC'){echo 'selected' ;} ?> >Descending</option>
				</select>
			</span>
		</div>
		<div class="pane options" style="display: none;">
			<span>
			  	<div><strong><?php     echo t('Display message for "no events"');?></strong></div>
			  	<input type="text" name="nonelistmsg" value="<?php     if(isset($nonelistmsg)){echo $nonelistmsg;}?>" style="width: 250px">
			</span>
			
			<br/><br />
			
			<span>
			   	<div><strong><?php     echo t('Show iCal Feed?');?></strong></div>  
			  	<input name="showfeed" type="checkbox" value="1" <?php    if ($showfeed == 1) {echo 'checked' ;}  ?> /> <?php     echo t('yes');?>
			</span>
			
				<?php    if (isset($rssDescription)) { echo '<input type="hidden" name="rssDescription" value="'.$rssDescription.'" />';} ?>
		</div>
	</div>
</div>