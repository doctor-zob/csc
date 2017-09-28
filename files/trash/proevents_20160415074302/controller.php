<?php    

defined('C5_EXECUTE') or die(_("Access Denied."));

class ProeventsPackage extends Package {

	protected $pkgHandle = 'proevents';
	protected $appVersionRequired = '5.6.0';
	protected $pkgVersion = '11.1.0';
	
	public function getPackageDescription() {
		return t("A professional Event package");
	}
	
	public function getPackageName() {
		return t("Pro Events");
	}
	
	public function install() {
	
		Config::save('ENABLE_CACHE', 0);
		      
		$pkg = parent::install();
		
		//install blocks
	  	BlockType::installBlockTypeFromPackage('pro_event_list', $pkg);	
		
		$this->load_required_models();
		
		$this->install_event_attributes($pkg);
		
		$this->add_se_pages($pkg);
         
      // install pages
      $iak = CollectionAttributeKey::getByHandle('icon_dashboard');
      
      $cp = SinglePage::add('/dashboard/proevents', $pkg);
      $cp = Page::getByPath('/dashboard/proevents');
      $cp->update(array('cName'=>t('Pro Events'), 'cDescription'=>t('Professional event management')));
      
      $pel = SinglePage::add('/dashboard/proevents/list', $pkg);
      $pel = Page::getByPath('/dashboard/proevents/list');
      $pel->setAttribute($iak,'icon-list-alt');
      
      $an = SinglePage::add('/dashboard/proevents/add_event', $pkg);
      $an = Page::getByPath('/dashboard/proevents/add_event');
      $an->update(array('cName'=>t('Add/Edit')));
      $an->setAttribute($iak,'icon-calendar');
      
      $pep = SinglePage::add('/dashboard/proevents/preview', $pkg);
      $pep = Page::getByPath('/dashboard/proevents/preview');
      $pep->setAttribute($iak,'icon-search');
      
      SinglePage::add('/dashboard/proevents/preview/monthly', $pkg);
      SinglePage::add('/dashboard/proevents/preview/weekly', $pkg);
      
      /*
      $pee = SinglePage::add('/dashboard/proevents/exclude_dates', $pkg);
      $pee->setAttribute($iak,'icon-remove-circle');
      */
      $generated_dates = SinglePage::add('/dashboard/proevents/generated_dates', $pkg);
      $generated_dates = Page::getByPath('/dashboard/proevents/generated_dates');
	  $generated_dates->setAttribute($iak,'icon-list');
			
      $pes = SinglePage::add('/dashboard/proevents/settings', $pkg);
      $pes = Page::getByPath('/dashboard/proevents/settings');
      $pes->setAttribute($iak,'icon-wrench');
      
      SinglePage::add('/dashboard/proevents/settings/events', $pkg);
      
      $peh = SinglePage::add('/dashboard/proevents/help', $pkg);
      $peh = Page::getByPath('/dashboard/proevents/help');
      $peh->setAttribute($iak,'icon-question-sign');
      
      $this->setDefaults();
   
	}

	public function uninstall(){
			
		$results= Page::getByPath('/event');
		$results->delete();
		$db= Loader::db();
		$db->Execute("DELETE from btProEventDates");
		parent::uninstall();
	}
	
	public function upgrade(){
	
		$db = Loader::db();
		
		$this->load_required_models();
		
		$pkg = Package::getByHandle('proevents');
		
		////////////////////////////////////////////////////////////////////////////
		//pre v5.8 updates
		///////////////////////////////////////////////////////////////////////////
		//$evset = AttributeSet::getByHandle('proevent');
		//$checkn = AttributeType::getByHandle('boolean'); 
		//$pageless=CollectionAttributeKey::getByHandle('event_pageless'); 
		//if( !is_object($pageless) ) {
	    // 	CollectionAttributeKey::add($checkn, 
	    // 	array('akHandle' => 'event_pageless', 
	    // 	'akName' => t('Publish without details page?'),
	    // 	),$pkg)->setAttributeSet($evset); 
	  	//}
		
		
		////////////////////////////////////////////////////////////////////////////
		//pre v3 updates
		///////////////////////////////////////////////////////////////////////////
		$eaku = AttributeKeyCategory::getByHandle('collection');
		$multidateAttribute = AttributeType::getByHandle('multi_date');
		
		$eaku = AttributeKeyCategory::getByHandle('collection');
  		$eaku->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);
		$evset = AttributeSet::getByHandle('proevent');
		$evseta = AttributeSet::getByHandle('proevent_additional_attributes');
		
