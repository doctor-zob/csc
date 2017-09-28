<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
$bt = BlockType::getByHandle('pro_event_list');
$AJAX_url = BASE_URL.Loader::helper('concrete/urls')->getToolsURL('ajax_list.php','proevents').'?bID='.$bID;
global $c;

Loader::packageElement('filters','proevents',array('c'=>$c,'AJAX_url'=>$AJAX_url));
?>

<center class="ajax_loader" style="display: none;"><img src="<?php   echo Loader::helper('concrete/urls')->getBlockTypeAssetsURL($bt, 'ajax-loader.gif')?>" alt="loading"/></center>

<div class="ccm-page-list event_results">


</div>

<script type="text/javascript">
/*<![CDATA[*/
	$(document).ready(function(){
		$('.ajax_loader').show();
		var url = '<?php   echo $AJAX_url?>';
		var args = {ccID: <?php   echo $c->getCollectionID();?>,joinDays: true}
		$.get(url,args,function(data){
			$('.ajax_loader').hide();
			$('#event_results').html(data);
		});
	});
	
	function getEventResults(obj,p){
		//$('#event_results').fadeOut('slow');
		$('#event_results').html('');
		$('.ajax_loader').show();
		var url = '<?php   echo $AJAX_url?>?bID=<?php   echo $bID?>';
		var args = {ccID: <?php   echo $c->getCollectionID();?>,currentPage:p,joinDays: true}
		$.get(url,args,function(data){
			$('.ajax_loader').hide();
			$('.event_results').html(data);
			//$('#event_results').fadeIn('slow');
		});
		return false;
	}
/*]]>*/
</script>