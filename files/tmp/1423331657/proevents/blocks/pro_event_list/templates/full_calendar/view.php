<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;
//var_dump($c->getVersionObject()->isMostRecent());
if($c->getVersionObject()->isApproved() == true){
	$state = '&state=ACTIVE';
}


$ajax_url = Loader::helper('concrete/urls')->getToolsURL('ajax_cal.php','proevents').'?title='.$title.$state;

//here we set up our drop down month select
$year = date('Y');
if(isset($_REQUEST['month'])){
	$month = $_REQUEST['month'];
}else{
	$month = date('m');
}
$day = date('d') ;
?>
<style>
.loader {display: block;float: right;margin-right: 32px;height:16px;width:16px;background-image:url('<?php    echo ASSETS_URL_IMAGES?>/icons/icon_header_loading.gif');}
</style>
<script type="text/javascript">
event_dialog = function() {
	jQuery.fn.dialog.open({
		width: 630,
		height: 450,
		modal: false,
		href: '<?php    echo BASE_URL.DIR_REL?>/index.php/tools/packages/proevents/add_event/',
		title: 'Add New Event',
		onClose: function() {
			$(window.location).attr('href', '<?php    echo $this->action('view')?>');
      	}	
	});
}

next_month = function(){
	var ctID = $("#ctID option:selected").val();
	var year = $("#setyear").val();
	var month = $("#setmo").val();
	
	if(month == '12'){
		ni = '01';
		year = (year * 1) + 1;
		$("#setyear").val(year);
	}else{
		var ni = (month * 1) + 01;
		ni = ni+='';
		
		if(ni.length < 2){
			var ni = '0'+ ni;
		}
		
	}
	
	var month = $("#setmo").val(ni);	

	var ajax_url = '<?php    echo $ajax_url?>';
	var args = {
		bID: '<?php  echo $bID?>',
		cID: '<?php  echo $c->cID?>',
		ctID: ctID,
		sctID: '<?php  echo $sctID?>',
		year: year,
		month: ni,
		dateset: true
	}
	$.get(ajax_url, args, function(html) {
		//alert(html);
		$('#ajax_cal').html('');
		$('#ajax_cal').html(html);
	});
}

prev_month = function(){
	var ctID = $("#ctID option:selected").val();
	var year = $("#setyear option:selected").val();
	var month = $("#setmo option:selected").val();
	if(month == '01'){
		ni = '12';
		year = (year * 1) - 1;
		$("#setyear").val(year);
	}else{
		var ni = (month * 1) - 1;
		ni = ni+='';
		if(ni.length < 2){
			var ni = '0'+ni;
		}
	}
	var month = $("#setmo").val(ni);
	//alert($ni);
	var ajax_url = '<?php    echo $ajax_url?>';
	var args = {
		bID: '<?php  echo $bID?>',
		cID: '<?php  echo $c->cID?>',
		ctID: ctID,
		sctID: '<?php  echo $sctID?>',
		year: year,
		month: ni,
		dateset: true
	}
	$.get(ajax_url, args, function(html) {
		//alert(html);
		$('#ajax_cal').html('');
		$('#ajax_cal').html(html);
	});
}


