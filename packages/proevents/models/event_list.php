<?php     
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('page_list');
/**
*
* An object that allows a filtered list of events to be returned.
* @package ProEvents
*
**/
class EventList extends PageList {
	/**
	* extending the filter to exclude excluded dates within the query
	*/
	//var $exclude_filter = "left join btProEventDatesExclude excluddates on event.eID = excluddates.eventID";
	
	
	var $num = 0;
	var $template = '';
	var $calNum = null;
	

	/** 
	 * Filters by category
	 * @param categories array
	 * it's not possible to easily use the optimized data structure for select attributes specifically because of the
	 * way they are single-string'd in the index table by value and not ID.  Thus, we are forced to use %LIKE%. no choice.
	*/
	public function filterByCategories($categories) {
		if($categories != null && !in_array('All Categories',$categories)  && !in_array('',$categories)){
				$ccount = count($categories);
				$category = 'ak_event_category LIKE ';
				foreach($categories as $category_item){
					$category_item = str_replace("'","\'",trim($category_item));
					//$category_item = str_replace('&','&amp;',$category_item);
					$category .= "'%\n$category_item\n%'";
					$cct++;
					if($cct < $ccount){
						$category .= ' OR ak_event_category LIKE  ';
					}
				}
				$this->filter(false, "(".$category.")");
			}
	}
	
