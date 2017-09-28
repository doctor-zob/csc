<?php    
defined('C5_EXECUTE') or die("Access Denied.");

class PePostPageTypeController extends Controller {
	
	public function on_start(){
		Loader::library('cache');
		cache::flush();
	}

}