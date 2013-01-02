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

return array(
  "page_name" => "Страница %%pagenr%%",
  "stream_menu_user" => $this->Lang_Get("stream_menu_user").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("stream_menu"),
  "stream_menu_all" => $this->Lang_Get("stream_menu_all").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("stream_menu")
);

?>
