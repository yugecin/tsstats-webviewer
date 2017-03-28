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

	$times = array();
	$times[] = $db->getTimes( 10, 0, 5 );
	$times[] = $db->getTimes( 10, 6, 11 );
	$times[] = $db->getTimes( 10, 12, 17 );
	$times[] = $db->getTimes( 10, 18, 23 );

	$total = array();
	/*
	$total[] = $db->getTimesCount( 0, 5 );
	$total[] = $db->getTimesCount( 6, 11 );
	$total[] = $db->getTimesCount( 12, 17 );
	$total[] = $db->getTimesCount( 18, 23 );
	*/

	foreach( $times as $k => $v ) {
		$total[$k] = 0;
		foreach( $v as $vv ) {
			$total[$k] += $vv->c;
		}
	}
	
	$t = new Table( 'listgraph', false );
	$t->header( 'n&deg;', 'Nightcrawlers<br/>hours 0-6', 'Early birds<br/>hours 6-12', 'Afternoon shift<br/>hours 12-18', 'Evening chatters<br/>hours 18-24' );
	$t->width( '28' );
	for( $i = 0; $i < 10; $i++ ) {
		$row = array();
		$row[] = $i + 1;
		for( $j = 0; $j < 4; $j++ ) {
			if( $i < count( $times[$j] ) ) {
				$time = $times[$j][$i];
				$perc = round( $time->c * 100 / $total[$j] );
				$count = $time->c;
				$row[] = mkclientlink( $time->uid, $time->currentname ) . ' <em>' . $count . '</em><div style="background:#' . $todcolors[$j] . ';width:' . $perc . '%"></div>';
			} else {
				$row[] = '&nbsp;';
			}
		}
		$t->row( $row );
	}
	$t->output();

