<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$html = Loader::helper('html');
$this->addHeaderItem($html->css('weekcal.css', 'proevents'));
$settings = $eventify->getSettings();
?>
<br/>
<style>
#event_cal{border-color: <?php    echo $settings['bordercolor'];?>;}
#event_cal TD {border-color: <?php    echo $settings['bordercolor'];?>;background-color: <?php    echo $settings['cellcolor'];?>;}
#event_cal TD:hover {background-color: <?php    echo $settings['cellhover'];?>;}
#event_cal #cal_blank{background-color: <?php    echo $settings['blankdate'];?>;}
#event_cal #current{background-color: <?php    echo $settings['currentdate'];?>;}
#event_cal #allday, #allday a{background-color: <?php    echo $settings['alldaycolor'];?>;}
.label {text-align: right;}
.struct a {padding-left: 8px; padding-right: 5px;}
.struct {padding-bottom: 5px;}
a.tooltip {position: relative; text-decoration: none;}
a.tooltip span{
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
a:hover.tooltip span{display: block;}
#ccm-dashboard-content-inner{float: left!important;}
.category_color{padding: 0px 4px 0px 4px; color: white!important; font-size: 10px;-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;}
</style>

<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Events Preview'), false, false, false);?>
	<div class="ccm-pane-body">
<?php    
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
global $c;
$nh = Loader::helper('navigation');
$link = $nh->getLinkToCollection($c);
$link = $eventify->URLfix($link);

//set this value to one to change the calendar view to Euro format with weeks starting on Mon.
 $euro_cal = 0;

//if the 'BACK' button is selected, bump the month back by one
if(isset($_GET['back'])){
	$date = $_GET['CurrentDate'];

	$date= strtotime('-7 day',strtotime($date));
	$day = date('j', $date) ;
	$month = date('m', $date) ;
	$year = date('Y', $date) ;
	$date = date('Y-m-j',strtotime($year.'-'.$month.'-'.$day));
	$ctID = $_GET['ctID'];
}
//if the 'NEXT' button is selected, bump the month by one
else if (isset($_GET['next'])){
	$date = $_GET['CurrentDate'];

	$date= strtotime('+7 day',strtotime($date));
	$day = date('j', $date) ;
	$month = date('m', $date) ;
	$year = date('Y', $date) ;
	$date = date('Y-m-j',strtotime($year.'-'.$month.'-'.$day));
	$ctID = $_GET['ctID'];
}
// if the month select option is used, set the month to that month
else if (isset($_GET['dateset'])){
	$date =time () ;
	$day = date('d', $date) ;
	$year = $_GET['setyear'];
	$month = $_GET['setmo'];
	$date = date('Y-m-j',strtotime($year.'-'.$month.'-'.$day));
	$ctID = $_GET['ctID'];
}
else
{
// if nothing then set the date to today
$date =time () ;
$day = date('j', $date) ;
$month = date('m', $date) ;
$year = date('Y', $date) ;
$date = date('Y-m-j',strtotime($year.'-'.$month.'-'.$day));
$ctID='All Categories';
}
	
		$db = Loader::db();

	//This gets us the month name 
		$title = date('F', strtotime($date)) ; 

	//Here we find out what day of the week the first day of the week falls on 
		$sunday = strtotime('last Sunday',strtotime($date)) ; 
		

	//days in a week
		$days_in_week = 7 ; 

		
			echo "<table  id='event_cal'>";
			echo "<tr><th colspan=4 id='select'>";
			
	//here we set up our drop down month select
		
		?>
		<form action="<?php    echo $link ;?>" method="GET">
			<a href="<?php    echo $link ;?>back=1&CurrentDate=<?php    echo $date ;?>&CurrentYear=<?php    echo $year  ; ?>&ctID=<?php    echo $ctID ;?>"><?php    echo t('PREV'); ?></a>
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
			<input type="submit" value="go" />
			&nbsp;
			<a href="<?php    echo $link ;?>next=1&CurrentDate=<?php    echo $date ;?>&CurrentYear=<?php    echo $year  ; ?>&ctID=<?php    echo $ctID ;?>"><?php    echo t('NEXT'); ?></a>
		</form>

		<?php    
		//Here we start building the table heads 
			echo "</th><th colspan=3 id='year'>$title $year</th></tr>";
			
			if($euro_cal >= 1){
			
			echo '<tr class="header"><td>'.date('D',strtotime('Monday')).'</td><td>'.date('D',strtotime('Tuesday')).'</td><td>'.date('D',strtotime('Wednesday')).'</td><td>'.date('D',strtotime('Thursday')).'</td><td>'.date('D',strtotime('Friday')).'</td><td>'.date('D',strtotime('Saturday')).'</td><td>'.date('D',strtotime('Sunday')).'</td></tr>';
			
			}else{
			
			echo '<tr class="header"><td>'.date('D',strtotime('Sunday')).'</td><td>'.date('D',strtotime('Monday')).'</td><td>'.date('D',strtotime('Tuesday')).'</td><td>'.date('D',strtotime('Wednesday')).'</td><td>'.date('D',strtotime('Thursday')).'</td><td>'.date('D',strtotime('Friday')).'</td><td>'.date('D',strtotime('Saturday')).'</td></tr>';
			
			}

		//This counts the days in the week, up to 7
			$day_count = 1;
			
			
		//starting day	
			
			echo "<tr>";
			
			
		//go grab the posts, check if they are current, return only current posts
			$events = $eventify->getDateSpan($ctID,date('Y-m-d', $sunday), date('Y-m-d',strtotime('+6 day',$sunday)));
		
		//count up the days, untill we've done all of them in the month
			while ( $day_count <= $days_in_week ) { 
			
		
		//if the current date block being looped through is equal to today's date, then highlight it via CSS	
			if(date('Y-m-d') == date('Y-m-d',strtotime($year.'-'.$month.'-'.date('j',$sunday)))){ $daystyle = 'current';}else{$daystyle = 'day';}
			 ?>
				<td  valign="top" id="<?php    echo $daystyle ; ?>">
				<?php    echo date('j',$sunday) ; ?>
				<div id="cal_day">
				<?php    
	        
				$daynum = date('Y-m-d',$sunday);

				
		//we want to loop through each event, check it's recur state, then check if the current date block being looped through is with that range
					foreach($events as $key => $row){

							$dote = date('Y-m-d',strtotime($row['date']));
							
							if($dote == $daynum){
								$url = $eventify->grabURL($row['eventID']); 
								$events_item = $day_count;
							}
							

				//if any events turn up that recure durring or are on the current date block being looped through, we create an event			
				
								if($events_item==$day_count){
									$ep = Page::getByID($row['eventID']);
									$color = $ep->getAttribute('category_color');
									
									if($row['allday'] != 1){ $itemstyle = 'normal';}else{$itemstyle = 'allday';}
									?>
									<div id="<?php    echo $itemstyle ; ?>">
									<a  href="<?php    echo $url ; ?>" class="tooltip">
									<?php    
										if ($row['allday'] !=1){
											$time = date($settings['time_format'],strtotime($row['sttime'])).' - '.date($settings['time_format'],strtotime($row['entime']));
										}else{
											$time = t('All Day');
										}
										?>
									<?php    
									if($color){
										print '<div style="background-color: '.$color.';" class="category_color">';
										echo $time.' <br/> '.substr($row['title'],0,20) ;
										print '</div>';
									}else{
										echo $time.' <br/> '.substr($row['title'],0,20) ;
									}
									//echo $time.' <br/> '.substr($row['title'],0,20) ;
									?>
									
									<span>
										<h2><?php    echo $row['title'] ; ?></h2>
										<?php    if($row['location']!=''){?>		
										<strong><?php    echo $row['location'] ; ?></strong>
										<br/>
										<?php    } ?>
										<strong><?php    echo $time;?></strong>
										<br/>
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
$sunday = strtotime('+1 day',$sunday);
$day_count++;
}

echo "</tr></table>";
?>
	</div>
</div>