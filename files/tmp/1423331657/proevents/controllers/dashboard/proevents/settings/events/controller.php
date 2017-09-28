<?php    
defined('C5_EXECUTE') or die(_("Access Denied.")); 
class DashboardProeventsSettingsEventsController extends Controller {

	function view(){
		$db= Loader::db();
		$r = $db->execute("SELECT * FROM btProEventSettings");
		while($row=$r->fetchrow()){
			$this->set('themed',$row['themed']);
			$this->set('showHolidays',$row['showHolidays']);
			$this->set('showTooltips',$row['showTooltips']);
			$this->set('tooltipColor',$row['tooltipColor']);
			$this->set('defaultView',$row['defaultView']);
			$this->set('time_formatting',$row['time_formatting']);
			$this->set('search_path',$row['search_path']);
			$this->set('tweets',$row['tweets']);
			$this->set('google',$row['google']);
			$this->set('xml_feeds',isset($row['xml_feeds']) ? explode(':^:',$row['xml_feeds']) : '');
			$this->set('fb_like',$row['fb_like']);
			$this->set('invites',$row['invites']);
			$this->set('ctID',$row['ctID']);
			$this->set('tz_format',$row['tz_format']);
			$this->set('sharethis_key',$row['sharethis_key']);
			$this->set('user_events',$row['user_events']);
		}
		$this->loadPageTypes();
	}


	function save_settings(){
		$feeds = '';
		if($this->post('xml_feeds')){
			$feeds = implode(':^:',$this->post('xml_feeds'));
		}
		$args = array(
			'themed'=> ($this->post('themed')) ? 'true' : 'false',
			'showHolidays'=> $this->post('showHolidays'),
			'showTooltips'=> $this->post('showTooltips'),
			'tooltipColor'=> $this->post('tooltipColor'),
			'defaultView'=> $this->post('defaultView'),
			'time_formatting'=> $this->post('time_formatting'),
			'search_path'=>$this->post('search_path'),
			'tweets'=>$this->post('tweets'),
			'google'=>$this->post('google'),
			'fb_like'=>$this->post('fb_like'),
			'invites'=>$this->post('invites'),
			'ctID'=>$this->post('ctID'),
			'xml_feeds'=> str_replace('basic','full',$feeds),
			'tz_format'=>$this->post('tz_format'),
			'sharethis_key'=>$this->post('sharethis_key'),
			'user_events'=>$this->post('user_events')
		);
		
		$db= Loader::db();
		
		$db->EXECUTE("DELETE FROM btProEventSettings");	
		
		$q = ("INSERT INTO btProEventSettings (themed,showHolidays,showTooltips,tooltipColor,defaultView,time_formatting,search_path,tweets,google,fb_like,invites,ctID,xml_feeds,tz_format,sharethis_key,user_events) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$db->EXECUTE($q,$args);
		
		$this->view();
	}
	
	protected function loadPageTypes() {
		Loader::model("collection_types");
		$ctArray = CollectionType::getList('');
		$pageTypes = array();
		foreach($ctArray as $ct) {
			$pageTypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();		
		}
		$this->set('pageTypes', $pageTypes);
	}

}