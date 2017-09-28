<?php  
defined('C5_EXECUTE') or die(_("Access Denied.")); 
$id =  $_REQUEST['id'];
Loader::model('event_item','proevents');
$date = new EventItemDate($id);
$fm = Loader::helper('form');
?>
<style type="text/css">
	 td {padding: 4px!important}
	.lable{text-align: left;width: 120px!important;vertical-align: top;}
</style>
<div class="ccm-ui">
	<style type="text/css">
		
	</style>
	<form action="<?php  echo BASE_URL.DIR_REL?>/index.php/dashboard/proevents/generated_dates/date_edit/" method="post" name="update_event">
	<table>
		<tr>
			<td class="lable"><?php  echo t('Title')?></td>
		</tr>
		<tr>
			<td><?php  echo $fm->text('title',$date->title);?></td>
		</tr>
		<tr>
			<td class="lable"><?php  echo t('Status')?></td>
		</tr>
		<tr>
			<?php  
			$status = array(
				''=>t('none'),
				'available'=>t('Available'),
				'booked'=>t('Booked')
			);
			?>
			<td><?php  echo $fm->select('status',$status,$date->status);?><br/><br/></td>
		</tr>
		<tr>
			<td class="lable"><?php  echo t('Price')?></td>
		</tr>
		<tr>
			<td><?php  echo $fm->text('event_price',$date->event_price);?></td>
		</tr>
		<tr>
			<td class="lable"><?php  echo t('Qty')?></td>
		</tr>
		<tr>
			<td><?php  echo $fm->text('event_qty',$date->event_qty);?></td>
		</tr>
		<tr>
			<td class="lable"><?php    echo t('Event Content')?></td>
		</tr>
		<tr>
			<td>
			<?php   echo $fm->hidden('eID',$id);?>
			<?php     Loader::element('editor_init'); ?>
			<?php     Loader::element('editor_config'); ?>
			<?php     Loader::element('editor_controls', array('mode'=>'full')); ?>
			<?php    echo $fm->textarea('description', $date->description, array('style' => 'width: 100%; font-family: sans-serif;', 'class' => 'ccm-advanced-editor'))?>
			<br/>
			</td>
		</tr>
		<tr>
			<td></td>
		</tr>
		<tr>
			<td>
			<?php    $ih = Loader::helper('concrete/interface'); ?>
	        <?php    print $ih->submit(t('Update This Date'), 'update_event', 'right', 'primary'); ?>
			</td>
		</tr>
	</table>
	</form>
</div>