		if(!is_object($multidateAttribute) || !intval($multidateAttribute->getAttributeTypeID()) ) {
			
			$multidateAttribute = AttributeType::add('multi_date', t('Multi Date'), $pkg);
			$eaku->associateAttributeKeyType(AttributeType::getByHandle('multi_date')); 	  
			
			$eventmulti=CollectionAttributeKey::getByHandle('event_multidate'); 
			if( !is_object($eventmulti)   || $eventman==false) {
		     	CollectionAttributeKey::add($multidateAttribute, 
		     	array('akHandle' => 'event_multidate', 
		     	'akName' => t('Event Dates'),
		     	'akIsSearchable' => '1', 
     			'akIsSearchableIndexed' => '1', 
     			'akDateDisplayMode'=>'date_time_time'
		     	),$pkg)->setAttributeSet($evset); 
		  	}
  			
  			if(!is_object($evseta)){
  				$evseta = $eaku->addSet('proevent_additional_attributes', t('Pro Events Additional Attributes'),$pkg);
  			}
  			
  			$ak=CollectionAttributeKey::getByHandle('event_recur');
  			$akv = SelectAttributeTypeOption::getByValue('twice per month');
  			if($akv){
  				$akv->delete();
  			}
  			
  			SinglePage::add('/dashboard/proevents/settings/events/', $pkg);
  			 
  			SinglePage::add('/dashboard/proevents/help/', $pkg);
		}
		
		////////////////////////////////////////////////////////////////////////////
		//pre v4 updates
		///////////////////////////////////////////////////////////////////////////
		if(version_compare(APP_VERSION,'5.4.1.1', '>')){
			$colorpicker = AttributeType::getByHandle('event_color');
			if(!is_object($colorpicker) || !intval($colorpicker->getAttributeTypeID()) ) { 
				$colorpicker = AttributeType::add('event_color', t('Color Picker'), $pkg);	  
			}
		  	$catcolor=CollectionAttributeKey::getByHandle('category_color'); 
			if( !is_object($catcolor) ) {
		     	CollectionAttributeKey::add($colorpicker, 
		     	array('akHandle' => 'category_color', 
		     	'akName' => t('Category Color'), 
		     	'akIsSearchable' => '1', 
		     	'akIsSearchableIndexed' => '1'
		     	),$pkg)->setAttributeSet($evset); 
		  	}
		}else{
			$db->Execute("UPDATE Packages SET pkgVersion='3.1.2' WHERE pkgHandle='proevents'");
			throw new Exception(t('Attention!  You are missing key features of ProEvents v4 because you failed to upgrade your C5 core to v5.4.2 first.'));  
      		exit;
		}
		
		////////////////////////////////////////////////////////////////////////////
		//pre v7 updates
		///////////////////////////////////////////////////////////////////////////
		$iak = CollectionAttributeKey::getByHandle('icon_dashboard');
      

		$pel = Page::getByPath('/dashboard/proevents/list');
		$pel->setAttribute($iak,'icon-list-alt');
		
		$an = Page::getByPath('/dashboard/proevents/add_event');
		$an->setAttribute($iak,'icon-calendar');
		
		$pep = Page::getByPath('/dashboard/proevents/preview');
		$pep->setAttribute($iak,'icon-search');
		
		/*
		$pee = Page::getByPath('/dashboard/proevents/exclude_dates');
		$pee->setAttribute($iak,'icon-remove-circle');
		*/
		
		$pes = Page::getByPath('/dashboard/proevents/settings');
		$pes->setAttribute($iak,'icon-wrench');
		
