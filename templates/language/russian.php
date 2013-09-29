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

return array(
  "page_name" => "Страница %%pagenr%%",
  "stream_menu_user" => $this->Lang_Get("stream_menu_user").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("stream_menu"),
  "stream_menu_all" => $this->Lang_Get("stream_menu_all").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("stream_menu"),
  
  "blog_menu_collective_good" => $this->Lang_Get("blog_menu_collective").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_collective_good"),
  "blog_menu_collective_new" => $this->Lang_Get("blog_menu_collective").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_collective_new"),
  "blog_menu_collective_discussed" => $this->Lang_Get("blog_menu_collective").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_collective_discussed"),
  "blog_menu_collective_top" => $this->Lang_Get("blog_menu_collective").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_collective_top"),
  
  "blog_menu_personal_good" => $this->Lang_Get("blog_menu_personal").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_personal_good"),
  "blog_menu_personal_new" => $this->Lang_Get("blog_menu_personal").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_personal_new"),
  "blog_menu_personal_discussed" => $this->Lang_Get("blog_menu_personal").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_personal_discussed"),
  "blog_menu_personal_top" => $this->Lang_Get("blog_menu_personal").Config::Get("plugin.trickytitle.title.separator").$this->Lang_Get("blog_menu_personal_top"),
);

?>
