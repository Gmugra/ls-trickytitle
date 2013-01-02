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

class PluginTrickytitle_ModuleTagtext extends Module {

  protected $aStopChars = array("_","-"," ","\t");
  protected $aEmpty = array("","","","");


  function Init() {
  }

  protected function doPeriodAndPageText($sText, $aObject, $sParams, $sPage ) {
    
   if ($aObject["show_period"] && isset($sParams ) ) {

     if ($aObject["default_period"] != "1" && stripos($sParams, "?period=1") !== false ) {
          
       if ($sText != "" ) { $sText .= $aObject["separator"]; }
       $sText .= $this->Lang_Get("blog_menu_top_period_24h"); 
     } else if ($aObject["default_period"] != "7" && stripos($sParams, "?period=7") !== false ) {

       if ($sText != "" ) { $sText .= $aObject["separator"]; }
       $sText .= $this->Lang_Get("blog_menu_top_period_7d");
     } else if ($aObject["default_period"] != "30" && stripos($sParams, "?period=30") !== false ) {

       if ($sText != "" ) { $sText .= $aObject["separator"]; }
       $sText .= $this->Lang_Get("blog_menu_top_period_30d");
     } else if ($aObject["default_period"] != "all" && stripos($sParams, "?period=all") !== false ) {

       if ($sText != "" ) { $sText .= $aObject["separator"]; }
         $sText .= $this->Lang_Get("blog_menu_top_period_all");
       }
    } 
    if ($aObject["show_page"] && isset($sPage) && $sPage != "1" && $sPage != "" ) {

      if ($aObject["show_value"] || $sText != "") { $sText .= $aObject["separator"]; }

        $sText .= $this->Lang_Get("plugin.trickytitle.page_name",array("pagenr" => $sPage ) );
    }

    return $sText; 
  }

  protected function getBlogs($oSmarty, $sNotIn, $iShowBlogsMax) {
     
      $sBlogs = "";

      $aTopic = $oSmarty->getTemplateVars("aTopics");
       
      if (isset($aTopic) && count($aTopic) > 0 ) {

        $aGlobalBlog = array();

        foreach($aTopic as $oTopic) {
                              
          $sBlogTitle = htmlspecialchars($oTopic->getBlog()->getTitle() ); 

          if (array_key_exists($sBlogTitle, $aGlobalBlog) ) {
	      
            $aGlobalBlog[$sBlogTitle] = 10000000 + $aGlobalBlog[$sBlogTitle];	
          } else {

            $aGlobalBlog[$sBlogTitle] = 10000000 + floor($oTopic->getBlog()->getRating() );
          }
        }

        $aGlobalFilteredBlog = array();

        $sToCompare = str_replace($this->aStopChars,$this->aEmpty,mb_strtolower($sNotIn));

        foreach($aGlobalBlog as $sBlogTitle => $iCount) {
	   
          if ( 
              stripos($sToCompare,
                str_replace($this->aStopChars,$this->aEmpty,mb_strtolower($sBlogTitle ) ) ) === false ) {
            
            $aGlobalFilteredBlog[$sBlogTitle] = $iCount;
  	  }
        }

        if (count($aGlobalFilteredBlog) == 0 ) {
          
          return $sBlogs;
        }

        arsort($aGlobalFilteredBlog);

        $i = 1;
        foreach($aGlobalFilteredBlog as $sBlogTitle => $iCount) { 
          
          if ($i > $iShowBlogsMax ) {break; }
                
          $sBlogs .= $sBlogTitle.", ";

          $i++;
        }

        if ($sBlogs != "" ) {

          $sBlogs = "(".rtrim($sBlogs,", " ).")";
        }
      }

      return $sBlogs;
    }

    protected function getTags($oSmarty, $sNotIn, $iShowTagsMax) {

      $sTags = "";

      $aTopic = $oSmarty->getTemplateVars("aTopics");       
      if (isset($aTopic) && count($aTopic) > 0 ) {

        $aGlobalTag = array();

        foreach($aTopic as $oTopic) {

          $aTag = explode(",",$oTopic->getTags() );
          foreach($aTag as $sTag) {

            $sTag2 = htmlspecialchars($sTag);	    

            if (array_key_exists($sTag2, $aGlobalTag) ) {
	      
              $aGlobalTag[$sTag2]++;	
            } else {

              $aGlobalTag[$sTag2] = 1;
            } 
  	
          }
        }

        $aGlobalFilteredTag = array();

        $sToCompare = str_replace($this->aStopChars,$this->aEmpty,mb_strtolower($sNotIn));

        foreach($aGlobalTag as $sTag => $iCount) {
	   
          if ( 
              stripos($sToCompare,
                str_replace($this->aStopChars,$this->aEmpty,mb_strtolower($sTag ) ) ) === false ) {
            
            $aGlobalFilteredTag[$sTag] = $iCount;
  	  }
        }

        if (count($aGlobalFilteredTag) == 0 ) {
          
          return $sTags;
        }

        arsort($aGlobalFilteredTag);
        
        $iMin = ceil((array_sum($aGlobalFilteredTag )/count($aGlobalFilteredTag ) ) - 0.11 );

        $i = 1;
        foreach($aGlobalFilteredTag as $sTag => $iCount) {
	   
	  if ($i > $iShowTagsMax ) {break; }

          if ($iCount >= $iMin ) { 
              
            
            $sTags .= $sTag.", ";
  	  }
          $i++;
        }

        if ($sTags != "" ) {

          $sTags = "(".rtrim($sTags,", " ).")";
        }
      }

      return $sTags; 
    }

}
?>