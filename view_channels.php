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

	include "countries.php";


	$alivechannels = $db->getAliveChannels( $serverinfo['lastupdate'] - 60 ); // some slack for update time (y)
	$ripchannels = $db->getRipChannels( $serverinfo['lastupdate'] - 60 ); // some slack for update time (y)

	$total = 0;
	foreach( $alivechannels as $c ) {
		$total += $c->totalusers;
	}

	if( $total > 0 ) {

		$t = new Table( 'listgraph chans r3', false );
		$t->header( 'n&deg;', 'Active channels: Name - usage', 'Most users' );
		$t->width( '28' );
		$i = 0;

		// TODO channel usage bar doesn't show very good in gecko browseres
		function printchannels( $pid, $depth ) {
			global $alivechannels, $t, $i, $total;
			foreach( $alivechannels as $chan ) {
				if( $chan->pid != $pid ) continue;

				$row = array();
				$row[] = ++$i;

				$perc = round( $chan->totalusers  * 100 / $total );
				$elem = mkchanlink( $chan->cid, $chan->name) . "({$perc}%)";
				$row[] = str_repeat( '<u></u>', 2 * $depth ) . ' ' . $elem . '<div style="margin-left:' . $depth * 2 . 'em;width:' . $perc . '%"></div>';

				if( $chan->maxclients == 0 ) {
					$row[] = "&nbsp;";
				} else {
					$row[] = $chan->maxclients;
				}
				
				$t->row( $row );
				printchannels( $chan->cid, $depth + 1 );
			}
		}

		printchannels( 0, 0 );

		$t->output();
	}

	$t = new Table( 'listgraph r3 r4', false );
	$t->header( 'n&deg;', 'Channel cemetery', 'Most users', 'Last seen' );
	$t->width( '28' );
	$i = 0;

	foreach( $ripchannels as $chan ) {

		$row = array();
		$row[] = ++$i;

		$row[] = mkchanlink( $chan->cid, $chan->name);

		if( $chan->maxclients == 0 ) {
			$row[] = "&nbsp;";
		} else {
			$row[] = $chan->maxclients;
		}

		$row[] = parselastonline( $chan->lastseen );
		
		$t->row( $row );
	}

	$t->output();

