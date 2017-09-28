<?php   defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); 
$imgHelper = Loader::Helper('image');
?>

<div class="row">
  <div class="small-8 column">
    <div class="pageSection">
      <?php   $ai = new Area('Blog Post Header'); $ai->display($c); ?>
    </div>
    <div class="pageSection">
      <h1><?php   echo $c->getCollectionName(); ?></h1>
      <p class="meta"><?php  
        echo t('Posted by %s on %s',
          $c->getVersionObject()->getVersionAuthorUserName(),
          $c->getCollectionDatePublic(DATE_APP_GENERIC_MDY_FULL));
      ?></p>		
    </div>
    <div class="pageSection">
    	<?php   
				$file = $c->getAttribute("blog_post_full_image");
				if (is_object($file)) {
					$im = Loader::helper('image'); 
					$im->output($file);
				}
			?>
      <?php   $as = new Area('Main'); $as->display($c); ?>
    </div>
    <div class="pageSection">
      <?php   $a = new GlobalArea('Blog After Post'); $a->display($c); ?>
    </div>
  </div>
  <div id="area-sidebar" class="small-4 column">
    <?php   
			$as = new Area('Sidebar');
			$as->display($c);
    ?>
    <?php   
			$as = new GlobalArea('Blog Sidebar');
			$as->display($c); 
    ?>	
  </div>	
</div>

<?php   $this->inc('elements/footer.php'); ?>