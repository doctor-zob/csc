<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/types/default/controller');

class ColorAttributeTypeController extends DefaultAttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function getDisplaySanitizedValue() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.colorpicker.css'));
   		$this->addHeaderItem($html->javascript('jquery.colorpicker.js'));
   		
		if (is_object($this->attributeValue)) {
			$value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
		}
		$html = '<div class="ccm-color-swatch-wrapper" style="background: none;"><div class="ccm-color-swatch" style="background: none;"><div hex-color="' . $value . '" style="background: none; background-color:' . $value . '"></div></div></div>';

		return $html;
	}
	
	public function getColorsUsed(){
		$db = Loader::db();
		$q = $db->query("SELECT DISTINCT value FROM atDefault WHERE value LIKE '#%' AND LENGTH(value) = 7");
		while($row = $q->fetchRow()){
			$colors[] = $row['value'];
		}
		return $colors;
	}
	
	public function form() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->addHeaderItem($html->css('ccm.colorpicker.css'));
   		$this->addHeaderItem($html->javascript('jquery.ui.js'));
   		
   		$presets = $this->getColorsUsed();

		if (is_object($this->attributeValue)) {
			$value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
		}
		$fieldFormName = 'akID['.$this->attributeKey->getAttributeKeyID().'][value]';
		if($value == '#000000' || $value == '#ffffff'){
			$value = '';
			$value_text = 'none';
		}
		$html = '';
		$html .= '<select name="presets" id="presets"><option value="">'.t('choose one').'</option>';
		if(is_array($presets)){
			foreach($presets as $color){
				$html .= '<option value="'.$color.'"';
				if($value == $color){ $html .= ' selected';}
				$html .= '>'.$color.'</option>';
			}
		}
		$html .= '</select>';
		$html .= '<div style="float: right; margin-left: 23px; margin-right: 12px; width: 32px; height: 32px; background-color: '.$value.';" class="color_preview"></div>';
		$html .= '<br/><a href="javascript:;" class="pick_menu">Show ColorPicker</a>';
		$html .= '<div class="color_pick" style="display: none; clear: both;">';
		$html .= '<div id="f' . $this->attributeKey->getAttributeKeyHandle() . '" hex-color="' . $value . '" >'.$value_text.'</div>';
		$html .= $form->hidden($fieldFormName, $value);
		$html .= '</div>';
		$html .= "<script type=\"text/javascript\">
	$('#presets').change(function(){
		var Color = $('#presets option:selected').val();
		$('input[name=" . '"akID['.$this->attributeKey->getAttributeKeyID().'][value]"' . "]').val(Color);
		$('.color_preview').css('background-color', Color);
	});
	$('.pick_menu').click(function(){
		if($('.color_pick').css('display') == 'none'){
			$('.color_pick').show();
			$('.pick_menu').html('Hide ColorPicker');
		}else{
			$('.color_pick').hide();
			$('.pick_menu').html('Show ColorPicker');
		}
	});
	$(function() {
		var f" .$this->attributeKey->getAttributeKeyHandle(). "Div =$('div#f" .$this->attributeKey->getAttributeKeyHandle(). "');
		var c" .$this->attributeKey->getAttributeKeyHandle(). " = f" .$this->attributeKey->getAttributeKeyHandle(). "Div.attr('hex-color'); 
		f" .$this->attributeKey->getAttributeKeyHandle(). "Div.ColorPicker({
			flat: true,
			color: c" .$this->attributeKey->getAttributeKeyHandle(). ",  
			onSubmit: function(hsb, hex, rgb, cal) { 
				
				if(hex == '000000' || hex == 'ffffff' || hex == ''){
					$('.color_preview').css('background-color', '');
					$('.color_preview').html('none');
					$('input[name=" . '"akID['.$this->attributeKey->getAttributeKeyID().'][value]"' . "]').val('');
					$('.color_pick').hide();
					$('.pick_menu').html('Show ColorPicker');
				}else{
					$('.color_preview').html('');
					$('.color_preview').css('background-color', '#'+hex);
					$('input[name=" . '"akID['.$this->attributeKey->getAttributeKeyID().'][value]"' . "]').val('#' + hex);
					$('.color_pick').hide();
					$('.pick_menu').html('Show ColorPicker');
				}
			},  
			onNone: function(cal) {  
				$('input[name=" . $fieldFormName . "]').val('');		
				$('div#f" . $this->attributeKey->getAttributeKeyHandle(). "').css('backgroundColor',''); 
				cal.show();
			}
		});
		$('.colorpicker_none').remove();
		$('.colorpicker_submit').addClass('btn');
		$('.colorpicker_submit').css('margin-left','-18px');
		$('.colorpicker_submit').css('margin-top','-4px');
		$('.color_pick').css('display','none');
	});
</script>";
		print $html;
	}

}
?>