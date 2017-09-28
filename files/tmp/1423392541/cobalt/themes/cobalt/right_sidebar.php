<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<div class="row">
  <div class="small-8 column">
    <?php   
      $a = new Area('Main');
      $a->display($c);
    ?>	 
  </div>
  <div id="area-sidebar" class="small-4 column">
    <?php   
      $a = new Area('Above Sidebar');
      $a->display($c); 
    ?>
		<?php   
      $a = new GlobalArea('Global Sidebar');
      $a->display($c); 
    ?>
		<?php   
      $a = new Area('Sidebar');
      $a->display($c); 
    ?>	
  </div>
</div>

<?php    $this->inc('elements/footer.php'); ?>