<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsPreviewWeeklyController extends Controller {

	public function view(){

		$this->set('controller',$this);
		$eventify = Loader::helper('eventify','proevents');
		$this->set('eventify',$eventify);
	}

}