		$peh = Page::getByPath('/dashboard/proevents/help');
		$peh->setAttribute($iak,'icon-question-sign');
		
		$checkn = AttributeType::getByHandle('boolean'); 
		$eventgrouped=CollectionAttributeKey::getByHandle('event_grouped'); 
		if( !is_object($eventgrouped) ) {
	     	CollectionAttributeKey::add($checkn, 
	     	array('akHandle' => 'event_grouped', 
	     	'akName' => t('Group Dates?'),
	     	),$pkg)->setAttributeSet($evset); 
	  	}
	  	
	  	
	  	////////////////////////////////////////////////////////////////////////////
		//pre v8 updates
		///////////////////////////////////////////////////////////////////////////
	  	$exclude_dates_remove = Page::getByPath('/dashboard/proevents/exclude_dates');
	  	if(is_object($exclude_dates_remove) || $exclude_dates_remove->cID > 0){  
			$exclude_dates_remove->delete();
		}
		
		$generated_dates = Page::getByPath('/dashboard/proevents/generated_dates');
		if(!is_object($generated_dates) || $generated_dates->cID < 1){  
			$generated_dates = SinglePage::add('/dashboard/proevents/generated_dates', $pkg);
			$generated_dates->setAttribute($iak,'icon-list');
		}
		
		$multidateAttribute = AttributeType::getByHandle('multi_date');
		
		$eventexclude=CollectionAttributeKey::getByHandle('event_exclude'); 
		if( !is_object($eventexclude) ) {
	     	CollectionAttributeKey::add($multidateAttribute, 
	     	array('akHandle' => 'event_exclude', 
	     	'akName' => t('Event Exclude Dates'),
	     	'akIsSearchable' => '1', 
			'akIsSearchableIndexed' => '1', 
			'akDateDisplayMode'=>'date'
	     	),$pkg)->setAttributeSet($evset); 
	  	}
	  	
		
		////////////////////////////////////////////////////////////////////////////
		//pre v9 updates
		////////////////////////////////////////////////////////////////////////////
		
		$event_manager = UserAttributeKey::getByHandle('event_manager');
		if($event_manager->akID){
			$event_manager->delete();
		}
		
		$group = Group::getByName('ProEvents Manager');
		if(!$group || $group->getGroupID() < 1){
			$group = Group::add('ProEvents Manager','Can create and edit Events');
		}
		
		$pk = PermissionKey::getByHandle('proevent_manager');
		if(!$pk || $pk->getPermissionKeyID() < 1){
			$pk = AdminPermissionKey::add('admin','proevent_manager',t('Create Events'),t('User can use ProEvents frontend features.'),true,false,$pkg);
		}
		
		$pe = GroupPermissionAccessEntity::getOrCreate($group);
		
		$pa = AdminPermissionAccess::create($pk);
		$pa->addListItem($pe, false, 10);
		
		$pka = new PermissionAssignment();
		$pka->setPermissionKeyObject($pk);
		$pka->assignPermissionAccess($pa);
		
		
		
		
		////////////////////////////////////////////////////////////////////////////
		//pre v10 updates
		///////////////////////////////////////////////////////////////////////////
		
		$colorpicker = AttributeType::getByHandle('event_color');
		if(!is_object($colorpicker) || !intval($colorpicker->getAttributeTypeID()) ) { 
			$eaku = AttributeKeyCategory::getByHandle('collection');
			$colorpicker = AttributeType::add('event_color', t('Event Color Picker'), $pkg);	
			$eaku->associateAttributeKeyType($colorpicker);   
			$atID = $colorpicker->getAttributeTypeID();
			$catcolor = CollectionAttributeKey::getByHandle('category_color'); 
			$akID = $catcolor->getAttributeKeyID();
			$db->execute("UPDATE AttributeKeys SET atID=? WHERE akID=?",array($atID,$akID));	
		}
		
		
		$eventPageType = CollectionType::getByHandle('pe_post');
		if(!is_object($eventPageType) || $eventPageType==false){  
	  		$eventPageType = array('ctHandle' => 'pe_post', 'ctName' => t('ProEvents Post'),'ctIcon'=>t('template3.png'));
      		CollectionType::add($eventPageType, $pkg);
      		$db->execute("UPDATE btProEventSettings SET ctID = ?",array($eventPageType->ctID));
      	}
      	
