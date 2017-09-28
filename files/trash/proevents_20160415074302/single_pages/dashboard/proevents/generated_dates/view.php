<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$fm=Loader::helper('form');
$pkg = Package::getByHandle('proevents');
$url = Loader::helper('concrete/urls')->getToolsURL('edit_generated_event','proevents');
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
div.pager {
    text-align: center;
    margin: 1em 0;
}

div.pager span {
    display: inline-block;
    width: 1.8em;
    height: 1.8em;
    line-height: 1.8;
    text-align: center;
    cursor: pointer;
    background: #000;
    color: #fff;
    margin-right: 0.5em;
}

div.pager span.active {
    background: #c00;
}
.ccm-results-list tr.booked td{background-color: #ffbfbf!important;}
</style>
<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Generated Dates'), false, false, false);?>
	<div class="ccm-pane-body ccm-ui">
		<?php   
		if($remove_eid){
		?>
		<div class="alert-message block-message error">
		  <a class="close" href="<?php    echo $this->action('clear_warning');?>">×</a>
		  <p><strong><?php    echo t('Holy guacamole! This is a warning!');?></strong></p><br/>
		  <p><?php    echo t('Are you sure you want to delete ').t($remove_name).'?';?></p>
		  <p><?php    echo t('This action may not be undone!');?></p>
		  <div class="alert-actions">
		    <a class="btn small" href="<?php    echo BASE_URL.DIR_REL;?>/index.php/dashboard/proevents/generated_dates/delete/<?php    echo $remove_eid;?>/<?php    echo $remove_name;?>/"><?php    echo t('Yes Remove This');?></a> <a class="btn small" href="<?php    echo $this->action('clear_warning');?>"><?php    echo t('Cancel');?></a>
		  </div>
		</div>
		<?php   
		}
		?>
		<form class="form-search" style="float: left;">
		  	<input type="text" name="search" value="<?php  echo $_SESSION['search']?>" class="input-medium search-query">
			<select name="search_type">
		      <option <?php   if($_SESSION['search_type'] == 'date'){echo 'selected';}?>><?php  echo t('date')?></option>
			  <option <?php   if($_SESSION['search_type'] == 'title'){echo 'selected';}?>><?php  echo t('title')?></option>
			  <option <?php   if($_SESSION['search_type'] == 'description'){echo 'selected';}?>><?php  echo t('description')?></option>
			</select>
			<button type="submit" class="btn"><?php  echo t('Search')?></button>
		</form>
		<form style="float: left;padding-left: 6px;" action="<?php  echo $this->action('clear_search')?>">
			<button type="submit" class="btn"><?php  echo t('Clear')?></button>
		</form>
		<table border="0" class="ccm-results-list paginated" cellspacing="0" cellpadding="0" id="dates_list">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th><?php    echo t('Date')?></th>
					<th><?php    echo t('Title')?></th>
					<th><?php    echo t('Status')?></th>
					<th><?php    echo t('Price')?></th>
					<th><?php    echo t('Qty')?></th>
				</tr>
			</thead>
			<tbody>
			<?php  
			if($fullDateList){
				$count = count($fullDateList);
				foreach($fullDateList as $key=>$date){
					$i++;
					?>
						<tr data-set="<?php  echo $date['eID']?>" class="<?php  echo $date['status']?>">
							<td width="88px" class="align_top">
								<a href="javascript:;" rel="<?php  echo $date['eID']?>" class="eventtooltip icon edit" onClick="edit_event(this)"><span><?php  echo t('Edit this Event')?></span></a> &nbsp;
								<a href="<?php  echo $this->action('delete_check',$date['eID'],$date['title'].' - '.date(t('M d, Y'),strtotime($date['date'])))?>" class="eventtooltip icon delete"><span><?php  echo t('Remove this Event')?></span></a>
							</td>
							<td><?php  echo date('M d, Y',strtotime($date['date'])).' '.date('g:ia',strtotime($date['sttime'])).'-'.date('g:ia',strtotime($date['entime']))?></td>
							<td><?php  echo $date['title']?></td>
							<td><?php  echo $date['status']?></td>
							<td  width="80px"><?php   print  $date['event_price']?></td>
							<td  width="80px"><?php   print  $date['event_qty']?></td>
						</tr>
					<?php  
					}
			}
			?>
			</tbody>
		</table>
	</div>
	<div class="ccm-pane-footer">
	
	</div>
	<script type="text/javascript">
	$('table.paginated').each(function() {
	    var currentPage = 0;
	    var numPerPage = 25;
	    var $table = $(this);
	    $table.bind('repaginate', function() {
	        $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
	    });
	    $table.trigger('repaginate');
	    var numRows = $table.find('tbody tr').length;
	    var numPages = Math.ceil(numRows / numPerPage);
	    var $pager = $('<div class="pagination ccm-pagination"></div>');
	    var $list = $('<ul></ul>');
	    for (var page = 0; page < numPages; page++) {
	        $('<li class="page-number"></li>').html('<a href="javascript:;">' + (page + 1) +'</a>').bind('click', {
	            newPage: page
	        }, function(event) {
	            currentPage = event.data['newPage'];
	            $table.trigger('repaginate');
	            $(this).addClass('active').siblings().removeClass('active');
	        }).appendTo($list).addClass('clickable');
	    }
	    $list.appendTo($pager);
	    $pager.appendTo('.ccm-pane-footer').find('li.page-number:first').addClass('active');
	});
	
	edit_event = function(t){
		var id = $(t).attr('rel');
		var url = '<?php    echo $url?>?id='+id;
	
		var el = document.createElement('div');
		el.id = "myNewElementmyDialogContent";
		el.innerHTML = $('#addtolist').html();
		el.style.display = "none";
		$('#addtolist').parent().append(el);
		jQuery.fn.dialog.open({
			title: '<?php  echo t('Generated Date Info')?>',
			href: url,
			width: 620,
			modal: false,
			height: 425
		});
	}
	</script>