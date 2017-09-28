<?php    
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

			<select name="ctID" id="ctID">
	    	<?php    
	    	function getCat($ctID) {
	    		$categories = array();
				$db = Loader::db();
				$akID = $db->query("SELECT akID FROM AttributeKeys WHERE akHandle = 'event_category'");
				while($row=$akID->fetchrow()){
					$akIDc = $row['akID'];
				}
				$akv = $db->execute("SELECT value FROM atSelectOptions WHERE akID = $akIDc");
				while($row=$akv->fetchrow()){
					$categories[]=$row;
				}
				foreach($categories as $cat){
					if ($cat['value'] == $ctID && $cat['value'] != 'All Categories'){
				  		echo '<option  value="'.$cat['value'].'"  selected>'.$cat['value'].'</option>';
					} elseif($cat['value'] != 'All Categories') {
						echo  '<option value="'.$cat['value'].'">'.$cat['value'].'</option>';
					}
				}
				if ($ctID == 'All Categories' || $ctID == '' || !isset($ctID)){
					echo '<option value="All Categories" selected>'.t('All Categories').'</option>';
				} elseif($ctID != 'All Categories') {
					echo '<option value="All Categories">'.t('All Categories').'</option>';
				}
			}
			echo getCat($ctID);
			?>	
			</select>