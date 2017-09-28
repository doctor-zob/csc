<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
Loader::model('user');
Loader::model('userinfo');
//print $_REQUEST['ccID'];
function isValidEmail($email){
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}
if(strlen($_REQUEST['invite_emails'])<1){
	print t('You must enter at least one email address.');
	exit;
}
if(strpos($_REQUEST['invite_emails'], ",") > 0){
	print t('Your email list is in the wrong format');
	exit;
}

$emails_array = explode("\r\n", $_REQUEST['invite_emails']);
foreach($emails_array as $email){
	if(isValidEmail($email)!=true){
		print t('We\'re sorry.  But one or more emails entered are not valid email addresses. Please try again');
		exit;
	}
}

$ui = UserInfo::getByID($_REQUEST['uID']);
$uName = $ui->getUserFirstName().' '.$ui->getUserLastName();

$event = Page::getByID($_REQUEST['ccID']);
$eName = $event->getCollectionName();
$eDescription = $event->getCollectionDescription();
$eLink = BASE_URL.Loader::helper('navigation')->getLinkToCollection($event);

$mh = Loader::helper('mail');
$mh->addParameter('uName', $uName);
$mh->addParameter('eName', $eName);
$mh->addParameter('eDescription', $eDescription);
$mh->addParameter('eLink', $eLink);
$mh->from('events@'.substr(BASE_URL,7));

foreach($emails_array as $email){
	$mh->to( $email ); 
}
$mh->load('event_invite','proevents');
$mh->setSubject(t('You have been invited to an Event!'));
@$mh->sendMail(); 

print 'success';
exit;
?>