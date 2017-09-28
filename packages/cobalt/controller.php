<?php   
defined('C5_EXECUTE') or die(_("Access Denied."));

class CobaltPackage extends Package {

	protected $pkgHandle = 'cobalt';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Cobalt is a responsive business theme for C5 that integrates Foundation Framework CSS.");
	}

	public function getPackageName() {
		return t("Cobalt");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// Install Theme
		PageTheme::add('cobalt', $pkg);	
		
		// Install Page Types
		if(!is_object(CollectionType::getByHandle('homepage'))) {
			$data['ctHandle'] = 'homepage';
			$data['ctName'] = t('Homepage');
			$hpt = CollectionType::add($data, $pkg);
		}
	}
     
}
?>