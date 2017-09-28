<?php       
require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');

	class ProEventListBlockController extends BlockController {
		
		var $pobj;
		var $el;
		var $settings;
		
		public $paginationPage;
		public $listType;
		public $category;
		
		protected $btTable = 'btProEventList';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "430";

		protected $btCacheBlockOutput = false;
		protected $btCacheBlockRecord = true;
		
		
		public function getBlockTypeDescription() {
			return t("Event List.");
		}
		
		public function getBlockTypeName() {
			return t("Event List");
		}
		
		public function __construct($b = null){ 
			parent::__construct($b);
			$eventify = Loader::helper('eventify','proevents');
			$this->settings = $eventify->getSettings();
		}
		
		function getbID() {return $this->bID;}
		
		function setListType($listType){$this->listType = $listType;}

		function getEvents($date=null,$date2=null){

			if ($this->bID) {
				$db = Loader::db();
				$q = "select num, ordering, rssTitle, nonelistmsg, showfeed, rssDescription, truncateSummaries, isPaged, truncateChars, ctID, sctID from btProEventList where bID = '$bID'";
				$r = $db->query($q);
				if ($r) {
					$row = $r->fetchRow();
				}
			} else {
				$row['num'] = $this->num;
				$row['ordering'] = $this->ordering;
				$row['rssTitle'] = $this->rssTitle;
				$row['nonelistmsg'] = $this->nonelistmsg;
				$row['showfeed'] = $this->showfeed;
				$row['rssDescription'] = $this->rssDescription;
				$row['truncateSummaries'] = $this->truncateSummaries;
				$row['isPaged'] = $this->isPaged;
				$row['truncateChars'] = $this->truncateChars;
				$row['ctID'] = $this->ctID;
				$row['sctID'] = $this->sctID;
			}
	
			Loader::model('event_list','proevents');
			$el = new EventList();
			$el->setNameSpace('b' . $this->bID);

			$num = $this->num;
			$el->setEventNum($this->num);
			
			if($this->paginationPage){
				$el->currentPages = $this->paginationPage;
			}
			
			$b = Block::getByID($this->bID);
			
			if($this->listType){
				$template = strtolower($this->listType);
				$el->setEventTemplate($template);
			}else{
				$template = strtolower($b->getBlockFilename());
				$el->setEventTemplate($template);
			}

			$el->filterDates($date,$date2);
			
			$el->setEventOrdering($this->ordering);
			
			if ($this->ctID != 'All Categories') {
				$selected_cat = explode(', ',$this->ctID);
				$el->filterByCategories($selected_cat);
			}	
			
			if($this->category){
				$el->filterByCategories(array($this->category));
			}

			if ($this->sctID != 'All Sections' && $this->sctID != '') {
				$el->filterByParentID($this->sctID);
			}	
			
			if($this->filter_by_user){
				$u = new User();
				$el->filterByUser($u->uID);
			}
			
			Loader::model('attribute/categories/collection');
			if ($this->displayFeaturedOnly == 1) {
				$cak = CollectionAttributeKey::getByHandle('is_featured');
				if (is_object($cak)) {
					$el->filter(false,"ak_is_featured = 1");
				}
			}

			//$el->debug();
			
			$calNum = $el->getCalNum();
			if ($this->num > 0 && $calNum < 1) {
				if($this->paginationPage){
					$offset =  ($this->paginationPage - 1) * $this->num;
					$events = $el->get($this->num,$offset);
				}else{
					$events = $el->getPage();
				}
			
			} else {
				$events = $el->get();
			}
			
			$this->el = $el;
			$this->set('el', $el);
			return $events;
		}

		
		function view() {
			global $c;
			Loader::model('event_list','proevents');
			Loader::model('event_item','proevents');
			$eventify = Loader::helper('eventify','proevents');
			$this->set('eventify',$eventify);
			$this->set('settings',$eventify->getSettings());
			$this->set('ical_url',$this->getiCalUrl());
			$this->set('ical_img_url',$this->getiCalImgUrl());
			$this->set('rss_url',$this->getRssUrl());
			$this->set('rss_img_url',$this->getRssImgUrl());
			
			$this->set('message',$message);
			$eArray = array();
			$eArray = $this->getEvents();
			$this->set('eArray', $eArray);
			$this->set('message',$this->post('message'));
			$this->set('nh',loader::helper('navigation'));
			$this->set('dth',Loader::helper('form/date_time_time','proevents'));
			$this->set('link',Loader::helper('navigation')->getLinkToCollection($c));
			$months = array(
				'Jan'=>t('Jan'),
				'Feb'=>t('Feb'),
				'Mar'=>t('Mar'),
				'Apr'=>t('Apr'),
				'May'=>t('May'),
				'Jun'=>t('Jun'),
				'Jul'=>t('Jul'),
				'Aug'=>t('Aug'),
				'Sep'=>t('Sep'),
				'Oct'=>t('Oct'),
				'Nov'=>t('Nov'),
				'Dec'=>t('Dec'),
			);
			$this->set('months', $months);
		}	
		
		
		public function getiCalUrl(){
			$uh = Loader::helper('concrete/urls');
			$bt = BlockType::getByHandle('pro_event_list');
			$rssUrl = $uh->getBlockTypeToolsURL($bt)."/iCal.php";
			return $rssUrl;
		}
		
	
		public function getRssUrl(){
			$uh = Loader::helper('concrete/urls');
			$bt = BlockType::getByHandle('pro_event_list');
			$rssUrl = $uh->getBlockTypeToolsURL($bt)."/rss.php";
			return $rssUrl;
		}
		
		public function getiCalImgUrl(){
			$uh = Loader::helper('concrete/urls');
			$bt = BlockType::getByHandle('pro_event_list');
			$iCalIconUrl = $uh->getBlockTypeAssetsURL($bt,'/tools/calendar_sml.png');
			return $iCalIconUrl;
		}
		
		public function getRssImgUrl(){
			$uh = Loader::helper('concrete/urls');
			$bt = BlockType::getByHandle('pro_event_list');
			$iCalIconUrl = $uh->getBlockTypeAssetsURL($bt,'/rss.png');
			return $iCalIconUrl;
		}
		

		function save($data) { 
		
			if(!$data['ctID'] || !is_array($data['ctID'])){ $data['ctID']=array();}
			
			if(!in_array('All Categories', $data['ctID']) && !empty($data['ctID'])){
				if(count($data['ctID'])>1){
					$eventCat = implode(', ',$data['ctID']);
				}else{
					$eventCat = $data['ctID'][0];
				}
			}else{
				$eventCat = 'All Categories';
			}
			
			
			$args['num'] = isset($data['num']) ? $data['num'] : '';
			$args['ordering'] = isset($data['ordering']) ? $data['ordering'] : '';
			$args['rssTitle'] = isset($data['rssTitle']) ? $data['rssTitle'] : '';
			$args['nonelistmsg'] = isset($data['nonelistmsg']) ? $data['nonelistmsg'] : '';
			$args['showfeed'] = isset($data['showfeed']) ? $data['showfeed'] : '';
			$args['listType'] = isset($data['listType']) ? $data['listType'] : '';
			$args['rssDescription'] = isset($data['rssDescription']) ? $data['rssDescription'] : '';
			$args['truncateSummaries'] = ($data['truncateSummaries']==1) ? 1 : 0;
			$args['displayFeaturedOnly'] = ($data['displayFeaturedOnly']==1) ? 1 : 0;
			$args['isPaged'] = ($data['isPaged']==1) ? 1 : 0;
			$args['truncateChars'] = isset($data['truncateChars']) ? $data['truncateChars'] : '';
			$args['showfilters'] = ($data['showfilters']==1) ? 1 : 0;
			$args['ctID'] = $eventCat;
			$args['sctID'] = isset($data['sctID']) ? $data['sctID'] : '';
			$args['filter_by_user'] = ($data['filter_by_user']==1) ? 1 : 0;
			parent::save($args);
		}	
		
		public function on_page_view() {
	   		$html = Loader::helper('html');
	   		$eventify = Loader::helper('eventify','proevents');
	   		//this style is what controlls the jquery cal UI colors
	   		//$this->addHeaderItem($html->css('jquery-ui-lefrog.css','proevents'));
	   		$this->addHeaderItem($html->css('jquery.ui.css'));
	   		$this->addHeaderItem("<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>");
	   		$this->addHeaderItem($html->css('ccm.forms.css'));
	   		$this->addHeaderItem($html->css('ccm.base.css'));
	   		$this->addHeaderItem($html->css('ccm.dialog.css'));
	   		$this->addFooterItem($html->javascript('jquery.js'));
	   		$this->addFooterItem($html->javascript('jquery.ui.js'));
	   		$this->addFooterItem($html->javascript('bootstrap.js'));
	   		$this->addFooterItem($html->javascript('ccm.app.js'));
	   		$this->addFooterItem($html->javascript('tiny_mce/tiny_mce.js'));
		}

}
?>