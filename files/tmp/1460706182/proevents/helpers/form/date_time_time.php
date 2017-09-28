<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
class FormDateTimeTimeHelper {


public function translate($field, $akID=null, $arr = null) {
      if ($arr == null) {
         $arr = $_POST;
      }
 
 
      if (isset($arr[$field . '_st_dt'])) {
 
         if (DATE_FORM_HELPER_FORMAT_HOUR == '24') {
 
            if ($arr[$field . '_st_h'] == 12) {
                  $arr[$field . '_st_a'] = 'PM';
            } elseif ($arr[$field . '_st_h'] < 12) {
               if ($arr[$field . '_st_h'] == 0) {
                  $arr[$field . '_st_h'] = 12;
               }
               $arr[$field . '_st_a'] = 'AM';
            } else {
               $arr[$field . '_st_h'] = $arr[$field . '_st_h'] - 12;
               $arr[$field . '_st_a'] = 'PM';
            }
 
            if ($arr[$field . '_end_h'] == 12) {
               $arr[$field . '_end_a'] = 'PM';
            } elseif ($arr[$field . '_end_h'] < 12) {
               if ($arr[$field . '_end_h'] == 0) {
                  $arr[$field . '_end_h'] = 12;
               }
               $arr[$field . '_end_a'] = 'AM';
            } else {
               $arr[$field . '_end_h'] = $arr[$field . '_end_h'] - 12;
               $arr[$field . '_end_a'] = 'PM';
            }
 
         } else {
 
            if($arr[$field . '_st_h'] > 12){
               $arr[$field . '_st_h'] = $arr[$field . '_st_h'] - 12;
               $arr[$field . '_st_a'] = 'PM';
            }
 
            if($arr[$field . '_end_h'] > 12){
               $arr[$field . '_end_h'] = $arr[$field . '_end_h'] - 12;
               $arr[$field . '_end_a'] = 'PM';
            }
 
            if($arr[$field . '_st_a'] == ''){
               $arr[$field . '_st_a'] = 'AM';
            }
 
            if($arr[$field . '_end_a'] == ''){
               $arr[$field . '_end_a'] = 'AM';
            }
 
         }
         
         $dt = $this->getReformattedDate($arr[$field . '_st_dt']);

         $str = $dt . ' ' . $arr[$field . '_st_h'] . ':' . $arr[$field . '_st_m'] . ' ' . $arr[$field . '_st_a']. ':-:'.$arr[$field . '_end_h'] . ':' . $arr[$field . '_end_m'] . ' ' . $arr[$field . '_end_a'];
 
 
         return $str;
      }
 
      if ($akID) {
 
         if (DATE_FORM_HELPER_FORMAT_HOUR == '24') {
 
            if ($arr[$field . '_st_h'] == 12) {
               $arr[$field . '_st_a'] = 'PM';
            } elseif ($arr[$field . '_st_h'] < 12) {
               if ($arr[$field . '_st_h'] == 0) {
                  $arr[$field . '_st_h'] = 12;
               }
               $arr[$field . '_st_a'] = 'AM';
            } else {
               $arr[$field . '_st_h'] = $arr[$field . '_st_h'] - 12;
               $arr[$field . '_st_a'] = 'PM';
            }
 
            if ($arr[$field . '_end_h'] == 12) {
               $arr[$field . '_end_a'] = 'PM';
            } elseif ($arr[$field . '_end_h'] < 12) {
               if ($arr[$field . '_end_h'] == 0) {
                  $arr[$field . '_end_h'] = 12;
               }
               $arr[$field . '_end_a'] = 'AM';
            } else {
               $arr[$field . '_end_h'] = $arr[$field . '_end_h'] - 12;
               $arr[$field . '_end_a'] = 'PM';
            }
 
         } else {
 
            if($arr[$field . '_st_h'] > 12){
               $arr[$field . '_st_h'] = $arr[$field . '_st_h'] - 12;
               $arr[$field . '_st_a'] = 'PM';
            }
 
            if($arr[$field . '_end_h'] > 12){
               $arr[$field . '_end_h'] = $arr[$field . '_end_h'] - 12;
               $arr[$field . '_end_a'] = 'PM';
            }
 
            if($arr[$field . '_st_a'] == ''){
               $arr[$field . '_st_a'] = 'AM';
            }
 
            if($arr[$field . '_end_a'] == ''){
               $arr[$field . '_end_a'] = 'AM';
            }
 
         }
 
         $arr = $arr['akID'][$akID];
         $dt = $this->translateDate($arr[$field . '_st_dt']);
         $str = $dt . ' ' . $arr[$field . '_st_h'] . ':' . $arr[$field . '_st_m'] . ' ' . $arr[$field . '_st_a']. ':-:'.$arr[$field . '_end_h'] . ':' . $arr[$field . '_end_m'] . ' ' . $arr[$field . '_end_a'];
 
 
         return $str;
      }
 
   }
	
