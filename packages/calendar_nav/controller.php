<?php       
defined('C5_EXECUTE') or die(_("Access Denied."));

class CalendarNavPackage extends Package {

     protected $pkgHandle = 'calendar_nav';
     protected $appVersionRequired = '5.4.0';
     protected $pkgVersion = '1.0.8';

     public function getPackageDescription() {
          return t("Calendar-based navigation.");
     }

     public function getPackageName() {
          return t("Calendar Nav");
     }
     
     public function install() {
          $pkg = parent::install();
     
	# create custom attribute
	Loader::model('collection_attributes');
	$att = AttributeType::getByHandle('boolean');
	$testAttribute3=CollectionAttributeKey::getByHandle('display_in_surefyre_calendar');
	if( !is_object($testAttribute3) ) {
		CollectionAttributeKey::add($att, array('akHandle' => 'display_in_surefyre_calendar', 'akName' => t('Show this item in Calendar Nav'), 'akIsSearchable' => true, 'akCheckedByDefault' => true), $pkg);
	}

          // install block 
          BlockType::installBlockTypeFromPackage('calendar_nav', $pkg); 
     }
     
}
?>
