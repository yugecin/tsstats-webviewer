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

	$parent = $db->getChannel( $data->pid );

	$t = new Table( 'various r1', 2 );
	$t->header( 'Various stats' );
	$t->row( array( 'Status', $data->lastseen < $serverinfo['lastupdate'] - 60 ? 'Deleted' : 'Active' ) );
	$t->row( array( 'Last seen', parselastonline( $data->lastseen ) . ' - ' . date( 'j/m/Y H:i:s', $data->lastseen ) ) );
	$t->row( array( 'Parent', $parent === false ? '' : mkchanlink( $parent->cid, $parent->name ) ) );
	$t->row( array( 'Max clients seen', $data->maxclients ) );
	$t->row( array( 'Topic', $data->topic ) );
	$t->output();

	$limit = 25;
	$usage = $db->getChannelUsage( $data->cid, $limit );
	$total = $db->getTotalChannelUsage( $data->cid );

	$t = new Table( 'listgraph', false );
	$t->header( 'n&deg;', 'Top users (limited to ' . $limit . ' users)' );
	$t->width( 28 );
	$i = 0;
	foreach( $usage as $u ) {
		$row = array();
		$row[] = ++$i;

		$perc = round( $u->c * 100 / $total );
		$row[] = mkclientlink( $u->uid, $u->currentname ) . ': ' . $perc . '% (' . $u->c . ')<div style="width:' . $perc . '%"></div>';
		$t->row( $row );
	}
	$t->output();

