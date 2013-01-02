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


/*
Плагин предназначен для динамической модификации содержимого HTML тега <title>...</title>.
Таким образом, чтобы оно выглядело информативно для поисковых систем, 
было уникальным для любой страницы, и вместе с тем, не теряло осмысленности.

В первую очередь нацелено на различные страницы со списками топиков.
Плагин активно использует данные из аTopics и aPaging.


Структура результата (в типичных случаях, кое-что можно менять конфигурационными параметрами) выглядит так:

<original><s><value (tags)><s><period><s><page><s><site>

<original (blogs)><s><period><s><page><s><site>

где:
original - оригинальное значение title сгенерированное движком
s - текст-разделитель. Обычно какой-то символ обрамлённый пробелами
value - дополнительная фраза которую можно задать для каждой страницы индивидуально
(tags) - список тегов заключенный в скобки, которым может быть дополнено value или original 
(blogs) - список названий блогов заключенный в скобки, которым может быть дополнено value или original
period - текущий временной интервал
page - текущая страница
site - название сайта (view.name стандартной конфигурации LiveStreet)


Примеры:

EVE-online (песочница, видео, индустрия) / Топ / За все время / Страница 2 / MMOзговед

Обсуждаемые (Kerbal Space Program, Guild Wars 2, Lineage II) / За 30 дней / Страница 3 / MMOзговед


Откуда берется value?
Для каждой страницы задаётся один ключ стандартной локализации LiveStreet.
Настоятельно рекомендуемый метод - использовать глобальные ключи, например blog_menu_all.
Можно ввести собственный ключ в локализационных файлах плагина, которые тут:
plugins/trickytitle/templates/language
В последнем случае, при использовании в этом конфигурационном файле ключ нужно дополнять префиксом плагина:
plugin.trickytitle.my_text_key
 

Как работает генерация списка тегов?
Собираются все теги из топиков на странице. 
Отбрасываются те, которые неточно (неточность примитивная, но лучше чем ничего ) совпадают с частью original.
Отбрасываются также те, которые, по мнению плагина, встречаются слишком редко для данной страницы.
Результат сортируется по частоте встречаемости, и первые N тегов (N задаётся конфигурационным параметром ),
из этого отсортированного списка, попадают в заголовок.


Как работает генерация списка названий блогов?
Собираются все названия блогов из топиков на странице.
Сортируются во-первых по частоте встречаемости, и во-вторых по собственному рейтингу (getRating() )
Первые N блогов (N задаётся конфигурационным параметром ), из этого отсортированного списка,
попадают в заголовок.
*/



/* Defaults { */

//По умолчанию, все конфигурации страниц имеют значения параметров как в этом блоке.
//Однако, любой параметр может быть переопределён для конкретной страницы.

//Текст-разделитель.
$config["title"]["separator"] = " / ";

//Указывает куда, относительно оригинального заголовка, добавлять расширяющий его текст.
//before - добавлять перед оригинальным заголовком.
//after- добавлять после оригинального заголовка.
$config["title"]["mode"] = "after"; // before | after

//Показывать или нет дополнительную фразу, которая задается индивидуально для каждой страницы.
//В случае если значение false, списки тегов и блогов в режиме aftervalue тоже не будут показываться.
//Текстова фраза всегда идёт вначале генерируемой части.
$config["title"]["show_value"] = true;

//Показывать или нет информацию о выбранном временном интервале.
//По возможности (и если НЕ активна опция ["title"]["default_period"]  ), 
//плагин пытается определить временной интервал "по умолчанию",
//и такой временной интервал не будет показываться даже при значении true.
//Информация о временном интервале всегда идёт после дополнительной фразы страницы.
$config["title"]["show_period"] = true;

//устанавливает временной интервал, который будет браться "по умолчанию", вопреки мнению движка
//all - "За всё время"
//30 - "За 30 дней"
//7 - "За 7 дней"
//1 - "За 24 часа"
//Если эта опция активна, то плагин НЕ будет пытаться определить временной интервал "по умолчанию",
//в качестве такого интервала всегда будет использоваться занное в этой опции значение
//$config["title"]["default_period"] = "all"; //all | 30 | 7 | 1

//Показывать или нет информацию о текущей странице.
//Информация о первой странице не показывается даже при значении true.
//Информация о странице всегда идёт после информации о временном интервале.
$config["title"]["show_page"] = true;


