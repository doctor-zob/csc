<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardproeventsController extends Controller {
	


	public function view() {
		$this->redirect('/dashboard/proevents/list/');
	}
	
}