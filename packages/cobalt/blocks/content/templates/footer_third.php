<?php   defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="small-12 large-4 column footer-third">
	<div class="footer-wrap">
	<?php  
    $content = $controller->getContent();
    print $content;
  ?>
  </div>
</div>