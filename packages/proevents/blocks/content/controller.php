<?php   
	defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class ContentBlockController extends Concrete5_Controller_Block_Content {
	
		public function on_page_view(){
			global $c;
			if(Page::getByID($c->getCollectionParentID())->getAttribute('event_section') > 0 ){
				$this->btCacheBlockOutputOnPost = false;
			}
			$v = View::getInstance();
			$v->addFooterItem(Loader::helper('html')->javascript('bootstrap.js'));
		}
				
	}
	
?>