<?php    defined('C5_EXECUTE') or die("Access Denied."); ?>

<div id="footer">

  <div id="footer-upper-wrap">
    <div class="row">
      <div id="footer-upper" class="small-12 column">
        <?php   
        $a = new Area('FooterUpper');
        $a->display($c); 
        ?>
        
        <?php   
        $a = new GlobalArea('FooterUpperGlobal'); 
        $a->display($c); 
        ?>
      </div>
    </div>
  </div>
    
  <div id="footer-lower-wrap">
    <div class="row">
      <div id="footer-lower" class="small-12 column">
        <?php   
        $a = new GlobalArea('FooterGlobal');
        $a->display($c); 
        ?>
      </div>
    </div>
  </div>

</div>



<!-- end page -->
</div>

<?php   Loader::element('footer_required'); ?>

</body>
</html>