      	$google_page = Page::getByPath('/google-event');
      	if(!$google_page || $google_page->cID < 1){
      		$seteventAt = Page::getByID(HOME_CID);
      		$eventPageType = CollectionType::getByHandle('pe_post');
	      	$data = array('cName' => t('Google Event'), 'cDescription' => t('Placeholder Google Events'), 'cDatePublic' => date('Y-m-d'));
			$google_page = $seteventAt->add($eventPageType, $data);	
			$data = array('content' => 'google data');
			$bt = BlockType::getByHandle('content');
			$b = $google_page->addBlock($bt, 'Main', $data);
			$b->setCustomTemplate('google_event');
			$db->Execute('update Pages set cIsSystemPage = 1 where cID = ?', array($google_page->getCollectionID()));
		}
		
		
		//payment / qty update & booking tab
		
		$evsbt = AttributeSet::getByHandle('proevent_booking_attributes');
		if(!is_object($evsbt)){
			$evsbt = $eaku->addSet('proevent_booking_attributes', t('Pro Events Booking Attributes'),$pkg);
		}
  			
		$payment = $db->getOne("SELECT column_name from information_schema.columns WHERE column_name = 'event_price'");
		if(!$payment){
			$db->execute("ALTER TABLE btProEventDates ADD event_qty INT(6)");
			$db->execute("ALTER TABLE btProEventDates ADD event_price DECIMAL(2,2)");
		}
		
		$price = AttributeType::getByHandle('price');
		if(!is_object($price) || !intval($price->getAttributeTypeID()) ) { 
			$price = AttributeType::add('price', t('Price'), $pkg);	  
		}
		
		$textn = AttributeType::getByHandle('text'); 
		$event_qty = CollectionAttributeKey::getByHandle('event_qty'); 
		if( !is_object($event_qty) ) {
	     	CollectionAttributeKey::add($textn, 
	     	array('akHandle' => 'event_qty', 
	     	'akName' => t('Event Qty'), 
	     	),$pkg)->setAttributeSet($evsbt); 
	  	}
	  	
	  	$event_price = CollectionAttributeKey::getByHandle('event_price'); 
		if( !is_object($event_price) ) {
	     	CollectionAttributeKey::add($price, 
	     	array('akHandle' => 'event_price', 
	     	'akName' => t('Event Price'), 
	     	),$pkg)->setAttributeSet($evsbt); 
	  	}
		
		parent::upgrade();
		
