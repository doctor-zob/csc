<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
$dth = Loader::helper('form/date_time_time','proevents'); 
$nh = Loader::helper('navigation');
Loader::model('block');
Loader::model('page');

$bID = $_REQUEST['bID'];
$cID = $_REQUEST['ccID'];
$b = Block::getByID($bID);
$controller = $b->getController();

if($_REQUEST['currentPage']){
	$controller->paginationPage = $_REQUEST['currentPage'];
}

if($_REQUEST['type']){
	$controller->listType = $_REQUEST['type'];
}

if($_REQUEST['category']){
	$controller->category = $_REQUEST['category'];
}


extract($controller->settings);	

$events = $controller->getEvents($_REQUEST['date']);
$truncateChars = $controller->truncateChars;

foreach($events as $date_string=>$event) {
	$date_array = $dth->translate_from_string($date_string);
	$date = $date_array['date'];

	if($date != $dateP || $_REQUEST['joinDays']=='false'){
		$args = array('date_string'=>$date_string,'event'=>$event,'truncateChars'=>$truncateChars,'joinDays'=>true);
		Loader::packageElement('event_list_theme','proevents',$args);
	}else{
		$args = array('date_string'=>$date_string,'event'=>$event,'truncateChars'=>$truncateChars);
		Loader::packageElement('event_list_theme','proevents',$args);	
	}

	$dateP = $date;
}

if(count($events)<1){
	print 'No events at this time.';
}

$el = $controller->el;

if ($controller->isPaged && $controller->num > 0 && is_object($el)) {
	$collection = Page::getByID($cID);
	$url = BASE_URL.$nh->getLinkToCollection($collection);
	$_SERVER['QUERY_STRING'] = '1=1';
	$paging = $el->displayAjaxPaging($url,true);
	print $paging;
}
?>
