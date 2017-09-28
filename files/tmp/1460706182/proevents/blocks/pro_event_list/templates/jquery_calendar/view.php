<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php 
$holidays = 'http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic';
$show_holidays = $settings['showHolidays'];
$tooltips = $settings['showTooltips'];
$tooltip_color = $settings['tooltipColor'];
$time_formatting = $settings['time_formatting'];
$themed = $settings['themed'];

$default_view = $settings['defaultView'];

$nh = Loader::helper('navigation');
$google_event = Page::getByPath('/google-event');
$url = $nh->getLinkToCollection($google_event).'?1=1&event=';


global $c;
if($c->getVersionObject()->isApproved() == true){
	$state = '&state=ACTIVE';
}
$ajax_url = Loader::helper('concrete/urls')->getToolsURL('ajax_jq_cal.php','proevents').'?cID='.$c->cID.'&bID='.$bID.$state;
?>

<script type='text/javascript'>

	$(document).ready(function() {

		jQuery("#ctID").change(function(){
		    filter_id = $(this).val();
		    getCalendar(filter_id);
		});

		getCalendar = function(ctID){
			if(!ctID){
				ctID = '<?php    echo urlencode($ctID)?>';
			}
			$('#calendar').empty();
			var date = new Date();var d = date.getDate();var m = date.getMonth();var y = date.getFullYear();
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,basicWeek,agendaDay,agendaList'
				},
				//firstDay: 0, //0=Sunday
				monthNames: [
					'<?php  echo t('January')?>','<?php  echo t('February')?>','<?php  echo t('March')?>','<?php  echo t('April')?>','<?php  echo t('May')?>','<?php  echo t('June')?>','<?php  echo t('July')?>','<?php  echo t('August')?>','<?php  echo t('September')?>','<?php  echo t('October')?>','<?php  echo t('November')?>','<?php  echo t('December')?>'
				],
				monthNamesShort: [
					'<?php  echo t('Jan')?>','<?php  echo t('Feb')?>','<?php  echo t('Mar')?>','<?php  echo t('Apr')?>','<?php  echo t('May')?>','<?php  echo t('Jun')?>','<?php  echo t('Jul')?>','<?php  echo t('Aug')?>','<?php  echo t('Sep')?>','<?php  echo t('Oct')?>','<?php  echo t('Nov')?>','<?php  echo t('Dec')?>'
				],
				dayNames: [
					'<?php  echo t('Sunday')?>','<?php  echo t('Monday')?>','<?php  echo t('Tuesday')?>','<?php  echo t('Wednesday')?>','<?php  echo t('Thursday')?>','<?php  echo t('Friday')?>','<?php  echo t('Saturday')?>'
				],
				dayNamesShort: [
					'<?php  echo t('Sun')?>','<?php  echo t('Mon')?>','<?php  echo t('Tues')?>','<?php  echo t('Wed')?>','<?php  echo t('Thur')?>','<?php  echo t('Fri')?>','<?php  echo t('Sat')?>'
				],
				columnFormat: {
		            month: 'ddd',
		            week: 'ddd M/d',
		            day: 'dddd M/d'
		        },
				showAgendaButton: true,
				editable: false,
				theme: <?php    echo $themed ;?>,
				defaultView: '<?php    echo $default_view?>',
				allDayDefault: false,
				lazyFetching: false,
				eventSources: [<?php    echo '{url: \''.$ajax_url.'&ctID=\'+'?>ctID<?php   echo '}';?><?php   if($show_holidays){?>,{url: '<?php    echo $holidays;?>',color: '#e6e6e6',textColor: '#7f7f7f'}<?php    } ?>
				],
				<?php 
				if($time_formatting == 'us'){
					echo 'timeFormat: \'h:mm{ - h:mm} {tt}\n\',';
					echo 'firstDay: 0,';
				}else{
					echo 'timeFormat: \'H:mm{ - H:mm}\n\',';
					echo 'firstDay: 1,';
				}
				?>
				eventClick: function(event) { if (event.url) { window.open(event.url);return false;}},
		    	<?php    if($tooltips){ ?>
		        eventRender: function(event, element) {

					if (event.url.indexOf("google") >= 0){
		        		event.url = '<?php  echo $url?>'+ event.url;
		        	}

		        	if (event.description) {
		        		element.qtip({
		        			content: event.description,
		        			position: {
		        				target: 'mouse',
		        				adjust: {
			        				mouse: false  // Can be omitted (e.g. default behaviour)
			        			}
			        		},
		        			style: { classes: 'ui-tooltip-<?php    echo $tooltip_color;?>' }
		        		});
		        	}
		        },
				<?php    } ?>
				loading: function(bool) { if (bool){ $('#loading').show(); }else{ $('#loading').hide();}},
			});
		}
		getCalendar();
	});
</script>
<br/>
<?php 
if($showfilters>0){
	Loader::packageElement('category_filter','proevents',array('c'=>$c,'ctID'=>$ctID));
}
?>
<div id="loading" style="display:none"><?php  echo t('loading')?>...</div>
<div id="calendar"></div>
<?php 

if($showfeed==1){
	?>
	   	<div class="iCal">
			<p><img src="<?php     echo $ical_img_url ;?>" width="25" alt="iCal feed" />&nbsp;&nbsp;
			<a href="<?php     echo $ical_url;?>?ctID=<?php    echo $ctID ;?>&bID=<?php    echo $bID ; ?>&ordering=<?php    echo $ordering ;?>" id="getFeed">
			<?php     echo t('get iCal link');?></a></p>
			<link href="<?php     echo $ical_url;?>" rel="alternate" type="application/rss+xml" title="<?php     echo t('RSS');?>" />
		</div>

	<?php 
}
?>
