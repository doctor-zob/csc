<?php     
defined('C5_EXECUTE') or die(_("Access Denied."));
$site = 'http://'.$_SERVER["SERVER_NAME"];

	
		function rssInfo($bID) {
			$db = Loader::db();
			$r = $db->Execute("select * from btProEventList where bID = '$bID'");
			
			while ($row = $r->FetchRow()) {
			    	$ctID = $row['ctID'];
			    	$ordering = $row['ordering'];
			    	$num = $row['num'];
					$rssInfo =  '<title>'.$row['rssTitle'].'</title>';
					$rssInfo .=  '<link>'.BASE_URL.DIR_REL.'</link>';
					$rssInfo .= '<description>'.$row['rssDescription'].'</description>';
				}
				
				echo $rssInfo;
				Loader::model('block');
				$b = Block::getByID($bID);
				getFeed($b);
				
			}
			
			
		function getFeed($b) {
		
			$strip = array("&nbsp;","&");
            $rplace = array(" ","and");

            $controller = $b->getController();
            $events = $controller->getEvents();
            foreach($events as $date_string => $event){
            
            		$dh = Loader::helper('form/date_time_time','proevents');
			  		$date_array = $dh->translate_from_string($date_string);
            		$date = $date_array['date'];
			  		$start = $date_array['start'];
			  		$end = $date_array['end'];
			  		$allday = $event->getAttribute('event_allday');
            		$title =  $event->getCollectionName();
            		$block = $event->getBlocks('Main');
					$content = $event->getCollectionDescription();

            		$feed .= '<item>';
					$feed .= '<title>'.$title.'</title>';
					$feed .= '<pubDate>'.date( DATE_RFC822,strtotime($date)).'</pubDate>';
					$feed .= '<link>'.BASE_URL.DIR_REL.Loader::helper('navigation')->getLinkToCollection($event).'</link>';
					$feed .= '<description><![CDATA['.htmlspecialchars(strip_tags($content)).']]></description>';
					$feed .='</item>';
					$feed = str_replace($strip,$rplace,$feed);
			}
			 echo $feed;
		}
			
		header('Content-type: text/xml');
?>
	
		<rss version="2.0">
		  <channel>		
<?php     
		rssInfo($_GET['bID'], $site,  $_GET['ctID']);
			
?>
     	  </channel>
		</rss>