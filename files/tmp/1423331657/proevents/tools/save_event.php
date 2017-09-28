<?php     
defined('C5_EXECUTE') or die(_("Access Denied."));

$db = loader::db();
$ueID = $db->getOne("SELECT ueID FROM btProEventUserSaved WHERE eventID = ? AND uID = ?",array($_REQUEST['event'],$_REQUEST['user']));

if($ueID){
	$db->execute("DELETE FROM btProEventUserSaved WHERE ueID = ?",array($ueID));
}else{
	$db->execute("INSERT INTO btProEventUserSaved (eventID,uID) VALUES (?,?)",array($_REQUEST['event'],$_REQUEST['user']));
}

exit;
?>