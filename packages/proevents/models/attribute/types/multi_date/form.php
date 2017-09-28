<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php     
if($type=='date_time_time'){
	$ele = Loader::helper('form/date_time');
	$tm = Loader::helper('form/time','proevents');
	$dtt = Loader::helper('form/date_time_time','proevents');
	$instance = rand(1,2000000);
	//var_dump(DATE_APP_GENERIC_MDYT);
?>
<a href="javascript:;" onClick="addDateTimeTime_<?php   echo $instance?>(<?php   echo $instance?>);">[+] Add Date</a>
<div id="dates_wrap" class="dates_wrap_<?php   echo $instance?>">
	<?php     
	//var_dump($values);
	//exit;
	echo Loader::helper('form')->hidden('akID['.$akval.']['.$i.'][reset]',0);
	if($values){
		$dates = explode(':^:',$values);
		foreach ($dates as $date){
			$vars = explode(':-:',$date);
				$i++;
				echo '<div id="date'.$i.'"><div style="padding-left: 10px;">';
				print '<input id="akID_'.$akval.'__'.$i.'__value_st_dt_pub" name="akID['.$akval.']['.$i.'][value_st_dt]_pub" class="ccm-input-date date_pick_'.$instance.'_'.$i.'" value="'.date(DATE_APP_GENERIC_MDY,strtotime($vars[0])).'" />';
				print '<input id="akID_'.$akval.'__'.$i.'__value_st_dt" name="akID['.$akval.']['.$i.'][value_st_dt]" type="hidden"  value="'.date(DATE_APP_GENERIC_MDY,strtotime($vars[0])).'"/>';				
				print $tm->timex('akID['.$akval.']['.$i.'][value_st]',date('H:i',strtotime($vars[0])));
				echo '&nbsp; to &nbsp;';
				print $tm->timex('akID['.$akval.']['.$i.'][value_end]',date('H:i',strtotime($vars[1])));
				echo '<a href="javascript:;" onClick="removeDate(\''.$i.'\',\''.$instance.'\');">[X]</a>';
				echo '</div></div>';
				echo '<script type="text/javascript">';
				echo '$(function() { $(".date_pick_'.$instance.'_'.$i.'").datepicker({ changeYear: true, showAnim: \'fadeIn\', dateFormat:\''. DATE_APP_DATE_PICKER .'\', altField: \'#akID_'.$akval.'__'.$i.'__value_st_dt\' }).datepicker( \'setDate\' , \''.date(DATE_APP_GENERIC_MDY,strtotime($vars[0])).'\' ); });';
				echo '</script>';
		}
	}
	?>
	<input type="hidden" value="<?php     echo $i?>" id="dateCount" />
</div>
<script type="text/javascript"> 
	addDateTimeTime_<?php   echo $instance?> = function(t){
	  var numi = $('.dates_wrap_'+t+' #dateCount').val();
	  var num = (numi * 1)+1;
	  //alert(num)
	  $('.dates_wrap_'+t+' #dateCount').val(num);
	  var divIdName = "date"+num;
	  var newdiv = document.createElement('div');
	  newdiv.setAttribute("id",divIdName);
	  newdiv.setAttribute("class","date_"+t+"_"+num);
	  newdiv.innerHTML = "<div style=\"padding-left: 0px;\"><span id=\"adID_<?php     echo $akval?>_"+num+"__value_dw\" class=\"ccm-input-date-wrapper\" style=\"padding-left: 10px;\">";
	  
	  newdiv.innerHTML += "<input id=\"akID_<?php   echo $akval?>__"+num+"__value_st_dt_pub\" name=\"akID[<?php     echo $akval?>]["+num+"][value_st_dt]_pub\" class=\"ccm-input-date date_pick_"+t+"_"+num+"\" value=\"<?php     echo date(DATE_APP_GENERIC_MDY);?>\" /><input id=\"akID_<?php     echo $akval?>__"+num+"__value_st_dt\" name=\"akID[<?php     echo $akval?>]["+num+"][value_st_dt]\" type=\"hidden\"  />";

	  
	  newdiv.innerHTML += "<select name=\"akID[<?php     echo $akval?>]["+num+"][value_st_h]\" class=\"input-mini\"><?php     if(DATE_FORM_HELPER_FORMAT_HOUR==12){ for($d=1;$d<=12;++$d){echo '<option>'.sprintf("%02d",$d).'</option>';} }else{ for($d=0;$d<=23;++$d){echo '<option>'.sprintf("%02d",$d).'</option>';} }?></select>:<select name=\"akID[<?php     echo $akval?>]["+num+"][value_st_m]\" class=\"input-mini\"><?php     for($m=0;$m<60;$m=$m+5){echo '<option>'.sprintf("%02d",$m).'</option>';}?></select> <?php     if(DATE_FORM_HELPER_FORMAT_HOUR==12){ ?><select name=\"akID[<?php     echo $akval?>]["+num+"][value_st_a]\" class=\"input-mini\"><option value=\"PM\">PM</option><option value=\"AM\">AM</option></select><?php     } ?> &nbsp; to &nbsp;<select name=\"akID[<?php     echo $akval?>]["+num+"][value_end_h]\" class=\"input-mini\"><?php     if(DATE_FORM_HELPER_FORMAT_HOUR==12){ for($d=1;$d<=12;++$d){echo '<option>'.sprintf("%02d",$d).'</option>';} }else{ for($d=0;$d<=23;++$d){echo '<option>'.sprintf("%02d",$d).'</option>';} }?></select>:<select name=\"akID[<?php     echo $akval?>]["+num+"][value_end_m]\" class=\"input-mini\"><?php     for($m=0;$m<60;$m=$m+5){echo '<option>'.sprintf("%02d",$m).'</option>';}?></select> <?php     if(DATE_FORM_HELPER_FORMAT_HOUR==12){ ?><select name=\"akID[<?php     echo $akval?>]["+num+"][value_end_a]\" class=\"input-mini\"><option value=\"PM\">PM</option><option value=\"AM\">AM</option></select><?php     } ?> <a href=\"javascript:;\" onClick=\"removeDate(\'"+num+"\',\'"+t+"\');\">[X]</a></div>";
	  $('.dates_wrap_'+t).append(newdiv);
	  
	  
	  var newDateScript = document.createElement('script');
	  newDateScript.text = "$(function() { $(\".date_pick_"+t+"_"+num+"\").datepicker({ changeYear: true, showAnim: \'fadeIn\', dateFormat:\'<?php     echo DATE_APP_DATE_PICKER ;?>\', altField: \'#akID_<?php     echo $akval?>__"+num+"__value_st_dt\' }).datepicker( \'setDate\' , new Date() ); });";
	  newDateScript.type = 'text/javascript';
	  $('.date_'+t+'_'+num).append(newDateScript);

	  var className = "ccm-input-date hasDatepicker date_pick_"+t+"_"+num;
	  $('.date_pick_'+t+'_'+num).attr('class',className);
	  if($('.proform_slider').length > 0){
		console.log($(this).next('#dates_wrap').height());
		$('.proform_slider').css('height',($('.proform_slider').height() + ($('#dates_wrap').height() / 2)));
	  }
	}
	
	removeDate = function(i,t){
		$('.dates_wrap_'+t+' #date'+i).remove();
	}
	
	$('#dates_wrap').find('value_st_h').replaceAll('[hr]');

</script>
<?php     
}else{
	$ele = Loader::helper('form/date_time');
	$instance = rand(1,2000000);
?>
<a href="javascript:;" onClick="addDate_<?php   echo $instance?>(<?php   echo $instance?>);">[+] Add Date</a>
<div id="dates_werap" class="date_wrap_<?php   echo $instance?>">
	<?php     
	$i=0;
	if($values){
		$dates = explode(':^:',$values);
		//var_dump($dates);
		foreach ($dates as $date){
			if($type=='date_exclude'){
				if(date('Y-m-d',strtotime($date)) >= date('Y-m-d')){
					$i++;
					echo '<div id="date_simple'.$i.'"><div style="padding-left: 10px;">';
					print $ele->date('akID['.$akval.']['.$i.'][value_st_dt]',$date);
					echo '<a href="javascript:;" onClick="removeDateSimple(\''.$i.'\',\''.$instance.'\');">[X]</a>';
					echo '</div></div>';
				}
			}else{
				$i++;
				echo '<div id="date_simple'.$i.'"><div style="padding-left: 10px;">';
				print $ele->date('akID['.$akval.']['.$i.'][value_st_dt]',$date);
				echo '<a href="javascript:;" onClick="removeDateSimple(\''.$i.'\',\''.$instance.'\');">[X]</a>';
				echo '</div></div>';
			}
		}
	}
	?>
	<input type="hidden" value="<?php     echo $i?>" id="dateCount" />
</div>
<script type="text/javascript"> 
	addDate_<?php   echo $instance?> = function(t){
	  
	  var numi = $('.date_wrap_<?php   echo $instance?> #dateCount').val();
	  var num = parseInt(numi) + 1;
	  console.log(num);
	  $('.date_wrap_<?php   echo $instance?> #dateCount').val(num);
	  
	  var divIdName = "date_simple"+num;
	  var newdiv = document.createElement('div');
	  newdiv.setAttribute("id",divIdName);
	  newdiv.innerHTML = "<div style=\"padding-left: 0px;\"><span id=\"adID_<?php     echo $akval?>_"+num+"__value_dw\" class=\"ccm-input-date-wrapper\" style=\"padding-left: 10px;\">";

	  
	  newdiv.innerHTML += "<input value=\"<?php     echo date(DATE_APP_GENERIC_MDY);?>\" class=\"ccm-input-date date_pick_"+t+"_"+num+"\" name=\"akID[<?php     echo $akval?>]["+num+"][value_st_dt]\" id=\"akID[<?php     echo $akval?>]["+num+"][value_st_dt]\"/>";
	  

	  newdiv.innerHTML += "<a href=\"javascript:;\" onClick=\"removeDateSimple(\'"+num+"\',\'"+t+"\');\">[X]</a></div>";
	  
	  $('.date_wrap_'+t).append(newdiv);
	  
	  
	  var dt = document.getElementById('date_simple'+num);
	  var newDateScript = document.createElement('script');
	  newDateScript.text = "$(function() { $(\".date_pick_"+t+"_"+num+"\").datepicker({ changeYear: true, showAnim: \'fadeIn\', dateFormat:\'<?php     echo DATE_APP_DATE_PICKER ;?>\'}).datepicker( \'setDate\' , new Date() ); });";
	  dt.appendChild(newDateScript);
	  

	  var className = "ccm-input-date hasDatepicker date_"+num;
	  $(".date_"+num).attr('class',className);
	}
	
	removeDateSimple = function(i,t){
		$('.date_wrap_<?php   echo $instance?> #date_simple'+i).remove();
	}
	
	$('#date_wrap').find('value_st_h').replaceAll('[hr]');
	
	if($('.proform_slider').length > 0){
		$('.proform_slider').css('height',($('.proform_slider').height() + $(this).next('.multiplex_set').children(":first").height()));
	}
</script>
<?php     
}
?>
