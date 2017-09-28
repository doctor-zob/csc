<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php        
// get list of items with the display_in_surefyre_calendar set
Loader::model('page_list');
$pl = new PageList();
$pl->filterByAttribute('display_in_surefyre_calendar', 1, '=');
$pl->sortByDisplayOrder();
$pages = $pl->get($itemsToGet = 1000, $offset = 0);

// collate dates
$dates = array();
foreach($pages as $page) {
	$nh = Loader::helper('navigation');
	$link = $nh->getLinkToCollection($page);
	$date = preg_replace('/ .*/', '', $page->vObj->cvDatePublic); // drop time portion of datetime
	$dates[$date][] = array($link, $page->vObj->cvName, $page->vObj->cvDescription);
}

if($displayType == 'calendar') { 
	/////////////// begin calendar option output
?>

<div class="surefyre_calendar" id="surefyre_calendar_<?php        echo $bID;?>"></div>

<script type="text/javascript">
cur_day = 0;
cur_month = 0;
cur_year = 0;

$(document).ready( function() {
	// attach the mouseover class
	$('.surefyre_calendar_row .surefyre_calendar_cell').live('mouseover', function() {
		$(this).addClass('surefyre_calendar_hover');
	});
	$('.surefyre_calendar_row .surefyre_calendar_cell').live('mouseout', function() {
		$(this).removeClass('surefyre_calendar_hover');
	});

	// handle clicks
	$('.surefyre_calendar_cell').live('click', function(e) {
		var list_page = <?php        echo $listPage; ?>;
		if($(this).is('.surefyre_calendar_highlight')) {
			var id = $(this).attr('id');
			top.document.location = '<?php     echo DIR_REL;?>/index.php?cID=' + list_page + '&d=' + escape(id);
		}
	});

	// prevnext
	$('.surefyre_calendar_prev').live('click', function(e) {
		output_surefyre_calendar(cur_year, cur_month, cur_day);
	});

	$('.surefyre_calendar_next').live('click', function(e) {
		output_surefyre_calendar(cur_year, cur_month+2, cur_day);
	});

	output_surefyre_calendar(<?php        echo date('Y') . ', ' . date('m') . ', ' . date('d');?>);
});

function output_surefyre_calendar(year, month, day) {
	month--;
	var me = $('#surefyre_calendar_<?php        echo $bID;?>');
	me.text(''); // clear div
	var mydate = new Date(year, month, day ,0 ,0 ,0 ,0)
	cur_day = mydate.getDate();
	cur_month = mydate.getMonth();
	cur_year = mydate.getFullYear();
	var data = new Array();
	var months = new Array();
	months[0] = '<?php        echo addslashes(('January'));?>';
	months[1] = '<?php        echo addslashes(('February'));?>';
	months[2] = '<?php        echo addslashes(('March'));?>';
	months[3] = '<?php        echo addslashes(('April'));?>';
	months[4] = '<?php        echo addslashes(('May'));?>';
	months[5] = '<?php        echo addslashes(('June'));?>';
	months[6] = '<?php        echo addslashes(('July'));?>';
	months[7] = '<?php        echo addslashes(('August'));?>';
	months[8] = '<?php        echo addslashes(('September'));?>';
	months[9] = '<?php        echo addslashes(('October'));?>';
	months[10]= '<?php        echo addslashes(('November'));?>';
	months[11]= '<?php        echo addslashes(('December'));?>';

	<?php        /// output JS date info array
	$i = 0;
	foreach($dates as $date=>$pages) {
		foreach($pages as $page) {
			echo "data[$i] = new Array();\n";
			echo "data[$i][0] = '" . $date . "';\n";
			echo "data[$i][1] = '" . $page[0] . "';\n";
			echo "data[$i][2] = '" . addslashes(preg_replace('/\\n|\r/', ' ', $page[1])) . "';\n"; // strip CR/CRLF/LF, escape 
			echo "data[$i][3] = '" . addslashes(preg_replace('/\\n|\r/', ' ', $page[2])) . "';\n";
		}
		$i++;
	}
	?>

	// output month name
	var html = '<div class="surefyre_calendar_month">' + months[mydate.getMonth()] + ' ' + mydate.getFullYear() + '</div>';
	me.append(html);

	// get month matrix
	// left hand column is Sunday
	mydate.setDate(1);
	first_day = mydate.getDay();
	start_date = mydate.setDate(1-first_day);

	// output column headers
	html = '<div class="surefyre_calendar_header">';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Su');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Mo');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Tu');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('We');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Th');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Fr');?></div>';
	html += '<div class="surefyre_calendar_cell"><?php        echo t('Sa');?></div>';
	html += '<div style="clear:both;"></div>';
	me.append(html);
	// output rows
	for(a=0; a<6; a++) {
		if(mydate.getDate() < 10 && a>3) {
			continue; // skip further output
		}
		var html = ''
		for(b=0; b<7; b++) {
			var mclass = '';
			var today = false;
			if(cur_month != mydate.getMonth()) {
				mclass = 'surefyre_calendar_othermonth';
			}
			if(<?php        echo date('d');?> == mydate.getDate() && <?php        echo date('m')-1;?> == mydate.getMonth() && <?php        echo date('Y');?> == mydate.getFullYear()) {
				mclass = 'surefyre_calendar_today';
				today = true;
			}
			// check against C5 page dates
			for(i in data) {
				page_date = data[i][0].split('-');
				date2 = new Date(page_date[0], page_date[1]-1, page_date[2], 0, 0, 0, 0); // avoid timezone hour offsets
				if(date2.valueOf() == mydate.valueOf()) {
					mclass = 'surefyre_calendar_highlight';
					if(today) {
						mclass = 'surefyre_calendar_highlight surefyre_calendar_today';
					}
				}
			}
			html += '<div class="surefyre_calendar_cell ' + mclass + '" id="' + mydate.getFullYear() + '-' + (mydate.getMonth()+1) + '-' + mydate.getDate() + '">' + mydate.getDate() + '</div>';
			mydate.setDate(mydate.getDate() + 1);
		}
		html = '<div class="surefyre_calendar_row">' + html + '<div style="clear:both;"></div></div>';
		me.append(html);
	}

	// output prev/next
	html = '<div class="surefyre_calendar_prevnext"><div class="surefyre_calendar_prev"><?php        echo t('Prev');?></div><div class="surefyre_calendar_next"><?php        echo t('Next');?></div><div style="clear:both;"></div></div>';
	me.append(html);

}
</script>
<?php        
} ////////////////////////////// end calendar option output

if($displayType == 'list') {
	$c = Page::getCurrentPage();
	if ($c->isEditMode()) {
		echo 'Calendar Nav (Listing Display Mode)';
	}
	/////////////////////////// begin list option output
	foreach($dates as $date=>$pages) {
		if(strtotime($date) == strtotime($_REQUEST['d']) or !isset($_REQUEST['d'])) {
			echo'<div class="surefyre_calendar_list_date">' . $date . '</div>';
			foreach($pages as $page) {
				echo'<div class="surefyre_calendar_list_item">';
				echo'<div class="surefyre_calendar_list_item_name"><a href="' . $page[0] . '">' . htmlentities($page[1], null, "UTF-8") . '</a></div>';
				echo'<div class="surefyre_calendar_list_item_desc">' . htmlentities($page[2], null, "UTF-8") . '</div>';
				echo'</div>';
			}
		}
	}
?>

<?php       
} ///////////////////////////// end list option output
?>
