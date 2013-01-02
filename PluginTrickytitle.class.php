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

/**
 * Forbid direct access to the file
 */
if (!class_exists('Plugin')) {
	die('Hacking attempt!');
}

class PluginTrickytitle extends Plugin {

  protected $aInherits=array(
    'module'=>array('ModuleViewer'),
  );

  //Plugin activation
  public function Activate() {
    return true;
  }	

  //Plugin init
  public function Init() {
  }
}
?>