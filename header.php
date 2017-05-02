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

	echo '<nav>';
	$t = new Table( "nav", false );
	$t->header(
		mknav( 'index', 'Clients', 's-group' ),
		mknav( 'times', 'Times', 's-chart_bar' ),
		mknav( 'countries', 'Countries', 's-world' ),
		mknav( 'channels', 'Channels', 'ts-channel-green' ),
		mknav( 'bans', 'Bans', 's-exclamination' ),
		mknav( 'kicks', 'Kicks', 's-error' ),
		mknav( 'viewer', 'Viewer', 's-zoom' )
	);
	$t->output();
	echo '</nav>';

	$t = new Table( "footr", false );
	$t->header( '<h1>Teamspeak stats <span>' . TSSERVER_NAME . '</span></h1>' );
	$t->row( array( 'Last general update: ' . date('D d/m H:i:s', $serverinfo['lastupdate']) . ' (updates every 5th minute) since ' . STARTDATE . '</p>' ) );
	foreach( $headertexts as $h ) {
		$t->row( array( $h ) );
	}
	if($serverinfo['lastupdate'] < time()-600) {
		$t->row( array( '<font color="#E63900">Server or bot offline :(</font>' ) );
	} else {
		$t->row( array( '<font color="#339966">Everything seems to work for now :)</font>' ) );
	}
	$t->output();