//Определяет поведение плагина в отношении названия сайта, которое всегда присутствует в заголовке.
//on - ничего не делать.
//off - окончательно и бесповоротно убирать название сайта.
//before - перебрасывать название сайта в самое начало, после всех модификаций.
//after - перебрасывать название сайта в самый конец, после всех модификаций.
$config["title"]["mode_view_name"] = "after"; // on | off | before | after

//генерировать или нет список тегов
$config["title"]["show_tags"] = false;

//где выводить список тегов
//aftervalue - после дополнительной фразы
//afteroriginal - после оригинального значения title
//afterfirst - после первой (до разделителя) фразы оригинального значения title
//afterviewname - после названия сайта
$config["title"]["show_tags_mode"] = "afteroriginal"; // aftervalue | afteroriginal | afterfirst | afterviewname

//максимальное количество тегов в списке
$config["title"]["show_tags_max"] = 5;

//генерировать или нет список названий блогов
$config["title"]["show_blogs"] = true;

//где выводить список названий блогов
//aftervalue - после дополнительной фразы
//afteroriginal - после оригинального значения title
//afterfirst - после первой (до разделителя) фразы оригинального значения title
//afterviewname - после названия сайта
$config["title"]["show_blogs_mode"] = "aftervalue"; // aftervalue | afteroriginal | afterfirst | afterviewname

//максимальное количество названий блогов в списке
$config["title"]["show_blogs_max"] = 5;

/* Defaults } */


/*  
Структура конфигурации страницы:

$config["action"]["event"]["firstparam"]["title"]["paramname"]

action - action LiveStreet, к которому относится страница. То, что возвращает Router::GetAction()

event - событие LiveStreet, к которому относится страница. То, что возвращает Router::GetActionEvent()
-- Если движок не возвращает событие для страницы, то нужно использовать "" (пустая строка)
-- Чтобы описать событие можно использовать регулярное выражение, с символом #, в качестве ограничителя.
-- Если необходимо задать конфигугацию для любых событий, то нужно использовать "*"
-- Конфигурации с "*", имеют более низкий приоретет чем конфигурации с регулярным выражением

firstparam - первый параметр, то что возвращает Router::GetParams()[0]
-- Если требуется убедится, что первого параметра нет, то нужно использовать "-"
-- Если значение параметра и/или вообще его наличие безразлично, то нужно использовать "*"
-- Конфигурации с "*", имеют более низкий приоретет чем конфигурации с "-"

title - константа, всегда так.

paramname - любой параметр описанный секции Defaults выше.

Общая рекомендация -  вырубать параметрами, всё, что не получиться использовать на старнице.
Т.е. если известно что, например, фильтра  по временному интервалу там нет, 
то стоит задать show_period = false.

Все параметры, которые не указаны для конкретной страницы получат значения,
указанные в блоке "по умолчанию".
*/



/* index action { */

$config["index"][""]["*"]["title"]["value"] = "blog_menu_all";
$config["index"][""]["*"]["title"]["mode_view_name"] = "before";
$config["index"][""]["*"]["title"]["show_blogs_mode"] = "afterviewname";

$config["index"]["#page(\d+)#i"]["*"]["title"]["value"] = "blog_menu_all";
$config["index"]["#page(\d+)#i"]["*"]["title"]["mode_view_name"] = "before";
$config["index"]["#page(\d+)#i"]["*"]["title"]["show_blogs_mode"] = "afterviewname";

$config["index"]["newall"]["*"]["title"]["value"] = "blog_menu_all_new";
$config["index"]["newall"]["*"]["title"]["default_period"] = "all";
$config["index"]["newall"]["*"]["title"]["show_period"] = false;
$config["index"]["newall"]["*"]["title"]["mode_view_name"] = "before";
$config["index"]["newall"]["*"]["title"]["show_blogs_mode"] = "afterviewname";

$config["index"]["discussed"]["*"]["title"]["value"] = "blog_menu_all_discussed";
$config["index"]["discussed"]["*"]["title"]["mode_view_name"] = "before";
$config["index"]["discussed"]["*"]["title"]["show_blogs_mode"] = "afterviewname";

$config["index"]["top"]["*"]["title"]["value"] = "blog_menu_all_top";
$config["index"]["top"]["*"]["title"]["mode_view_name"] = "before";
$config["index"]["top"]["*"]["title"]["show_blogs_mode"] = "afterviewname";

