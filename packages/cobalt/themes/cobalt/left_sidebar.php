<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<!-- Main Row -->
<div class="row">
  <div id="area-sidebar" class="small-4 column">
    <?php   
      $a = new Area('Sidebar');
      $a->display($c); 
    ?>	
  </div>
  <div class="small-8 column">
    <?php   
      $a = new Area('Main');
      $a->display($c);
    ?>	 
  </div>
</div>

<?php    $this->inc('elements/footer.php'); ?>