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

  protected function getBlogsAsArray($aGlobalBlog, $sNotIn, $sPersonalBlogsPrefix ) {
	  
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
          
          return array();
      }

      arsort($aGlobalFilteredBlog);

      $aResultBlog = array(); 
      foreach($aGlobalFilteredBlog as $sBlogTitle => $iCount) {
	  
	  if (isset($sPersonalBlogsPrefix ) && $sPersonalBlogsPrefix != "" && strpos($sBlogTitle,$sPersonalBlogsPrefix ) === 0 ) {continue;}
		  
          array_push($aResultBlog,$sBlogTitle);
      }

      return $aResultBlog;
  }

  protected function getBlogsByBlogsAsArray($oSmarty, $sNotIn, $sPersonalBlogsPrefix, $aIgnoreBlogs = array() ) {
	  
      $aBlogs = $oSmarty->getTemplateVars("aBlogs");
       
      if (!isset($aBlogs) || count($aBlogs) == 0 ) {
	      return array();
      }

      $aGlobalBlog = array();

      foreach($aBlogs as $oBlog) {
                              
	  $sBlogTitle = htmlspecialchars($oBlog->getTitle() );

	  if (in_array($sBlogTitle, $aIgnoreBlogs) ) {
	    continue;
	  }

          if (array_key_exists($sBlogTitle, $aGlobalBlog) ) {
	      
            $aGlobalBlog[$sBlogTitle] = 10000000 + $aGlobalBlog[$sBlogTitle];	
          } else {

            $aGlobalBlog[$sBlogTitle] = 10000000 + floor($oBlog->getRating() );
          }
      }

      return $this->getBlogsAsArray($aGlobalBlog, $sNotIn, $sPersonalBlogsPrefix );
  }

  protected function getBlogsByTopicsAsArray($oSmarty, $sNotIn, $sPersonalBlogsPrefix, $aIgnoreBlogs = array() ) {
	  
      $aTopic = $oSmarty->getTemplateVars("aTopics");
       
      if (!isset($aTopic) || count($aTopic) == 0 ) {
	      return array();
      }

      $aGlobalBlog = array();

      foreach($aTopic as $oTopic) {
                              
	  $sBlogTitle = htmlspecialchars($oTopic->getBlog()->getTitle() );

	  if (in_array($sBlogTitle, $aIgnoreBlogs) ) {
	    continue;
	  }

          if (array_key_exists($sBlogTitle, $aGlobalBlog) ) {
	      
            $aGlobalBlog[$sBlogTitle] = 10000000 + $aGlobalBlog[$sBlogTitle];	
          } else {

            $aGlobalBlog[$sBlogTitle] = 10000000 + floor($oTopic->getBlog()->getRating() );
          }
      }

      return $this->getBlogsAsArray($aGlobalBlog, $sNotIn, $sPersonalBlogsPrefix );
  }

    protected function getBlogs($oSmarty, $sNotIn, $iShowBlogsMax, $sPersonalBlogsPrefix, $sSeparator=", ", $sLeft="(", $sRight=")", $aIgnoreBlogs = array() ) {
     
      return	$this->getAsString(
			$this->getBlogsByTopicsAsArray($oSmarty, $sNotIn, $sPersonalBlogsPrefix, $aIgnoreBlogs ),
			$iShowBlogsMax, $sSeparator, $sLeft, $sRight 
		);
    }

    protected function getTagsAsArray($oSmarty, $sNotIn, $aIgnoreTags = array() ) {

      $aGlobalTag = array();
	    
      $aTopic = $oSmarty->getTemplateVars("aTopics");       
      if (!isset($aTopic) || count($aTopic) == 0 ) {
	 return array();
      }

        foreach($aTopic as $oTopic) {

          $aTag = explode(",",$oTopic->getTags() );
          foreach($aTag as $sTag) {

            $sTag2 = htmlspecialchars($sTag);	    

	    if (in_array($sTag2, $aIgnoreTags) ) {
	      continue;
	    }

            if (array_key_exists($sTag2, $aGlobalTag)   ) {
	      
              $aGlobalTag[$sTag2]++;	
            } else {

              $aGlobalTag[$sTag2] = 1;
            } 
  	
          }
	}

	if (count($aGlobalTag) == 0 ) {
          
          return array();
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
          
          return array();
        }

	arsort($aGlobalFilteredTag);

	$iMin = ceil((array_sum($aGlobalFilteredTag )/count($aGlobalFilteredTag ) ) - 0.11 );

	$aResultTags = array();
	foreach($aGlobalFilteredTag as $sTag => $iCount) {
	  if ($iCount >= $iMin ) { 

            array_push($aResultTags,$sTag); 
  	  }
	}

	return $aResultTags;
    }

    protected function getTags($oSmarty, $sNotIn, $iShowTagsMax, $sSeparator=", ", $sLeft="(", $sRight=")", $aIgnoreTags = array()  ) {

	return	$this->getAsString(
			$this->getTagsAsArray($oSmarty, $sNotIn, $aIgnoreTags),
			$iShowTagsMax, $sSeparator, $sLeft, $sRight 
		);
    }

    protected function getAsString($aArray, $iShowMax, $sSeparator=", ", $sLeft="(", $sRight=")" ) {
	
	$sResult = "";

        if (!isset($aArray) || count($aArray) == 0 ) {
          
          return $sResult;
        }

        $i = 1;
        foreach($aArray as $sString ) {
	   
	  if ($i > $iShowMax ) {break; } 
            
          $sResult .= $sString.$sSeparator;
          $i++;
        }

        if ($sResult != "" ) {

          $sResult = $sLeft.rtrim($sResult,$sSeparator ).$sRight;
        }
     
	return $sResult;    
    }
}
?>