$config["index"]["new"]["*"]["title"]["value"] = "blog_menu_all_only_new";
$config["index"]["new"]["*"]["title"]["mode_view_name"] = "before";
$config["index"]["new"]["*"]["title"]["show_blogs_mode"] = "afterviewname";

/* index action } */



/* feed action { */

$config["feed"]["index"]["*"]["title"]["value"] = "userfeed_title";
$config["feed"]["index"]["*"]["title"]["show_period"] = false;
$config["feed"]["index"]["*"]["title"]["show_page"] = false;
$config["feed"]["index"]["*"]["title"]["mode_view_name"] = "on";
$config["feed"]["index"]["*"]["title"]["mode"] = "before";

/* feed action } */



/* blog action { */

$config["blog"]["*"]["-"]["title"]["show_value"] = false;
$config["blog"]["*"]["-"]["title"]["show_tags"] = true;
$config["blog"]["*"]["-"]["title"]["show_blogs"] = false;

$config["blog"]["*"]["discussed"]["title"]["value"] = "blog_menu_collective_discussed";
$config["blog"]["*"]["discussed"]["title"]["show_tags"] = true;
$config["blog"]["*"]["discussed"]["title"]["show_blogs"] = false;

$config["blog"]["*"]["newall"]["title"]["value"] = "blog_menu_collective_new";
$config["blog"]["*"]["newall"]["title"]["default_period"] = "all";
$config["blog"]["*"]["newall"]["title"]["show_period"] = false;
$config["blog"]["*"]["newall"]["title"]["show_tags"] = true;
$config["blog"]["*"]["newall"]["title"]["show_blogs"] = false;

$config["blog"]["*"]["top"]["title"]["value"] = "blog_menu_collective_top";
$config["blog"]["*"]["top"]["title"]["show_tags"] = true;
$config["blog"]["*"]["top"]["title"]["show_blogs"] = false;
           
/* blog action } */



/* people action { */

$config["people"][""]["*"]["title"]["value"] = "people_menu_users_all";
$config["people"][""]["*"]["title"]["show_period"] = false;
$config["people"][""]["*"]["title"]["show_blogs"] = false;

$config["people"]["index"]["*"]["title"]["value"] = "people_menu_users_all";
$config["people"]["index"]["*"]["title"]["show_period"] = false;
$config["people"]["index"]["*"]["title"]["show_blogs"] = false;

$config["people"]["online"]["*"]["title"]["value"] = "people_menu_users_online";
$config["people"]["online"]["*"]["title"]["show_period"] = false;
$config["people"]["online"]["*"]["title"]["show_blogs"] = false;

$config["people"]["new"]["*"]["title"]["value"] = "people_menu_users_new";
$config["people"]["new"]["*"]["title"]["show_period"] = false;
$config["people"]["new"]["*"]["title"]["show_blogs"] = false;                           

/* people action } */



/* tag action { */

$config["tag"]["*"]["*"]["title"]["show_value"] = false;
$config["tag"]["*"]["*"]["title"]["show_blogs_mode"] = "afterfirst";
$config["tag"]["*"]["*"]["title"]["show_period"] = false;

/* tag action } */



/* stream action { */

$config["stream"]["user"]["*"]["title"]["value"] = "plugin.trickytitle.stream_menu_user";
$config["stream"]["user"]["*"]["title"]["show_period"] = false;
$config["stream"]["user"]["*"]["title"]["show_page"] = false;
$config["stream"]["user"]["*"]["title"]["show_blogs"] = false;
$config["stream"]["user"]["*"]["title"]["mode_view_name"] = "on";
$config["stream"]["user"]["*"]["title"]["mode"] = "before"; 

$config["stream"]["all"]["*"]["title"]["value"] = "plugin.trickytitle.stream_menu_all";
$config["stream"]["all"]["*"]["title"]["show_period"] = false;
$config["stream"]["all"]["*"]["title"]["show_page"] = false;
$config["stream"]["all"]["*"]["title"]["show_blogs"] = false;
$config["stream"]["all"]["*"]["title"]["mode_view_name"] = "on";
$config["stream"]["all"]["*"]["title"]["mode"] = "before"; 

/* tag action } */



/* search action { */

$config["search"]["topics"]["*"]["title"]["show_value"] = false;
$config["search"]["topics"]["*"]["title"]["show_blogs_mode"] = "afterfirst";
$config["search"]["topics"]["*"]["title"]["show_period"] = false;
$config["search"]["topics"]["*"]["title"]["mode"] = "after"; 

/* search action } */

return $config;

?>