	/**
	* translates a given page objects event_multidate attribute
	* into a nice array of dates and times
	**/
	public function translate_from($c){
		Loader::model('attribute/categories/collection');
		$emdd = CollectionAttributeKey::getByHandle('event_multidate');
		$date_multi = $c->getCollectionAttributeValue($emdd);
		
		$date_multi_array = explode(':^:',$date_multi);	
		foreach($date_multi_array as $dated){
			$i++;
			$date_sub = explode(' ',$dated);
			$dates_array[$i]['dsID'] = $i;
			$dates_array[$i]['date'] = $date_sub[0];
			$etdiv = explode(':-:',$date_sub[2]);
			$stime = $date_sub[1].' '.$etdiv[0];
			$etime = $etdiv[1].' '.$date_sub[3];
			$dates_array[$i]['start'] = $stime;
			$dates_array[$i]['end'] = $etime;
			
		}
		return $dates_array;
	}
	
	
	/**
	* translates a given string to a nice array of dates and times
	* input: cID date starttime:-:endtime
	* output: array(date=>value,start=>value,end=>value)
	**/
	public function translate_from_string($date_info){
		$date_multi_array = explode(':^:',$date_info);	
		foreach($date_multi_array as $dated){
			$i++;
			$date_sub = explode(' ',$dated);
			
			$dates_array['eID'] = $date_sub[0];
			$dates_array['date'] = $date_sub[1];
			$etdiv = explode(':-:',$date_sub[3]);
			$stime = $date_sub[2].' '.$etdiv[0];
			$etime = $etdiv[1].' '.$date_sub[4];
			$dates_array['start'] = $this->convert_to_time($stime);
			$dates_array['end'] = $this->convert_to_time($etime);
		}	
		return $dates_array;
	}
	
	/** 
	 * Takes a "string", converts to output string based on time config
	 * @param string $string
	 * @return string $Time
	 */
	public function convert_to_time($time) {
		if (DATE_FORM_HELPER_FORMAT_HOUR == '12') {
			$h = date('h', strtotime($time));
			$m = date('i', strtotime($time));
			$a = date('a', strtotime($time));
			$time_str = ltrim($h,0).':'.$m.' '.$a;
		} else {
			$h = date('G', strtotime($time));
			$m = date('i', strtotime($time));
			$time_str = $h.':'.$m;
		}
		return $time_str;
		
	}
	
	
	public function getReformattedDate($date,$debug=false){
		
		if($debug){
            var_dump(DATE_APP_DATE_PICKER);exit;
        }
        
		switch(DATE_APP_DATE_PICKER){
		
			case 'dd/mm/yy':
			case 'd/m/yy':

				list($day, $month, $year) = explode('/', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
			
				break;
				
			case 'dd-mm-yy':
			case 'd-m-yy':	
				list($day, $month, $year) = explode('-', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
				
			case 'dd.mm.yy':
			case 'd.m.yy':	
				list($day, $month, $year) = explode('.', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
				
			case 'mm/dd/yy':
			case 'm/d/yy':	
				list($month, $day, $year) = explode('/', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);

				break;
				
			case 'yy/mm/dd':
			case 'yy/m/d':	
				list($year, $month, $day) = explode('/', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);

				break;
			
			case 'yy.mm.dd':
			case 'yy.m.d':	
				list($year, $month, $day) = explode('.', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);

				break;
				
			case 'yy/dd/mm':
			case 'yy/d/m':	
				list($year, $day, $month) = explode('/', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);

				break;
				
			case 'mm-dd-yy':
			case 'm-d-yy':	
				list($month, $day, $year) = explode('-', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
				
			case 'mm.dd.yy':
			case 'm.d.yy':	
				list($month, $day, $year) = explode('.', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
			
			case 'yy-mm-dd':
			case 'yy-m-d':	
				list($year, $month, $day) = explode('-', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
				
			case 'yy-dd-mm':
			case 'y-d-mm':	
				list($year, $day, $month) = explode('-', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
				
			case 'yy.dd.mm':
			case 'y.d.mm':	
				list($year, $day, $month) = explode('.', $date);
				return $year.'-'.sprintf("%02s", $month).'-'.sprintf("%02s", $day);
				
				break;
		}
		
	}
	
}
?>