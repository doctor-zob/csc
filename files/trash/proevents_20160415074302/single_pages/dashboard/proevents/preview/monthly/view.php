<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$html = Loader::helper('html');
$this->addHeaderItem($html->css('fullcal.css', 'proevents'));
$settings = $eventify->getSettings();
?>
<style type="text/css">
#event_cal{border-color: <?php    echo $settings['bordercolor'];?>;}
#event_cal TD {border-color: <?php    echo $settings['bordercolor'];?>;background-color: <?php    echo $settings['cellcolor'];?>;}
#event_cal TD:hover {background-color: <?php    echo $settings['cellhover'];?>;}
#event_cal #cal_blank{background-color: <?php    echo $settings['blankdate'];?>;}
#event_cal #current{background-color: <?php    echo $settings['currentdate'];?>;}
#event_cal #allday, #allday a{background-color: <?php    echo $settings['alldaycolor'];?>;}
.label {text-align: right;}
.struct a {padding-left: 8px; padding-right: 5px;}
.struct {padding-bottom: 5px;}
a.tooltip_events {position: relative; text-decoration: none;}
a.tooltip_events span{
	border-style: solid;
	border-color: <?php    echo $settings['popupborder'];?>;
	border-width: 1px;
	color: <?php    echo $settings['popuptext'];?>;
	display: none;
	position: absolute;
	top: -110px;
	left: 10px;
	width: 155px;
	padding: 5px;
	z-index: 100;
	background: <?php    echo $settings['popupbg'];?>;
	-moz-border-radius: 5px; /* this works only in camino/firefox */
	-webkit-border-radius: 5px; /* this is just for Safari */
}
a:hover.tooltip_events span{background-color: #f7f7f7; display: block;}
#ccm-dashboard-content-inner{float: left!important;}
.category_color{padding: 0px 4px 0px 4px; color: white!important; font-size: 10px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;}
</style>
<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Events Preview'), false, false, false);?>
	<div class="ccm-pane-body">

<!--
		<ul class="breadcrumb">
		  <li><a href="/index.php/dashboard/proevents/list/">List</a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/add_event/">Add/Edit</a> <span class="divider">|</span></li>
		  <li class="active">Preview <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/exclude_dates/">Exclude Dates</a> <span class="divider">|</span></li>
		  <li><a href="/index.php/dashboard/proevents/settings/">Settings</a></li>
		</ul>
-->

<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
$html = Loader::helper('html');
$uh = Loader::helper('concrete/urls');
$bt = BlockType::getByHandle('pro_event_list');
global $c;
$cParentID =  $c->getCollectionID(); 
$hoveroffset = 'left';

//set this value to one to change the calendar view to Euro format with weeks starting on Mon.
 $euro_cal = 0;

//if the 'BACK' button is selected, bump the month back by one
if(isset($_GET['back'])){
	$month = $_GET['CurrentMonth'];
	$year = $_GET['CurrentYear'];
	if($month == 1){
		$month = 12;
		$year = $year-1;
	}
	else
	{
		$month=$month-1;
	}
	$ctID = $_GET['ctID'];
}
//if the 'NEXT' button is selected, bump the month by one
else if (isset($_GET['next'])){
	$month = $_GET['CurrentMonth'];
	$year = $_GET['CurrentYear'];
	if($month == 12){
		$month = 1;
		$year = $year+1;
	}
	else
	{
	$month= ++$month;
	}
	$ctID = $_GET['ctID'];
}
// if the month select option is used, set the month to that month
else if (isset($_GET['dateset'])){
	$date =time () ;
	$day = date('d', $date) ;
	$year = $_GET['setyear'];
	$month = $_GET['setmo'];
	$ctID = $_GET['ctID'];
}
else
{
// if nothing then set the date to today
$date =time () ;
$day = date('d', $date) ;
$month = date('m', $date) ;
$year = date('Y', $date) ;
$ctID='All Categories';
}
	echo '<h1>'.$rssTitle.'</h1>' ;
	
		$db = Loader::db();

	//We then determine how many days are in the current month
		$days_in_month = cal_days_in_month(0, $month, $year) ; 

	//go grab the posts, check if they are current, return only current posts
		$events = $eventify->getDateSpan($ctID,date('Y-m-d',strtotime($year.'-'.$month.'-01')),date('Y-m-d',strtotime($year.'-'.$month.'-'.$days_in_month)));

	//Here we generate the first day of the month 
		$first_day = mktime(0,0,0,$month, 1, $year) ; 

	//This gets us the month name 
		$title = date('F', $first_day) ; 

	//Here we find out what day of the week the first day of the month falls on 
		$day_of_week = date('D', $first_day) ; 

	//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
	
	switch($day_of_week){ 
			case date('D', strtotime('Mon')) : if($euro_cal >= 1){$blank = 0;}else{$blank = 1;} break; 
			case date('D', strtotime('Tue')) : if($euro_cal >= 1){$blank = 1;}else{$blank = 2;} break; 
			case date('D', strtotime('Wed')) : if($euro_cal >= 1){$blank = 2;}else{$blank = 3;} break; 
			case date('D', strtotime('Thu')) : if($euro_cal >= 1){$blank = 3;}else{$blank = 4;} break; 
			case date('D', strtotime('Fri')) : if($euro_cal >= 1){$blank = 4;}else{$blank = 5;} break; 
			case date('D', strtotime('Sat')) : if($euro_cal >= 1){$blank = 5;}else{$blank = 6;} break; 
			case date('D', strtotime('Sun')) : if($euro_cal >= 1){$blank = 6;}else{$blank = 0;} break; 
	}
		
			
	//here we set up our drop down month select
$link = Loader::helper('navigation')->getLinkToCollection($c);
$link = $eventify->URLfix($link);
		?>
		<form action="<?php    echo $link ;?>" method="GET">
			<a href="<?php    echo $link ;?>back=1&CurrentMonth=<?php    echo $month ;?>&CurrentYear=<?php    echo $year  ; ?>&ctID=<?php    echo $ctID ;?>" class="btn"><?php    echo t('PREV'); ?></a>
			&nbsp;
			<select name="setyear">
				<option value="<?php    echo $year-2?>"><?php    echo $year-2?></option>
				<option value="<?php    echo $year-1?>"><?php    echo $year-1?></option>
				<option value="<?php    echo $year?>" selected ><?php    echo $year?></option>
				<option value="<?php    echo $year+1?>"><?php    echo $year+1?></option>
				<option value="<?php    echo $year+2?>"><?php    echo $year+2?></option>
			</select>
			<select name="setmo">
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
			<select name="ctID">
	    	<?php    
	    		function getCat($ctID) {
				$db = Loader::db();
				$r = $db->Execute("SELECT DISTINCT category FROM btProEventDates");
				while ($row = $r->FetchRow()) {
					if ($row['category'] == $ctID && $row['category'] != 'All Categories'){
				  		echo '<option  value="'.$row['category'].'"  selected>'.$row['category'].'</option>';
					} elseif($row['category'] != 'All Categories') {
						echo  '<option value="'.$row['category'].'">'.$row['category'].'</option>';
					}
				}
				if ($ctID == 'All Categories' || $ctID == '' || !isset($ctID)){
					echo '<option value="All Categories" selected>'.t('All Categories').'</option>';
				} elseif($ctID != 'All Categories') {
					echo '<option value="All Categories">'.t('All Categories').'</option>';
				}
			}
			echo getCat($ctID);
			?>	
			</select>
			<input type="hidden" name="dateset" value="1">
			<input type="submit" class="btn" value="go" />
			&nbsp;
			<a href="<?php    echo $link ;?>next=1&CurrentMonth=<?php    echo $month ;?>&CurrentYear=<?php    echo $year  ; ?>&ctID=<?php    echo $ctID ;?>" class="btn"><?php    echo t('NEXT'); ?></a>
		</form>

		<?php    
		//Here we start building the table heads 
			echo "<table  id='event_cal'>";
			echo "<tr><th colspan=7 id='year'>$title $year</th></tr>";
	
			if($euro_cal >= 1){
			
			echo '<tr class="header"><td>'.date('D',strtotime('Monday')).'</td><td>'.date('D',strtotime('Tuesday')).'</td><td>'.date('D',strtotime('Wednesday')).'</td><td>'.date('D',strtotime('Thursday')).'</td><td>'.date('D',strtotime('Friday')).'</td><td>'.date('D',strtotime('Saturday')).'</td><td>'.date('D',strtotime('Sunday')).'</td></tr>';
			
			}else{
			
			echo '<tr class="header"><td>'.date('D',strtotime('Sunday')).'</td><td>'.date('D',strtotime('Monday')).'</td><td>'.date('D',strtotime('Tuesday')).'</td><td>'.date('D',strtotime('Wednesday')).'</td><td>'.date('D',strtotime('Thursday')).'</td><td>'.date('D',strtotime('Friday')).'</td><td>'.date('D',strtotime('Saturday')).'</td></tr>';
			
			}

		//This counts the days in the week, up to 7
			$day_count = 1;

			echo "<tr>";

		//first we take care of those blank days
			while ( $blank > 0 ) { 
				echo "<td id='cal_blank'></td>"; 
				$blank = $blank-1; 
				$day_count++;
			}

		//sets the first day of the month to 1 
			$day_num = 1;

		//count up the days, untill we've done all of them in the month
			while ( $day_num <= $days_in_month ) { 
		
		//if the current date block being looped through is equal to today's date, then highlight it via CSS	
			if(date('Y-m-d') == date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num))){ $daystyle = 'current';}else{$daystyle = 'day';}
			 ?>
				<td  valign="top" id="<?php    echo $daystyle ; ?>">
				<?php    echo $day_num ; ?>
				<div id="cal_day">
				<?php    
				
				$daynum = date('Y-m-d',strtotime($year.'-'.$month.'-'.$day_num));
				
		//we want to loop through each event, check it's recur state, then check if the current date block being looped through is with that range
					foreach($events as $key => $row){
							
							$date = date('Y-m-d',strtotime($row['date']));
							
							if($date == $daynum){
								$url = $eventify->grabURL($row['eventID']); 
								$events_item = $day_num;
							}
							
							
				//if any events turn up that recure durring or are on the current date block being looped through, we create an event			
							
								$i += 1;

								if($events_item==$day_num){
						
									$ep = Page::getByID($row['eventID']);
									$color = $ep->getAttribute('category_color');
								
									if($row['allday'] != 1){ $itemstyle = 'normal';}else{$itemstyle = 'allday';}
									?>
									<div id="<?php    echo $itemstyle ; ?>">
									<a  href="<?php    echo $url ; ?>" class="tooltip_events">
									<?php    
									if($color){
										print '<div style="background-color: '.$color.';" class="category_color">';
										print substr($row['title'],0,12) ;
										print '</div>';
									}else{
										print substr($row['title'],0,12) ;
									}
									//echo substr($row['title'],0,20) ;
									?>
									
									<span>
										<h2><?php    echo $row['title'] ; ?></h2>
										<?php    if($row['location']!=''){?>		
										<strong><?php    echo $row['location'] ; ?></strong>
										<br/>
										<?php    } ?>
										<h3>
										<?php    
										if ($row['allday'] !=1){
											$time = date($settings['time_format'],strtotime($row['sttime'])).' - '.date($settings['time_format'],strtotime($row['entime']));
											echo $time;
										}else{
											echo t('All Day');
										}
										?>
										</h3>
						
										<?php    
										$content = strip_tags($row['description']);
	  									echo  substr($content,0,90).'.....';
											?>
								
									</span>
									</a>
								</div>
									
									<?php    
									
								}
							unset($events_item);
						}
				
		?>
	</div>
