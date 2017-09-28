<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsPreviewController extends Controller {

	public function view() {
		$this->redirect('/dashboard/proevents/preview/monthly');
	}
}