	/** 
	 * Filters by section
	 * @param section array
	 */
	public function filterByDateRange($date1=null,$date2=null) {
		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "event.date >= '$date1' AND event.date <= '$date2'");
			}

	
	/** 
	 * Filters by section
	 * @param section array
	 */
	public function filterByAllDates($date=null,$time=null,$grouped=1) {
		if($date == null){
			$date = date("Y-m-d");
		}
		if($time == null){
			$time = date("H:i:s");
		}
		$this->setBaseQuery(",event.eID as eID, event.allday, event.date AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "((event.allday = 1 AND DATE_FORMAT(event.date,'%Y-%m-%d') >= '$date') OR (DATE_FORMAT(CONCAT_WS(' ', event.date, event.entime),'%Y-%m-%d %H:%i:%s') >= '$date $time'))");
		//$this->filter(false, "event.date > '$date' AND event.sttime > '$time'");
		if($grouped){
			$this->groupByString = " event.eventID,event.grouped";
		}
	}
	
	
	/** 
	 * Filters by section
	 * @param section array
	 */
	public function filterArchiveDates($date=null) {
		if($date == null){
			$date = date("Y-m-d");
		}
		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "event.date < CURDATE()");
		$this->groupByString = " event.eventID,event.grouped";
	}
	
	
	/** 
	 * Filters by date span
	 * @param filters date by provided date span
	 */
	public function filterBySpan($date,$date2) {
	
		$this->setBaseQuery(",event.eID as eID, event.date AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "(DATE_FORMAT(event.date,'%Y-%m-%d') >= DATE_FORMAT('$date','%Y-%m-%d') AND DATE_FORMAT(event.date,'%Y-%m-%d') <= DATE_FORMAT('$date2','%Y-%m-%d'))");
		
	}
	
	
	/** 
	 * Filters by month
	 * @param filters date by provided date month
	 */
	public function filterByMonth($date=null) {
		if($date == null){
			$date = date("Y-m-d");
		}
		$this->setBaseQuery(",event.eID as eID, event.date AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "DATE_FORMAT(event.date,'%Y-%m') = DATE_FORMAT('$date','%Y-%m')");
		
	}
	
	
	/** 
    * Filters by FOLLOWING month
    * @param filters date by provided date month
    */
   public function filterByFollowingMonth($date=null) {
      if($date == null){
         $j = date('Y');
         $m = date('m'); 
         $date = date("Y-m-d",mktime(0,0,0,$m+1,1,$j));
      }
 
 
      $this->setBaseQuery(",event.eID as eID, event.date AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
      $this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
      //$this->filter(false, "excluddates.eeID IS NULL");
      $this->filter(false, "DATE_FORMAT(event.date,'%Y-%m') = DATE_FORMAT('$date','%Y-%m')");
 
   }
	
	
	/** 
	 * Filters by week
	 * @param filters date by provided date week
	 */
	public function filterByWeek() {
		$date = date('Y-m-d');
		$sunday = date('Y-m-d', strtotime('last Sunday',strtotime($date))); 
		$to_saturday = date('Y-m-d', strtotime('+6 Days',strtotime($sunday)));
		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "DATE_FORMAT(event.date,'%Y-%m-%d') >= DATE_FORMAT('$sunday','%Y-%m-%d') AND DATE_FORMAT(event.date,'%Y-%m-%d') <= DATE_FORMAT('$to_saturday','%Y-%m-%d')");
		$this->groupByString = " event.eventID,event.grouped";
	}
	
	
	/** 
    * Filters by FOLLOWING week
    * @param filters date by provided date week
    */
   public function filterByFollowingWeek() {
	    $date = date('Y-m-d');
		$sunday = date('Y-m-d', strtotime('next Sunday',strtotime($date))); 
		$to_saturday = date('Y-m-d', strtotime('+6 Days',strtotime($sunday)));
		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "DATE_FORMAT(event.date,'%Y-%m-%d') >= DATE_FORMAT('$sunday','%Y-%m-%d') AND DATE_FORMAT(event.date,'%Y-%m-%d') <= DATE_FORMAT('$to_saturday','%Y-%m-%d')");
		$this->groupByString = " event.eventID,event.grouped";
   }
	
	/** 
	 * Filters by day
	 * @param filters date by provided date day
	 */
	public function filterByDay($date=null) {
		if($date == null){
			$date = date("Y-m-d");
		}
		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "event.date = '$date'");
		$this->groupByString = " event.eventID,event.date";
	}
	
	
	 /** 
	 * Filters by eID
	 * @param filters date by provided date eID
	 */
	public function filterBySpecific($eID) {

		$this->setBaseQuery(",event.eID as eID, min(event.date) AS eventdate, event.sttime AS eventstart, event.entime AS eventend");
		$this->setupAttributeFilters("left join btProEventDates event on p1.cID = event.eventID ".$this->exclude_filter);
		//$this->filter(false, "excluddates.eeID IS NULL");
		$this->filter(false, "event.eID = '$eID'");
	}
	
	
	/** 
	 * Filters by Viewing User
	 * @param filters dates by event_price
	 */
	public function filterByUser($uID){
		$this->setupAttributeFilters("left join btProEventUserSaved saved on p1.cID = saved.eventID ".$this->exclude_filter);
		$this->filter(false, "saved.uID = '$uID'");
	}
	
	/** 
	* method contribution @mattdavey
	* Filters by User ID
	* @param filters uID by $userID
	*/
	public function filterByUserId($userID){
		$this->setupAttributeFilters("left join Pages pages on p1.cID = pages.cID ".$this->exclude_filter);
		$this->filter(false, "pages.uID = '$userID'");
	}
	
	
	/** 
	 * Filters by bookable
	 * @param filters dates by bookable
	 */
	public function filterByBookable(){
		$this->filter(false, "event.status IS NOT NULL");
	}
	
	
	/** 
	 * Filters by status
	 * @param filters dates by status
	 */
	public function filterByStatus($status='available'){
		$this->filter(false, "event.status = '$status'");
	}
	
	/** 
	 * Filters by event_qty
	 * @param filters dates by event_qty
	 */
	public function filterByQty($qty=0){
		$this->filter(false, "event.event_qty = '$qty'");
	}
	
	/** 
	 * Filters by event_price
	 * @param filters dates by event_price
	 */
	public function filterByPrice($price=0){
		$this->filter(false, "event.event_price = '$price'");
	}
	
	/**
	* set the current template
	*/
	public function setEventTemplate($template){
		$this->template = $template;
	}
	
	/**
	* set the num of returns
	*/
	public function setEventNum($num){
		$this->num = $num;
	}
	
	/**
	* set the ordering
	*/
	public function setEventOrdering($ordering){
			switch($ordering) {
				case 'DESC':
					$this->sortByString = ($this->sortByString == "") ? "eventdate desc, eventstart desc" : "eventdate desc, eventstart desc,".$this->sortByString;
					break;
				default:
					$this->sortByString = ($this->sortByString == "") ? "eventdate asc, eventstart asc" : "eventdate asc, eventstart asc,".$this->sortByString;
					break;
			}
	}
	
	/** 
	 * Filters dates by block custom view
	 * detects block view name and chooses most efficient event list
	 * @param if no theme is present, filters all days like a list limited by num
	 */
	public function filterDates($date=null,$date2=null){
			
			$template = $this->template;
			$num = $this->num;
	
			if(substr_count($template,'specific') > 0){
				$this->filterBySpecific($date);
				$this->setItemsPerPage(1);
			}elseif(substr_count($template,'archive') > 0){
				$this->filterArchiveDates();
				if ($num > 0) {
					$this->setItemsPerPage($num);
				}
			}elseif(substr_count($template,'following') > 0 && substr_count($template,'month') > 0){
            	$this->filterByFollowingMonth($date);
            	$this->calNum = 1;
            }elseif(substr_count($template,'following') > 0 && substr_count($template,'week') > 0){
            	$this->filterByFollowingWeek($date);
            	$this->calNum = 1;
            }elseif(substr_count($template,'day') > 0 || substr_count($template,'today') > 0){
				$this->filterByDay($date);
				if ($num > 0) {
					$this->setItemsPerPage($num);
				}else{
					$this->calNum = 1;
				}
			}elseif(substr_count($template,'jquery') > 0 || substr_count($template,'dynamic') > 0){
				$this->filterBySpan($date,$date2);
				$this->calNum = 1;
			}elseif(
				substr_count($template,'month') > 0 || 
				substr_count($template,'ajax_') > 0 || 
				substr_count($template,'full') > 0 || 
				substr_count($template,'responsive') > 0 ||
				substr_count($template,'calendar') > 0){
				$this->filterByMonth($date);
				$this->calNum = 1;
			}elseif(substr_count($template,'week') > 0){
				$this->filterByWeek($date);
				if ($num > 0) {
					$this->setItemsPerPage($num);
				}else{
					$this->calNum = 1;
				}
			}else{
				$this->filterByAllDates();
				if ($num > 0) {
					$this->setItemsPerPage($num);
				}
			}
	
	}
	
	/** 
	 * Filters dates by block custom view
	 * detects block view name and chooses most efficient event list
	 * @param if no theme is present, filters all days like a list limited by num
	 */
	public function filterDatesByType($date=null,$date2=null,$type=null,$num=null){
		$list = 'filterBy'.$type;
		$this->$list($date,$date2);
	}
	
	
	/** 
	 * Checks a particular day and returns true or false if an event exists.
	 */
	public function eventIs($date,$category,$section=null,$allday=null){
		
		$i = 0;
        $params = array();
        $sections = explode(', ', $section);
        $categories = explode(', ', $category);
		$category_q = '';
        $section_q = '';
        $allday_q = '';

        // add date to as the first param
        $params[] = $date;

        if (!in_array('All Categories', $categories)) {
            foreach ($categories as $cat) {
                $cat = str_replace('&amp;', '&', $cat);
                if ($i) {
                    $category_q .= "OR ";
                } else {
                    $category_q .= "AND (";
                }
                $category_q .= "category LIKE ? ";
                // add categories as the next params
                $params[] = '%' . $cat . '%';
                $i++;
            }
            $category_q .= ")";
        } else {
            $category_q = '';
        }

        if ($section != null) {
            $vars;
            foreach ($sections as $k => $v) {
                if ($k == 0) {
                    $vars .= "?";
                } else {
                    $vars .= ",?";
                }
                // add the section to the params
                $params[] = $v;
            }
            $sections_q = "AND section IN ($vars) ";
        } else {
            $section = '';
        }

        if ($allday != null) {
            $params[] = '%' . $allday . '%';
            $allday_q = "AND allday LIKE ?";
        } else {
            $allday_q = '';
        }

        $db = Loader::db();

        $events = array();

        $q = "SELECT * FROM btProEventDates WHERE DATE_FORMAT(date,'%Y-%m-%d') = DATE_FORMAT(?,'%Y-%m-%d') $category_q $sections_q $allday_q";
        $r = $db->query($q, $params);
		
		$this->status = null;
		$stat_avail = null;
		
		while($row = $r->fetchrow()){
			if($row['status']=='available'){
				$stat_avail = 'available';
			}
			if(!$stat_avail && $row['status']=='booked'){
				$stat_avail = 'booked';
			}
		}
		
		$this->status = $stat_avail;
		
;
		if($r->RecordCount()>0){
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = null, $offset = null) {

		$pages = array();
		if ($this->getQuery() == '') {
			$this->setBaseQuery();
		}		

		$this->setItemsPerPage($itemsToGet);

		$r = DatabaseItemList::get((int)$itemsToGet,(int)$offset);

		foreach($r as $row) {
			$nc = $this->loadPageID($row['cID'], 'ACTIVE');
			if (!$this->displayOnlyApprovedPages) {
				$cp = new Permissions($nc);
				if ($cp->canViewPageVersions()) {
					$nc->loadVersionObject('RECENT');
				}
			}
			$nc->setPageIndexScore($row['cIndexScore']);
			$pages[$row['eID'].' '.$row['eventdate'].' '.date('h:i A',strtotime($row['eventstart'])).':-:'.date('h:i A',strtotime($row['eventend']))] = $nc;
	
		}

		return $pages;
	}
	
	
	/** 
	 * Gets standard HTML to display paging */
	public function displayAjaxPaging($script = false, $return = false, $additionalVars = array()) {
		if(!$this->itemsPerPage){
			$this->itemsPerPage = $this->num;
		}
		$summary = $this->getSummary();
		$paginator = $this->getAjaxPagination($script, $additionalVars);
		if ($summary->pages > 1) {
			$html = '<div class="ccm-spacer"></div>';
			$html .= '<div class="ccm-pagination">';
			$html .= '<span class="ccm-page-left">' . $paginator->getPrevious() . '</span>';
			$html .= $paginator->getPages();
			$html .= '<span class="ccm-page-right">' . $paginator->getNext() . '</span>';
			$html .= '</div>';
		}
		if (isset($html)) {
			if ($return) {
				return $html;
			} else {
				print $html;
			}
		}
	}
	
	public function getAjaxPagination($url = false, $additionalVars = array()) {
		$pagination = Loader::helper('pagination');
		if ($this->currentPage == false) {
			$this->setCurrentPage();
		}
		if (count($additionalVars) > 0) {
			$pagination->setAdditionalQueryStringVariables($additionalVars);
		}
		$pagination->queryStringPagingVariable = $this->queryStringPagingVariable;
		$pagination->init($this->currentPages, $this->getTotal(), false, $this->itemsPerPage, 'getEventResults');

		return $pagination;
	}
	
	
	public function getCalNum(){
		return $this->calNum;
	}
	
	
}