<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));

class TimeAttributeTypeController extends AttributeTypeController  {

	public $helpers = array('form');
	
	protected $searchIndexFieldDefinition = 'TIME NULL';


	public function type_form() {
		$this->load();
	}

	public function getDisplayValue() {
		$v = $this->getValue();
		if ($v == '' || $v == false) {
			return '';
		}
		$v2 = date('H:i:s', strtotime($v));
		$r = '';
		if ($v2 != '00:00:00') {
			$r .= date(DATE_APP_DATE_ATTRIBUTE_TYPE_T, strtotime($v));
		}
		return $r;
	}
	
	public function searchForm($list) {
		$timeFrom = $this->request('from');
		$timeTo = $this->request('to');
		if ($timeFrom) {
			$timeFrom = date('H:i:s', strtotime($timeFrom));
			$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $timeFrom, '>=');
		}
		if ($timeTo) {
			$timeTo = date('H:i:s', strtotime($timeTo));
			$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $timeTo, '<=');
		}
		return $list;
	}
	
	
	public function form() {
		$this->load();
		$dt = Loader::helper('form/time','proevents');
		$caValue = $this->getValue();
		switch($this->akTimeDisplayMode) {
			default:
				print $dt->timex($this->field('value'), $caValue);
				break;
		}
	}


	public function valitimeForm($data) {
		return $data['value'] != '';
	}

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atTime where avID = ?", array($this->getAttributeValueID()));
		return $value;
	}

	public function search() {
		$dt = Loader::helper('form/time','proevents');
		$html = $dt->timex($this->field('from'), $this->request('from'), false);
		$html .= ' ' . t('to') . ' ';
		$html .= $dt->timex($this->field('to'), $this->request('to'), false);
		print $html;
	}
	
	public function saveValue($value) {
		if ($value != '') {
			$value = date('H:i:s', strtotime($value));
		} else {
			$value = null;
		}
		
		$db = Loader::db();
		$db->Replace('atTime', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}

	
	
	public function saveForm($data) {
	$dt = Loader::helper('form/time','proevents');
	$dtim= $dt->translate('value',$data);
    $this->saveValue($dtim);
	}

	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atTime where avID = ?', array($id));
		}
	}
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atTime where avID = ?', array($this->getAttributeValueID()));
	}

}