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
 
class PluginTrickytitle_ModuleViewer extends PluginTrickytitle_Inherit_ModuleViewer {

    public function Display($sTemplate) {

      $aParamsConfig = $this->getParamsConfig();

      if (isset($aParamsConfig) ) {
	
	$aPaging = $this->getPaging();

	$this->doTrickyTitle($aParamsConfig, $aPaging );
	$this->doTrickyKeywords($aParamsConfig );
      }

      parent::Display($sTemplate);	
    }

    protected function doTrickyKeywords($aParamsConfig ) { 

	    $this->PluginTrickytitle_ModuleKeywordstext_doKeywords(
		    $this->Viewer_GetSmartyObject(), 
		    isset($aParamsConfig["keywords"])?$aParamsConfig["keywords"]:array() ); 
    }

    protected function doTrickyTitle($aParamsConfig, $aPaging ) {

        $sParams = NULL;
        $sPage = "1";
        if (isset($aPaging) ) {

          $sParams = $aPaging["sGetParams"];
          $sPage = $aPaging["iCurrentPage"];
        }
	  
        $this->PluginTrickytitle_ModuleTitletext_doTitle(
		$this->Viewer_GetSmartyObject(), 
		isset($aParamsConfig["title"])?$aParamsConfig["title"]:array(), 
		$sParams, $sPage );   
    }    

    protected function getPaging() {
	   
        return $this->Viewer_GetSmartyObject()->getTemplateVars("aPaging");
    }

    protected function getParamsConfig() {

	$sAction = Router::GetAction();

        $aActionConfig = Config::Get("plugin.trickytitle.".$sAction);
        if (!isset($aActionConfig ) ) {

          return;
        }

        $sActionEvent = Router::GetActionEvent();
        $sActionEvent = isset($sActionEvent )?$sActionEvent:"";

        $aEventConfig;
        if (isset($aActionConfig[$sActionEvent] ) ) {

          $aEventConfig = $aActionConfig[$sActionEvent];
        } else {
        
           foreach(array_keys($aActionConfig) as $sEventConfig ) {
		
	    if ( substr($sEventConfig,0,1) == "#"
	         && preg_match($sEventConfig,$sActionEvent) > 0 ) {

              $aEventConfig = $aActionConfig[$sEventConfig];
	      	
              break;
            }	
          }
        }
        if (!isset($aEventConfig ) && isset($aActionConfig["*"] ) ) {

          $aEventConfig = $aActionConfig["*"];
        } else if (!isset($aEventConfig ) ) {

          return;
        }
	
        $aParams = Router::GetParams();
        $aParamsConfig;
	if (!isset($aParams[0]) && isset($aEventConfig["-"]) ) {

	  $aParamsConfig = $aEventConfig["-"];
        } else if (isset($aParams[0]) && isset($aEventConfig[$aParams[0]] ) ) {

          $aParamsConfig = $aEventConfig[$aParams[0]];
        } else if (isset($aEventConfig["*"]) ) {

          $aParamsConfig = $aEventConfig["*"];
        } else {
        
          return;
	}

	return $aParamsConfig;
    }  
}
?>
