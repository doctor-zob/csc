<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h2>Choose your settings for this calendar block. </h2>


<?php        
echo $form->label('displayType', 'Display mode');
echo $form->select('displayType', array('calendar'=>t('Calendar'), 'list'=>t('List')), $displayType, null);

echo '<br /><br />';

print t('Choose a page to use as the calendar\'s item listing page when a highlighted date is clicked. Note that the chosen page should have a calendar block in it set to List mode. This selection has no effect when the Display Mode is set to List.');
$form = Loader::helper('form/page_selector');
print $form->selectPage('listPage', (isset($listPage) ? $listPage : null));
?>
<br />
<br />
<br />
<div style="text-align:center;">Calendar Nav is a free block for Concrete5.<br />If you would like to make a donation please <a href="http://surefyre.com/donate?s=calendarnav" target="_blank">click here</a>.</div>
