<?php    
defined('C5_EXECUTE') or die("Access Denied.");
class ProeventsConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController {
	
	public function displayItem() {
		$u = new User();
		if($u->isLoggedIn()){
			$tp = PermissionKey::getByHandle('proevent_manager');
			if(is_object($tp)){
				if ($u->isSuperUser() || $tp->can()){
					return true;
				}
			}else{
				return false;
			}
		}
		return false;
	}
}