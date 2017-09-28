<?php      
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::helper('eventify','proevents');

//-----------------------------------------------------//
//          !!!ATTENTION DEVELOPERS!!!                 //
//-----------------------------------------------------//
//this 'model' is now deprecated
//please use:
//  $eventify = Loader::helper('eventify','proevents');
//  $var = $eventify->method();
//-----------------------------------------------------//

class Eventify Extends EventifyHelper{	


}