		Config::save('ENABLE_CACHE', 0);
	}
	
	function install_event_attributes($pkg) {
  
  	$eaku = AttributeKeyCategory::getByHandle('collection');
  	$eaku->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);
  	$evset = $eaku->addSet('proevent', t('Pro Events'),$pkg);
  	$evsbt = $eaku->addSet('proevent_booking_attributes', t('Pro Events Booking Attributes'),$pkg);
  	$evsot = $eaku->addSet('proevent_additional_attributes', t('Pro Events Additional Attributes'),$pkg);
  	
  	$euku = AttributeKeyCategory::getByHandle('user');
  	$euku->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_SINGLE);
  	$uset = $euku->addSet('user_set', t('Events Package'),$pkg);
  	
 	$timen = AttributeType::getByHandle('time'); 

 	if(!is_object($timen) || !intval($timen->getAttributeTypeID())){ 
  		$timen = AttributeType::add('time','Time', $pkg);
  		$eaku->associateAttributeKeyType(AttributeType::getByHandle('time'));
  	}
  	
  	$multidateAttribute = AttributeType::getByHandle('multi_date');
	if(!is_object($multidateAttribute) || !intval($multidateAttribute->getAttributeTypeID()) ) { 
		$multidateAttribute = AttributeType::add('multi_date', t('Multi Date'), $pkg);	
		$eaku->associateAttributeKeyType(AttributeType::getByHandle('multi_date'));  
	}
	
	$colorpicker = AttributeType::getByHandle('event_color');
	if(!is_object($colorpicker) || !intval($colorpicker->getAttributeTypeID()) ) { 
		$colorpicker = AttributeType::add('event_color', t('Event Color Picker'), $pkg);
		$eaku->associateAttributeKeyType(AttributeType::getByHandle('event_color'));  	  
	}
	
	$price = AttributeType::getByHandle('price');
	if(!is_object($price) || !intval($price->getAttributeTypeID()) ) { 
		$price = AttributeType::add('price', t('Price'), $pkg);	  
	}
  	
  	$price = AttributeType::getByHandle('price');
  	$timen = AttributeType::getByHandle('time'); 
  	$multidateAttribute = AttributeType::getByHandle('multi_date');
  	$colorpicker = AttributeType::getByHandle('event_color');
  	
  	 
	$eventmulti=CollectionAttributeKey::getByHandle('event_multidate'); 
	if( !is_object($eventmulti) ) {
     	CollectionAttributeKey::add($multidateAttribute, 
     	array('akHandle' => 'event_multidate', 
     	'akName' => t('Event Dates'),
     	'akIsSearchable' => '1', 
		'akIsSearchableIndexed' => '1', 
		'akDateDisplayMode'=>'date_time_time'
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
	$eventexclude=CollectionAttributeKey::getByHandle('event_exclude'); 
	if( !is_object($eventexclude) ) {
     	CollectionAttributeKey::add($multidateAttribute, 
     	array('akHandle' => 'event_exclude', 
     	'akName' => t('Event Exclude Dates'),
     	'akIsSearchable' => '1', 
		'akIsSearchableIndexed' => '1', 
		'akDateDisplayMode'=>'date'
     	),$pkg)->setAttributeSet($evset); 
  	}
  
    $checkn = AttributeType::getByHandle('boolean'); 
  	$eventsec=CollectionAttributeKey::getByHandle('event_section'); 
	if( !is_object($eventsec) ) {
     	CollectionAttributeKey::add($checkn, 
     	array('akHandle' => 'event_section', 
     	'akName' => t('Calender'),
     	'akIsSearchable' => 1, 
     	'akIsSearchableIndexed' => 1
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
  	
  	$eventall=CollectionAttributeKey::getByHandle('event_allday'); 
	if( !is_object($eventall) ) {
     	CollectionAttributeKey::add($checkn, 
     	array('akHandle' => 'event_allday', 
     	'akName' => t('All Day Event?'),
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
  	$eventgrouped=CollectionAttributeKey::getByHandle('event_grouped'); 
	if( !is_object($eventgrouped) ) {
     	CollectionAttributeKey::add($checkn, 
     	array('akHandle' => 'event_grouped', 
     	'akName' => t('Group Dates?'),
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
    $pulln = AttributeType::getByHandle('select'); 
  	$eventcat=CollectionAttributeKey::getByHandle('event_category'); 
	if( !is_object($eventcat) ) {
     	CollectionAttributeKey::add($pulln, 
     	array('akHandle' => 'event_category', 
     	'akName' => t('Event Category'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => '1', 
		'akSelectAllowOtherValues' => true, 
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
  	$catcolor=CollectionAttributeKey::getByHandle('category_color'); 
	if( !is_object($catcolor) ) {
     	CollectionAttributeKey::add($colorpicker, 
     	array('akHandle' => 'category_color', 
     	'akName' => t('Category Color'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => '1'
     	),$pkg)->setAttributeSet($evset); 
  	}
		  	
  	$eventtag=CollectionAttributeKey::getByHandle('event_tag'); 
	if( !is_object($eventtag) ) {
     	CollectionAttributeKey::add($pulln, 
     	array('akHandle' => 'event_tag', 
     	'akName' => t('Event Tags'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => '1', 
		'akSelectAllowMultipleValues' => true, 
		'akSelectAllowOtherValues' => true, 
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
    $imagen = AttributeType::getByHandle('image_file'); 
  	$eventthum=CollectionAttributeKey::getByHandle('thumbnail'); 
	if( !is_object($eventthum) ) {
     	CollectionAttributeKey::add($imagen, 
     	array('akHandle' => 'thumbnail', 
     	'akName' => t('Thumbnail Image'), 
     	),$pkg); 
  	}
  	
  	//$pageless=CollectionAttributeKey::getByHandle('event_pageless'); 
	//if( !is_object($pageless) ) {
    // 	CollectionAttributeKey::add($checkn, 
    // 	array('akHandle' => 'event_pageless', 
    // 	'akName' => t('Publish without details page?'),
    // 	),$pkg)->setAttributeSet($evset); 
  	//}


  	$textn = AttributeType::getByHandle('text'); 
  	$eventurl=CollectionAttributeKey::getByHandle('event_local'); 
	if( !is_object($eventurl) ) {
     	CollectionAttributeKey::add($textn, 
     	array('akHandle' => 'event_local', 
     	'akName' => t('Event Location'), 
     	),$pkg)->setAttributeSet($evset); 
  	}

  	$address=CollectionAttributeKey::getByHandle('address'); 
	if( !is_object($address) ) {
     	CollectionAttributeKey::add($textn, 
     	array('akHandle' => 'address', 
     	'akName' => t('Address'), 
     	),$pkg)->setAttributeSet($evset); 
  	}
 
  	$contact=CollectionAttributeKey::getByHandle('contact_name'); 
	if( !is_object($contact) ) {
     	CollectionAttributeKey::add($textn, 
     	array('akHandle' => 'contact_name', 
     	'akName' => t('Contact Name'), 
     	),$pkg)->setAttributeSet($uset); 
  	}

  	$conemail=CollectionAttributeKey::getByHandle('contact_email'); 
	if( !is_object($conemail) ) {
     	CollectionAttributeKey::add($textn, 
     	array('akHandle' => 'contact_email', 
     	'akName' => t('Contact Email'), 
     	),$pkg)->setAttributeSet($uset); 
  	}
  	
  	$daten = AttributeType::getByHandle('date'); 
  	$eventthru=CollectionAttributeKey::getByHandle('event_thru'); 
	if( !is_object($eventthru) ) {
     	CollectionAttributeKey::add($daten, 
     	array('akHandle' => 'event_thru', 
     	'akName' => t('End Date'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => '1',
     	'akDateDisplayMode' => 'date', 
     	),$pkg)->setAttributeSet($evset); 
  	}
  	
  	$timeen = AttributeType::getByHandle('time'); 
  	
  	/*
  	$eventstime=CollectionAttributeKey::getByHandle('start_time'); 
	if( !is_object($eventstime) ) {
     	CollectionAttributeKey::add($timeen, 
     	array('akHandle' => 'start_time', 
     	'akName' => t('Start Time'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => 'false',  
     	),$pkg)->setAttributeSet($evset); 
  	}

  	$eventetime=CollectionAttributeKey::getByHandle('end_time'); 
	if( !is_object($eventetime) ) {
     	CollectionAttributeKey::add($timeen, 
     	array('akHandle' => 'end_time', 
     	'akName' => t('End Time'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => 'false',  
     	),$pkg)->setAttributeSet($evset); 
  	}
  	*/
  	
  	$eventrecur= CollectionAttributeKey::getByHandle('event_recur'); 
	if( !is_object($eventrecur) ) {
     	$eventrecur = CollectionAttributeKey::add($pulln, 
     	array('akHandle' => 'event_recur', 
     	'akName' => t('Recurring'), 
     	'akIsSearchable' => '1', 
     	'akIsSearchableIndexed' => '1'
     	),$pkg)->setAttributeSet($evset); 
     	$eventrecur= CollectionAttributeKey::getByHandle('event_recur'); 
     	SelectAttributeTypeOption::add($eventrecur,t('daily'));
     	SelectAttributeTypeOption::add($eventrecur,t('weekly'));
     	SelectAttributeTypeOption::add($eventrecur,t('every other week'));
     	SelectAttributeTypeOption::add($eventrecur,t('monthly'));
     	SelectAttributeTypeOption::add($eventrecur,t('yearly'));
     	//SelectAttributeTypeOption::add($ak,'twice per month');
	}
	
	$event_qty = CollectionAttributeKey::getByHandle('event_qty'); 
	if( !is_object($event_qty) ) {
     	CollectionAttributeKey::add($textn, 
     	array('akHandle' => 'event_qty', 
     	'akName' => t('Event Qty'), 
     	),$pkg)->setAttributeSet($evsbt); 
  	}
  	
  	$event_price = CollectionAttributeKey::getByHandle('event_price'); 
	if( !is_object($event_price) ) {
     	CollectionAttributeKey::add($price, 
     	array('akHandle' => 'event_price', 
     	'akName' => t('Event Price'), 
     	),$pkg)->setAttributeSet($evsbt); 
  	}
  	
   }
  
	function add_se_pages($pkg) {
	
		$db = Loader::db();
	
		$eventPageType = CollectionType::getByHandle('pe_post');
		if(!is_object($eventPageType) || $eventPageType==false){  
	  		$eventPageType = array('ctHandle' => 'pe_post', 'ctName' => t('ProEvents Post'),'ctIcon'=>t('template3.png'));
      		CollectionType::add($eventPageType, $pkg);
      	}
      	$eventPageType = CollectionType::getByHandle('pe_post');
      	
 		$pageType= CollectionType::getByHandle('full');
     	if(!is_object($pageType) || $pageType==false){  
     		$pageType= CollectionType::getByHandle('full_width');
    	}
    	$pageType= CollectionType::getByHandle('left_sidebar');
     	if(!is_object($pageType) || $pageType==false){  
			$pageType= CollectionType::getByHandle('right_sidebar');
		}


    	$pageeventParent = Page::getByID(HOME_CID);
    	$pageeventParent->add($pageType, array('cName' => 'Events', 'cHandle' => 'event'));

    	$seteventAt = Page::getByPath('/event');
    	$seteventAt->setAttribute('event_section',1); 
    	
		$data = array('cName' => t('Google Event'), 'cDescription' => t('Placeholder Google Events'), 'cDatePublic' => date('Y-m-d'));
		$google_page = $pageeventParent->add($eventPageType, $data);	
		$data = array('content' => 'google data');
		$bt = BlockType::getByHandle('content');
		$b = $google_page->addBlock($bt, 'Main', $data);
		$b->setCustomTemplate('google_event');
		$db->Execute('update Pages set cIsSystemPage = 1 where cID = ?', array($google_page->getCollectionID()));
    
    	$cIDn= $seteventAt->getCollectionID();
    
    	$bt = BlockType::getByHandle('pro_event_list');
		
		
		$data = array('num' => '5',
		'isPaged'=>'1',
		'nonelistmsg'=>'There are no events at this time',
		'ordering'=>'ASC',
		'showfeed'=>'1',
		'rssTitle'=>'Latest event',
		'rssDescription'=>'Our latest event feed',
		'truncateSummaries'=>'1',
		'truncateChars'=>'128',
		'ctID'=>'All Categories',
		'sctID'=>'All Sections'
		);			
					
		$b = $seteventAt->addBlock($bt, 'Main', $data);
		$b->setCustomTemplate('templates/jquery_calendar');

		
		$event_manager = UserAttributeKey::getByHandle('event_manager');
		if($event_manager->akID){
			$event_manager->delete();
		}
		
		$group = Group::getByName('ProEvents Manager');
		if(!$group || $group->getGroupID() < 1){
			$group = Group::add('ProEvents Manager','Can create and edit Events');
		}
		
		$pk = PermissionKey::getByHandle('proevent_manager');
		if(!$pk || $pk->getPermissionKeyID() < 1){
			$pk = AdminPermissionKey::add('admin','proevent_manager',t('Create Events'),t('User can use ProEvents frontend features.'),true,false,$pkg);
		}
		
		$pe = GroupPermissionAccessEntity::getOrCreate($group);
		
		$pa = AdminPermissionAccess::create($pk);
		$pa->addListItem($pe, false, 10);
		
		$pka = new PermissionAssignment();
		$pka->setPermissionKeyObject($pk);
		$pka->assignPermissionAccess($pa);
		
		
		$seteventAt->reindex();
  }
  
  function setDefaults(){
  		
		$pe_post= CollectionType::getByHandle('pe_post');
		
	$args = array(
		'themed'=> false,
		'showHolidays'=> true,
		'showTooltips'=> true,
		'tooltipColor'=> 'dark',
		'defaultView'=> 'month',
		'time_formatting'=> 'us',
		'search_path'=>'',
		'tweets'=>true,
		'google'=>true,
		'fb_like'=>true,
		'invites'=>true,
		'ctID'=>$pe_post->ctID
	);
	
	$db= Loader::db();
	
	$db->EXECUTE("DELETE FROM btProEventSettings");	
	
	$q = ("INSERT INTO btProEventSettings (themed,showHolidays,showTooltips,tooltipColor,defaultView,time_formatting,search_path,tweets,google,fb_like,invites,ctID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
	$db->EXECUTE($q,$args);

  }

  public function on_start(){
  
    Events::extend('proforms_item_payment', 'ProeventsFormStatusExtend', 'onPaymentSuccess', DIRNAME_PACKAGES . '/' . $this->pkgHandle . '/models/events/proforms_extend.php');
    
    Events::extend('on_page_delete', 'ProeventsPageExtend', 'onPageDelete', DIRNAME_PACKAGES . '/' . $this->pkgHandle . '/models/events/page_extend.php');
    
  	$html = Loader::helper('html');

	$u = new User();
	
  	if(version_compare(APP_VERSION,'5.4.1.1', '>')){
	  	$ihm = Loader::helper('concrete/interface/menu');
		//Loader::model('section', 'multilingual');		
		$uh = Loader::helper('concrete/urls');
		
		if(!is_array($_REQUEST['cID'])){
			$pID = Page::getByID($_REQUEST['cID'])->getCollectionParentID();
			$parent = Page::getByID($pID);
			if($parent->getAttribute('event_section') > 0){
				$title = t('Edit');
			}else{
				$title = t('Create');
			}
			
			$ihm->addPageHeaderMenuItem('proevents', $title.t(' Event'), 'right', array(
				'dialog-title' => t('Create Event'),
				'href' => $uh->getToolsUrl('add_event', 'proevents').'?eventID='.$_REQUEST['cID'],
				'dialog-on-open' => "$(\'#ccm-page-edit-nav-proevents\').removeClass(\'ccm-nav-loading\')",
				'dialog-on-close' => "location.reload();",
				'dialog-width' => '700',
				'dialog-height' => "500",
				'dialog-modal' => "false",
				'class' => 'dialog-launch'
			), 'proevents');
		}
  	}
  	
  	if(!DATE_APP_DATE_PICKER){
		//$GLOBALS['DATE_APP_DATE_PICKER'] ='mm/dd/yy';
		define('DATE_APP_DATE_PICKER', 'yy/mm/dd');
		define('DATE_APP_GENERIC_MDY', 'Y/m/d');
		define('DATE_APP_GENERIC_T','H:i:s');
		define('DATE_APP_GENERIC_MDYT','Y/m/d H:i:s');
	}

	$objEnv = Environment::get();
	$objEnv->overrideCoreByPackage('blocks/content/controller.php', $this);
  }	
  
  function load_required_models() {
    Loader::model('single_page');
    Loader::model('collection');
    Loader::model('page');
    loader::model('block');
    Loader::model('collection_types');
    Loader::model('/attribute/categories/collection');
    Loader::model('/attribute/categories/user');
    Loader::model('/attribute/types/select/controller');
	    Loader::model('/permission/access/entity/types/group');
	    Loader::model('/permission/access/model');
	    Loader::model('/permission/access/categories/admin');
	    Loader::model('/permission/category');
	    Loader::model('/permission/keys/admin');
	    Loader::model('/permission/assignment');
	    Loader::model('groups');
  }		
		
}