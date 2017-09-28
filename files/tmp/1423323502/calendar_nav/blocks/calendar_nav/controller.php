<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php       
	class CalendarNavBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "A simple calendar block for displaying date-based links to Concrete5 Resources.";
		protected $btName = "Calendar Nav";
		protected $btTable = 'btSurefyreCalendarNav';
		protected $btInterfaceWidth = "350";
		protected $btInterfaceHeight = "300";
		
	}
?>
