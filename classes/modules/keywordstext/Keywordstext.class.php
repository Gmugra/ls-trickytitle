<?php

/* ---------------------------------------------------------------------------
 * Plugin Name: Tricky Title 
 * Plugin Version: 2.0
 * Author: Gmugra
 * Author URI: http://mmozg.net
 * LiveStreet Version: 1.0.1
 * ----------------------------------------------------------------------------
 *   GNU General Public License, version 2:
 *   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class PluginTrickytitle_ModuleKeywordstext extends PluginTrickytitle_ModuleTagtext {

	function Init() {
	}

	public function doKeywords($oSmarty, $aKeywords ) {
		
		$aKeywords = $this->fillDefaults($aKeywords, Config::Get("plugin.trickytitle.keywords") );
		if ($aKeywords["on"] === "false"){

			return;
		}

		$aAdded = array();
		if ($aKeywords["view_name"] ) {
			array_push($aAdded,Config::Get("view.name") );
		}

		if ($aKeywords["show_blogs"] ) {

			$aTopic = $oSmarty->getTemplateVars("aTopics");
			if (isset($aTopic) && count($aTopic) > 0 ) {

				$aAdded = 
					array_merge($aAdded,
						$this->getBlogsByTopicsAsArray(
							$oSmarty, "", 
							$aKeywords["include_personal_blogs"]?"":$this->Lang_Get("blogs_personal_title"),
							$aAdded
						)
					);

				if ($aKeywords["show_tags"] ) {
					$aAdded = array_merge($aAdded, $this->getTagsAsArray($oSmarty, "", $aAdded ) );
				}

			} else {
				
				$oTopic = $oSmarty->getTemplateVars("oTopic");
				if (isset($oTopic) ) {
					$aAdded = 
						array_merge($aAdded,
							$this->getBlogByTopicAsArray(
								$oTopic,
								$aKeywords["include_personal_blogs"]?"":$this->Lang_Get("blogs_personal_title"),
								$aAdded
							)
						);

					if ($aKeywords["show_tags"] ) {
						$aAdded = array_merge($aAdded, $this->getTagsByTopicAsArray($oTopic, "", $aAdded ) );
					}
				} else {

					$aAdded = 
						array_merge($aAdded,
							$this->getBlogsByBlogsAsArray(
								$oSmarty, "", 
								$aKeywords["include_personal_blogs"]?"":$this->Lang_Get("blogs_personal_title"),
								$aAdded
							)
						);
				}
			}

		} else if ($aKeywords["show_tags"] ) {
			
			$aAdded = array_merge($aAdded, $this->getTagsAsArray($oSmarty, "", $aAdded ) );
		}

		$oSmarty->assign("sHtmlKeywords", $this->getAsString($aAdded, $aKeywords["show_max"],",","","" ) );
	}

	protected function fillDefaults($aObject, $aDefauts) {

		if (!isset($aObject["on"] ) ) {
        
        		$aObject["on"] = $aDefauts["on"];
		}

		if (!isset($aObject["view_name"] ) ) {
        
        		$aObject["view_name"] = $aDefauts["view_name"];
      		}

		if (!isset($aObject["show_tags"] ) ) {
        
        		$aObject["show_tags"] = $aDefauts["show_tags"];
      		}

		if (!isset($aObject["show_max"] ) ) {
        
			$aObject["show_max"] = $aDefauts["show_max"];
      		}

		if (!isset($aObject["show_blogs"] ) ) {
        
        		$aObject["show_blogs"] = $aDefauts["show_blogs"];
		}

		if (!isset($aObject["include_personal_blogs"] ) ) {
        
        		$aObject["include_personal_blogs"] = $aDefauts["include_personal_blogs"];
		}


		return $aObject; 
	}

}
?>

