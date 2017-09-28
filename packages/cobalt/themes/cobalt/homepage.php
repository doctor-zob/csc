<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<!-- Showcase Area -->
<div class="row">
  <div class="small-12 column">
  	<?php   
      $a = new Area('Showcase');
      $a->display($c);
    ?>	
  </div>
</div>

<!-- Main Row -->
<div class="row">
  <div class="small-8 column">
    <?php   
      $a = new Area('Main');
      $a->display($c);
    ?>	 
  </div>
  <div id="area-sidebar" class="small-4 column">
    <?php   
      $a = new Area('Sidebar');
      $a->display($c); 
    ?>	
  </div>
</div>
  
<!-- Central Row -->
<div class="row">
  <div id="area-central" class="small-12 columns">
    <?php   $a = new Area('Central'); $a->display($c); ?>
  </div>
</div>
  
<!-- Lower Row -->
<div class="row">
  <div id="area-lower-main" class="small-8 column">
    <?php   $a = new Area('LowerMain'); $a->display($c); ?>
  </div>
  <div id="area-lower-side" class="small-4 column">
    <?php   $a = new Area('LowerSide'); $a->display($c); ?>
  </div>
</div>

<?php    $this->inc('elements/footer.php'); ?>