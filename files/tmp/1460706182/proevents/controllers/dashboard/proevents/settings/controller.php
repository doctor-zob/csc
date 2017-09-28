<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsSettingsController extends Controller {

	public function view() {
		$this->redirect('/dashboard/proevents/settings/events');
	}
}