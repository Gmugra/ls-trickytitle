<?php

/* ---------------------------------------------------------------------------
 * Plugin Name: Tricky Title 
 * Plugin Version: 1.0
 * Author: Gmugra
 * Author URI: http://mmozg.net
 * LiveStreet Version: 1.0.1
 * ----------------------------------------------------------------------------
 *   GNU General Public License, version 2:
 *   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class PluginTrickytitle_ModuleTitletext extends PluginTrickytitle_ModuleTagtext {

  function Init() {
  }

  public function doTitle($oSmarty, $aTitle, $sParams, $sPage ) {

      if (!isset($aTitle) ) {
	
	return;
      }	

      $aTitle = $this->fillDefaults($aTitle, Config::Get("plugin.trickytitle.title"), $sParams, $sPage );

      $sHtmlTitle = $oSmarty->getTemplateVars("sHtmlTitle");

      $sViewName = Config::Get("view.name");
	
      if ($aTitle["mode_view_name"] != "on") {

        $sHtmlTitle = str_replace(array(" / ".$sViewName, $sViewName), "", $sHtmlTitle);
      }

      $sTags = "";
      if ($aTitle["show_tags"] ) {
        
        $sTags = $this->getTags($oSmarty, $sHtmlTitle, $aTitle["show_tags_max"] );
      }

      $sBlogs = "";
      if ($aTitle["show_blogs"] ) {

	$sBlogs = $this->getBlogs($oSmarty, $sHtmlTitle, $aTitle["show_blogs_max"], 
		                  $aTitle["include_personal_blogs"]?NULL:$this->Lang_Get("blogs_personal_title") );
      }

      $sHtmlTitle = $this->doText($oSmarty, $sHtmlTitle, $aTitle, $sParams, $sPage, $sTags, $sBlogs );

      
      if ($aTitle["show_tags_mode"] == "afterviewname") {

        $sViewName .= " ".$sTags;
      }
      if ($aTitle["show_blogs_mode"] == "afterviewname") {

        $sViewName .= " ".$sBlogs;
      }
	

      if ($aTitle["mode_view_name"] == "before" && $sHtmlTitle != "") {
        
        $sHtmlTitle = $sViewName.$aTitle["separator"].$sHtmlTitle;
      } else if ($aTitle["mode_view_name"] == "after" && $sHtmlTitle != "") {

        $sHtmlTitle .= $aTitle["separator"].$sViewName;
      } else if ($aTitle["mode_view_name"] != "off" && $aTitle["mode_view_name"] != "on"  ) {
      
        $sHtmlTitle = $sViewName;
      }
 
      $oSmarty->assign("sHtmlTitle", $sHtmlTitle);                                                                               
    }

    protected function doText($oSmarty, $sOriginalValue, $aObject, $sParams, $sPage, $sTags, $sBlogs) {


      if ($aObject["mode"] == "after") {
        
        $sOriginalValue = $this->insertOriginalList($sOriginalValue, $sTags, $aObject["show_tags_mode"]);
        $sOriginalValue = $this->insertOriginalList($sOriginalValue, $sBlogs, $aObject["show_blogs_mode"]);

        if ($aObject["show_value"] ) {

          if ($sOriginalValue != "" ) {
           
            $sOriginalValue .= $aObject["separator"];
          }
          
          $sOriginalValue .= $this->Lang_Get($aObject["value"]);

	  $sOriginalValue = $this->insertValueList($sOriginalValue, $sTags, $aObject["show_tags_mode"]);
          $sOriginalValue = $this->insertValueList($sOriginalValue, $sBlogs, $aObject["show_blogs_mode"]);	
        }   
         
        $sOriginalValue = $this->doPeriodAndPageText($sOriginalValue,$aObject,$sParams,$sPage);
      } else {

        $sText = "";

        if ($aObject["show_value"] ) {
          
          $sText = $this->Lang_Get($aObject["value"]);

          $sText = $this->insertValueList($sText, $sTags, $aObject["show_tags_mode"]);
          $sText = $this->insertValueList($sText, $sBlogs, $aObject["show_blogs_mode"]);
          
        }

        $sText = $this->doPeriodAndPageText($sText, $aObject,$sParams,$sPage);

        if ($sOriginalValue != "" ) {
	
          $sOriginalValue = $this->insertOriginalList($sOriginalValue, $sTags, $aObject["show_tags_mode"]);
          $sOriginalValue = $this->insertOriginalList($sOriginalValue, $sBlogs, $aObject["show_blogs_mode"]);
        } 

        if ($sText != "" ) {

          $sOriginalValue = $sText.$aObject["separator"].$sOriginalValue;
        }
      }      
 
      return $sOriginalValue;
    }

    protected function insertValueList($sValue, $sList, $sListMode) {

      if ($sList != "" && isset($sValue) && $sValue != "" ) { 	

        if ($sListMode == "aftervalue" ) { 

          $sValue .= " ".$sList;
        }
      }

      return $sValue; 
    }

    protected function insertOriginalList($sOriginalValue, $sList, $sListMode) {
      
      if ($sList != "" && isset($sOriginalValue) && $sOriginalValue != "" ) { 	

        if ($sListMode == "afteroriginal" ) {

          $sOriginalValue .= " ".$sList;
        } else if ($sListMode == "afterfirst" ) {

          $pos = strpos($sOriginalValue, " /");
          if ($pos === false) {
    
            $sOriginalValue .= " ".$sList;
          } else {
    
            $sOriginalValue = 
              substr($sOriginalValue, 0, $pos + 1 )." ".$sList.substr($sOriginalValue, $pos);
          }
		
        }
      }

      return $sOriginalValue; 
    }

    protected function fillDefaults($aObject, $aDefauts, $sParams, $sPage) {
      
      if (!isset($aObject["mode"] ) ) {
        
        $aObject["mode"] = $aDefauts["mode"];
      }	 

      if (!isset($aObject["separator"] ) ) {
        
        $aObject["separator"] = $aDefauts["separator"];
      }

      if (!isset($aObject["show_period"] ) ) {
        
        $aObject["show_period"] = $aDefauts["show_period"];
      }

      if (!isset($aObject["show_page"] ) ) {
        
        $aObject["show_page"] = $aDefauts["show_page"];
      }

      if (!isset($aObject["mode_view_name"]) && isset($aDefauts["mode_view_name"] ) ) {
        
        $aObject["mode_view_name"] = $aDefauts["mode_view_name"];
      }

      if (!isset($aObject["show_value"] ) ) {
        
        $aObject["show_value"] = $aDefauts["show_value"];
      }

      if (!isset($aObject["show_tags"] ) ) {
        
        $aObject["show_tags"] = $aDefauts["show_tags"];
      }

      if (!isset($aObject["show_tags_mode"] ) ) {
        
        $aObject["show_tags_mode"] = $aDefauts["show_tags_mode"];
      }

      if (!isset($aObject["show_tags_max"] ) ) {
        
        $aObject["show_tags_max"] = $aDefauts["show_tags_max"];
      }

      if (!isset($aObject["show_blogs"] ) ) {
        
        $aObject["show_blogs"] = $aDefauts["show_blogs"];
      }

      if (!isset($aObject["include_personal_blogs"] ) ) {
        
        $aObject["include_personal_blogs"] = $aDefauts["include_personal_blogs"];
      }

      if (!isset($aObject["show_blogs_mode"] ) ) {
        
        $aObject["show_blogs_mode"] = $aDefauts["show_blogs_mode"];
      }

      if (!isset($aObject["show_blogs_max"] ) ) {
        
        $aObject["show_blogs_max"] = $aDefauts["show_blogs_max"];
      }

      if ($aObject["show_period"] && !isset($aObject["default_period"] ) ) {
        
        $sPeriodSelectCurrent = $this->Viewer_GetSmartyObject()->getTemplateVars("sPeriodSelectCurrent");

        if (isset($sPeriodSelectCurrent) && isset($sPage) && $sPage == "1" 
            && (!isset($_SERVER["QUERY_STRING"] ) || stripos($_SERVER["QUERY_STRING"], "period=") === false ) ) {
          
          //if it first page and period not present in QUERY_STRING - set engine default 
          $aObject["default_period"] = $sPeriodSelectCurrent;
        } else {
          
          //in all other cases default period not set
          $aObject["default_period"] = "0";
        }

      }

      return $aObject; 
    }
}
