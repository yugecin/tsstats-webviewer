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

	$__time = microtime( true );

	// TODO: parse server log to generate data instead of serverquery?

	include "db.php";
	include "funcs.php";
	include "table.php";

	$db = new Database();
	$db->getServerInfo( $serverinfo );

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Teamspeak stats <?php echo TSSERVER_NAME; ?></title>
	<link type="text/css" rel="stylesheet" href="reset.css" />
	<link type="text/css" rel="stylesheet" href="style.css" />
	<link type="text/css" rel="stylesheet" href="silk.css" />
	<link type="text/css" rel="stylesheet" href="flags.css" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<style type="text/css">
		table.graph td:nth-child(<?php echo date('G') + 1; ?>) {
			background: #999;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<div>
	<?php
		include "header.php";

		$views = array(
			'index',
			'client',
			'times',
			'countries',
			'channels',
			'channel',
			'bans',
			'kicks',
			'viewer',
		);

		$view = "index";
		if( isset( $_GET[ "view" ] ) && in_array( $_GET[ "view" ], $views ) )
		{
			$view = $_GET[ "view" ];
		}

		include "view_{$view}.php";

		include "footer.php";
	?>
	</div>
</body>
</html>
