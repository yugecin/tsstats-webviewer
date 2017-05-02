<?php
/*
 * Webviewer for my teamspeak3 statistics bot - see github.com/yugecin/tsstats
 * Copyright (C) 2014-2017  Robin C.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define("MYSQL_HOST", "127.0.0.1");
define("MYSQL_USER", "");
define("MYSQL_PASSWORD", "");
define("MYSQL_DB", "");

$todcolors = array( "0629AD", "10AE05", "B6B309", "AD0506" );
//$todcolors = array( "0936dc", "17ce0a", "d8d407", "d40b0c" );

define("TSSERVER_NAME", "myserver");
define("STARTDATE", "24/08/2014");

date_default_timezone_set('CET');

$headertexts = array(
	'All times are CET',
);


$footertexts = array(
	'<a href="https://github.com/yugecin/tsstats" target=_blank>github.com/yugecin/tsstats</a> <a href="https://github.com/yugecin/tsstats-webviewer" target=_blank>github.com/yugecin/tsstats-webviewer</a>', // I'd appreciate it if you leave this one here
	'For reference only, data may deviate. (But shouldn\'t too badly)',
	'This site uses <a href="http://www.famfamfam.com/lab/icons/silk/" target=_blank>silk icons</a> and <a href="http://www.famfamfam.com/lab/icons/flags/" target=_blank>flag icons</a> made by <a href="http://www.famfamfam.com" target=_blank>James M.</a>', // it would be nice if you left this one here aswell
);