$(document).ready(function(){
	$('.loader').show();
	var ctID = $("#ctID option:selected").val();
	var year = $("#setyear option:selected").val();
	var month = $("#setmo option:selected").val();
	var args = {
		bID: '<?php  echo $bID?>',
		cID: '<?php  echo $c->cID?>',
		ctID: ctID,
		sctID: '<?php  echo $sctID?>',
		year: year,
		month: month
	}
	var ajax_url = '<?php    echo $ajax_url?>';
	$.get(ajax_url,args, function(html) {
		//alert(html);
		$('#ajax_cal').html(html);
		$('.loader').hide();
	});

	$("#setyear").change(function(){
		$('.loader').show();
		var ctID = $("#ctID option:selected").val();
		var year = $("#setyear option:selected").val();
		var month = $("#setmo option:selected").val();
		var args = {
			bID: '<?php  echo $bID?>',
			cID: '<?php  echo $c->cID?>',
			ctID: ctID,
			sctID: '<?php  echo $sctID?>',
			year: year,
			month: month,
			dateset: true
		}
		//alert($ni);
		var ajax_url = '<?php    echo $ajax_url?>';
		$.get(ajax_url, args, function(html) {
			//alert(html);
			$('#ajax_cal').html('');
			$('#ajax_cal').html(html);
			$('.loader').hide();
		});
	});
	
	$("#setmo").change(function(){
		$('.loader').show();
		var ctID = $("#ctID option:selected").val();
		var year = $("#setyear option:selected").val();
		var month = $("#setmo option:selected").val();
		var args = {
			bID: '<?php  echo $bID?>',
			cID: '<?php  echo $c->cID?>',
			ctID: ctID,
			sctID: '<?php  echo $sctID?>',
			year: year,
			month: month,
			dateset: true
		}
		//alert($ni);
		var ajax_url = '<?php    echo $ajax_url?>';
		$.get(ajax_url, args,function(html) {
			//alert(html);
			$('#ajax_cal').html('');
			$('#ajax_cal').html(html);
			$('.loader').hide();
		});
	});
	
	$("#ctID").change(function(){
		$('.loader').show();
		var ctID = $("#ctID option:selected").val();
		var year = $("#setyear option:selected").val();
		var month = $("#setmo option:selected").val();
		var args = {
			bID: '<?php  echo $bID?>',
			cID: '<?php  echo $c->cID?>',
			ctID: ctID,
			sctID: '<?php  echo $sctID?>',
			year: year,
			month: month,
			dateset: true
		}
		//alert($ni);
		var ajax_url = '<?php    echo $ajax_url?>';
		$.get(ajax_url, args, function(html) {
			//alert(html);
			$('#ajax_cal').html('');
			$('#ajax_cal').html(html);
			$('.loader').hide();
		});
	});

});
</script>
<?php   
/** for example only **

$u = new User();
if($u->isLoggedIn()){
	$ui = UserInfo::getByID($u->uID);
	$manager = $ui->getAttribute('event_manager');
} 
if( $manager == true || $u->isSuperUser()==true ){
	echo '<span onClick="event_dialog();" class="button right white">Add New Event</span>';
}

**/
?>
<br/>
<div class="loader"></div>
<input type="hidden" value="0" id="cur_date"/>
<form action="<?php    echo $link ;?>" method="GET">
	<span onClick="prev_month();" class="button white"><?php    echo t('<<'); ?></span>
	&nbsp;
	<select name="setyear" id="setyear">
		<option value="<?php    echo $year-2?>"><?php    echo $year-2?></option>
		<option value="<?php    echo $year-1?>"><?php    echo $year-1?></option>
		<option value="<?php    echo $year?>" selected ><?php    echo $year?></option>
		<option value="<?php    echo $year+1?>"><?php    echo $year+1?></option>
		<option value="<?php    echo $year+2?>"><?php    echo $year+2?></option>
	</select>
	<select name="setmo" id="setmo">
		<option value="01" <?php    if($month == '01'){echo 'selected' ; } ?>><?php    echo t('Jan');?></option>
		<option value="02" <?php    if($month == '02'){echo 'selected' ; } ?>><?php    echo t('Feb');?></option>
		<option value="03" <?php    if($month == '03'){echo 'selected' ; } ?>><?php    echo t('Mar');?></option>
		<option value="04" <?php    if($month == '04'){echo 'selected' ; } ?>><?php    echo t('Apr');?></option>
		<option value="05" <?php    if($month == '05'){echo 'selected' ; } ?>><?php    echo t('May');?></option>
		<option value="06" <?php    if($month == '06'){echo 'selected' ; } ?>><?php    echo t('Jun');?></option>
		<option value="07" <?php    if($month == '07'){echo 'selected' ; } ?>><?php    echo t('Jul');?></option>
		<option value="08" <?php    if($month == '08'){echo 'selected' ; } ?>><?php    echo t('Aug');?></option>
		<option value="09" <?php    if($month == '09'){echo 'selected' ; } ?>><?php    echo t('Sep');?></option>
		<option value="10" <?php    if($month == '10'){echo 'selected' ; } ?>><?php    echo t('Oct');?></option>
		<option value="11" <?php    if($month == '11'){echo 'selected' ; } ?>><?php    echo t('Nov');?></option>
		<option value="12" <?php    if($month == '12'){echo 'selected' ; } ?>><?php    echo t('Dec');?></option>
	</select>
	<?php  
	if($showfilters>0){
		Loader::packageElement('category_filter','proevents',array('c'=>$c,'ctID'=>$ctID));
	}
	?>
	<input type="hidden" name="dateset" value="1">
	&nbsp;
	<span onClick="next_month();" class="button white"><?php    echo t('>>'); ?></span>
</form>

<div id="ajax_cal"></div>
<?php    
//is iCal feed option is sellected, show it
if($showfeed==1){		
	?>
		<div class="backlink" style="float: right; font-size: 9px; margin-top: 22px;">
				<?php  echo t('built with ')?><a href="http://codestrat.com" alt="CodeStrat concrete5 addon addons packages blocks" title="CodeStrat.com Concrete5 Addons">CodeStrat.com</a><?php  echo t(' products.')?>
		</div>
	   	<div class="iCal">
			<p><img src="<?php     echo $ical_img_url ;?>" width="25" alt="iCal feed" />&nbsp;&nbsp;
			<a href="<?php     echo $ical_url;?>?ctID=<?php    echo $ctID ;?>&bID=<?php    echo $bID ; ?>&ordering=<?php    echo $ordering ;?>" id="getFeed">
			<?php     echo t('get iCal link');?></a></p>
			<link href="<?php     echo $ical_url;?>" rel="alternate" type="application/rss+xml" title="<?php     echo t('RSS');?>" />
		</div>
	<?php    
}
?>
<br style="clear:both;"/>