</td> 
<?php    
$day_num++; 
$day_count++;

//Make sure we start a new row every week
	if ($day_count > 7){
		echo "</tr><tr>";
		$day_count = 1;
	}
}

//Finaly we finish out the table with some blank details if needed
while ( $day_count >1 && $day_count <=7 ) { 
	echo "<td id='cal_blank'> </td>"; 
	$day_count++; 
} 

echo "</tr></table>";
	
//is iCal feed option is sellected, show it
		if($showfeed==1){		
			?>
			   	<div class="iCal">
        			<p><img src="<?php     echo $eventify->getCalUrl() ;?>" width="25" alt="iCal feed" />&nbsp;&nbsp;
        			<a href="<?php     echo($eventify->getiCalUrl());?>?ctID=<?php    echo $ctID ;?>&bID=<?php    echo $bID ; ?>&ordering=<?php    echo $ordering ;?>" id="getFeed">
        			<?php     echo t('get iCal link');?></a></p>
        			<link href="<?php     echo $eventify->getiCalUrl();?>" rel="alternate" type="application/rss+xml" title="<?php     echo t('RSS');?>" />
    			</div>
    		<?php    
    	}


	if (isset($bID)) { echo '<input type="hidden" name="bID" value="'.$bID.'" />';}
	
?>
	</div>
	<div class="ccm-pane-footer">

    </div>
