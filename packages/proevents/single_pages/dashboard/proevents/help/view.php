<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
?>
	<?php   echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('ProEvents Help'), false, false, false);?>
	<div class="ccm-pane-body">
		<h2><?php    echo t('Get Help')?></h2>
		<p><?php    echo t('Proevents & related ProEvents products are working to improve the help and roadmap pages found on Concrete5.org.</p>
		<p>Please head over to <a href="http://www.concrete5.org/marketplace/addons/proevents/documentation/" target="_blank">http://www.concrete5.org/marketplace/addons/proevents/documentation/</a> for updated help and roadmaps.')?></p>
		
		<h2><?php    echo t('Get Support')?></h2>
		<p><?php    echo t('You can also report bugs and search previously posted bugs & solutions here: <a href="http://www.concrete5.org/marketplace/addons/proevents/support/" target="_blank">http://www.concrete5.org/marketplace/addons/proevents/support/</a>')?></p>
	</div>
